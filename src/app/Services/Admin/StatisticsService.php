<?php

namespace App\Services\Admin;

use App\Models\PageVisit;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserNavigationFlow;
use App\Models\UserSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Column used to group each breakdown type.
     *
     * @var array<string, array{model: class-string, column: string}>
     */
    private const BREAKDOWNS = [
        'devices' => ['model' => UserSession::class, 'column' => 'device_type'],
        'browsers' => ['model' => UserSession::class, 'column' => 'browser'],
        'countries' => ['model' => UserSession::class, 'column' => 'country'],
        'pages' => ['model' => PageVisit::class, 'column' => 'url'],
    ];

    /**
     * Scalar KPI metrics for the overview screen.
     *
     * @return array<string, int>
     */
    public function overviewMetrics(string $dateFrom, string $dateTo): array
    {
        $range = [$dateFrom, $dateTo . ' 23:59:59'];

        return [
            'totalSessions' => UserSession::whereBetween('created_at', $range)->count(),
            'totalPageViews' => PageVisit::whereBetween('created_at', $range)->count(),
            'totalActivities' => UserActivity::whereBetween('created_at', $range)->count(),
            'uniqueUsers' => UserSession::whereBetween('created_at', $range)
                ->distinct('user_id')
                ->count('user_id'),
        ];
    }

    /**
     * Server-side data for the breakdown tables (devices, browsers,
     * countries, pages) and the active users table. Follows the
     * useDataTable contract.
     *
     * @return array<string, mixed>
     */
    public function breakdownData(string $type, Request $request): array
    {
        if ($type === 'active-users') {
            return $this->activeUsersData($request);
        }

        abort_unless(isset(self::BREAKDOWNS[$type]), 404);

        ['model' => $model, 'column' => $column] = self::BREAKDOWNS[$type];
        [$dateFrom, $dateTo] = $this->range($request);

        $query = $model::query()
            ->selectRaw("{$column} as label, COUNT(*) as total")
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull($column)
            ->groupBy($column);

        if ($search = $request->string('search')->toString()) {
            $query->where($column, 'like', "%{$search}%");
        }

        $sortField = $request->input('sort_field');
        $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField === 'label' ? 'label' : 'total', $sortOrder);

        $page = $query->paginate(min(100, max(1, $request->integer('per_page', 10))));

        return [
            'data' => collect($page->items())
                ->map(fn ($row) => ['label' => $row->label, 'total' => (int) $row->total])
                ->all(),
            'total' => $page->total(),
        ];
    }

    /**
     * Server-side data for the "most active users" table.
     *
     * @return array<string, mixed>
     */
    private function activeUsersData(Request $request): array
    {
        [$dateFrom, $dateTo] = $this->range($request);

        // SR-016: las 3 IPs de sesión más frecuentes por usuario dentro del
        // rango vigente. Subconsulta correlacionada, por lo que solo se evalúa
        // sobre las filas de la página visible y no penaliza la carga del
        // panel. Se agrega aquí y no en activeUsersBaseQuery() para dejar la
        // exportación a Excel sin cambios.
        $query = $this->activeUsersBaseQuery($dateFrom, $dateTo)
            ->selectRaw(
                <<<'SQL'
                (
                    SELECT string_agg(top_ips.ip_address, ',' ORDER BY top_ips.hits DESC)
                    FROM (
                        SELECT s.ip_address, COUNT(*) AS hits
                        FROM user_sessions s
                        WHERE s.user_id = users.id
                          AND s.ip_address IS NOT NULL
                          AND s.created_at BETWEEN ? AND ?
                        GROUP BY s.ip_address
                        ORDER BY hits DESC
                        LIMIT 3
                    ) top_ips
                ) AS top_session_ips
                SQL,
                [$dateFrom, $dateTo . ' 23:59:59']
            );

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $sortable = [
            'name' => 'users.name',
            'email' => 'users.email',
            'pageVisits' => 'page_visits_count',
            'sessions' => 'sessions_count',
            'lastSessionAt' => 'last_session_at',
        ];

        $sortField = $sortable[$request->input('sort_field')] ?? 'page_visits_count';
        $query->orderBy($sortField, $request->input('sort_order') === 'asc' ? 'asc' : 'desc');

        $page = $query->paginate(min(100, max(1, $request->integer('per_page', 10))));

        return [
            'data' => collect($page->items())->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'pageVisits' => (int) $user->page_visits_count,
                'sessions' => (int) $user->sessions_count,
                'lastSessionAt' => $user->last_session_at
                    ? Carbon::parse($user->last_session_at)->format('d/m/Y H:i')
                    : null,
                'lastSessionLocation' => $user->last_session_location,
                'topSessionIps' => $user->top_session_ips
                    ? explode(',', $user->top_session_ips)
                    : [],
            ])->all(),
            'total' => $page->total(),
        ];
    }

    /**
     * Everything the per-user statistics screen needs.
     *
     * @return array<string, mixed>
     */
    public function userDetail(int $userId, string $dateFrom, string $dateTo): array
    {
        $range = [$dateFrom, $dateTo . ' 23:59:59'];

        $avgSessionDuration = UserSession::forUser($userId)
            ->whereBetween('created_at', $range)
            ->whereNotNull('ended_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (ended_at - started_at))) as avg_duration')
            ->value('avg_duration');

        return [
            'metrics' => [
                'totalSessions' => UserSession::forUser($userId)->whereBetween('created_at', $range)->count(),
                'totalPageViews' => PageVisit::forUser($userId)->whereBetween('created_at', $range)->count(),
                'totalActivities' => UserActivity::forUser($userId)->whereBetween('created_at', $range)->count(),
                'avgSessionDurationMinutes' => $avgSessionDuration
                    ? round($avgSessionDuration / 60, 1)
                    : null,
            ],
            'topPages' => PageVisit::select('url', 'page_title', DB::raw('COUNT(*) as visits'))
                ->forUser($userId)
                ->whereBetween('created_at', $range)
                ->groupBy('url', 'page_title')
                ->orderByDesc('visits')
                ->limit(10)
                ->get()
                ->map(fn ($row) => [
                    'url' => $row->url,
                    'pageTitle' => $row->page_title,
                    'visits' => (int) $row->visits,
                ]),
            'recentSessions' => UserSession::forUser($userId)
                ->whereBetween('created_at', $range)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(fn (UserSession $session) => $this->sessionSummary($session)),
            'activityTypes' => UserActivity::select('activity_type', DB::raw('COUNT(*) as count'))
                ->forUser($userId)
                ->whereBetween('created_at', $range)
                ->groupBy('activity_type')
                ->orderByDesc('count')
                ->get()
                ->map(fn ($row) => ['type' => $row->activity_type, 'count' => (int) $row->count]),
            'activityByDay' => PageVisit::select(
                DB::raw('DATE(visited_at) as date'),
                DB::raw('COUNT(*) as visits'),
            )
                ->forUser($userId)
                ->whereBetween('visited_at', $range)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => Carbon::parse($row->date)->format('d/m'),
                    'visits' => (int) $row->visits,
                ]),
            'navigationFlows' => UserNavigationFlow::select('from_url', 'to_url', DB::raw('COUNT(*) as count'))
                ->forUser($userId)
                ->whereBetween('created_at', $range)
                ->groupBy('from_url', 'to_url')
                ->orderByDesc('count')
                ->limit(15)
                ->get()
                ->map(fn ($row) => [
                    'fromUrl' => $row->from_url,
                    'toUrl' => $row->to_url,
                    'count' => (int) $row->count,
                ]),
        ];
    }

    /**
     * Paginated sessions of a user.
     *
     * @return array<string, mixed>
     */
    public function userSessions(int $userId, string $dateFrom, string $dateTo, int $page): array
    {
        $sessions = UserSession::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->withCount('pageVisits')
            ->withCount('activities')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'page', $page);

        return [
            'data' => collect($sessions->items())->map(fn (UserSession $session) => [
                ...$this->sessionSummary($session),
                'pageVisitsCount' => (int) $session->page_visits_count,
                'activitiesCount' => (int) $session->activities_count,
            ])->all(),
            'total' => $sessions->total(),
            'perPage' => $sessions->perPage(),
            'page' => $sessions->currentPage(),
        ];
    }

    /**
     * A session with its ordered visits and activities.
     *
     * @return array<string, mixed>
     */
    public function sessionDetail(int $sessionId): array
    {
        $session = UserSession::with('user:id,name,email')->findOrFail($sessionId);

        return [
            'session' => [
                ...$this->sessionSummary($session),
                'userId' => $session->user_id,
                'userName' => $session->user->name ?? 'N/A',
                'userEmail' => $session->user->email ?? null,
                'ipAddress' => $session->ip_address,
                'platform' => trim(($session->platform ?? '') . ' ' . ($session->platform_version ?? '')) ?: null,
            ],
            'pageVisits' => PageVisit::where('user_session_id', $sessionId)
                ->orderBy('visited_at')
                ->get()
                ->map(fn (PageVisit $visit) => [
                    'id' => $visit->id,
                    'url' => $visit->url,
                    'pageTitle' => $visit->page_title,
                    'visitedAt' => $visit->visited_at?->format('d/m/Y H:i:s'),
                    'timeOnPage' => $visit->time_on_page,
                ]),
            'activities' => UserActivity::where('user_session_id', $sessionId)
                ->orderBy('created_at')
                ->get()
                ->map(fn (UserActivity $activity) => [
                    'id' => $activity->id,
                    'type' => $activity->activity_type,
                    'description' => $activity->activity_description,
                    'url' => $activity->url,
                    'createdAt' => $activity->created_at?->format('d/m/Y H:i:s'),
                ]),
        ];
    }

    /**
     * Paginated activities of a user, optionally filtered by type.
     *
     * @return array<string, mixed>
     */
    public function userActivities(int $userId, string $dateFrom, string $dateTo, ?string $activityType, int $page): array
    {
        $query = UserActivity::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        $activities = $query->orderByDesc('created_at')->paginate(50, ['*'], 'page', $page);

        return [
            'data' => collect($activities->items())->map(fn (UserActivity $activity) => [
                'id' => $activity->id,
                'type' => $activity->activity_type,
                'description' => $activity->activity_description,
                'url' => $activity->url,
                'createdAt' => $activity->created_at?->format('d/m/Y H:i:s'),
            ])->all(),
            'total' => $activities->total(),
            'perPage' => $activities->perPage(),
            'page' => $activities->currentPage(),
            'availableTypes' => UserActivity::forUser($userId)
                ->distinct('activity_type')
                ->pluck('activity_type')
                ->values(),
        ];
    }

    /**
     * Users with page visits in range plus their visit/session counts and
     * last-session data. Shared with the Excel export.
     */
    public function activeUsersBaseQuery(string $dateFrom, string $dateTo): Builder
    {
        $range = [$dateFrom, $dateTo . ' 23:59:59'];

        return User::query()
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('last_session.last_session_at')
            ->selectRaw("NULLIF(CONCAT_WS(', ', last_session.city, last_session.region, last_session.country), '') as last_session_location")
            ->withLastSession()
            ->withCount([
                'pageVisits' => fn ($query) => $query->whereBetween('created_at', $range),
                'sessions' => fn ($query) => $query->whereBetween('created_at', $range),
            ])
            ->whereHas('pageVisits', fn ($query) => $query->whereBetween('created_at', $range));
    }

    /**
     * Shared summary shape for a session row.
     *
     * @return array<string, mixed>
     */
    private function sessionSummary(UserSession $session): array
    {
        $duration = null;

        if ($session->started_at && $session->ended_at) {
            $duration = round($session->started_at->diffInSeconds($session->ended_at) / 60, 1);
        }

        return [
            'id' => $session->id,
            'deviceType' => $session->device_type,
            'browser' => $session->browser,
            'location' => collect([$session->city, $session->region, $session->country])
                ->filter()
                ->implode(', ') ?: null,
            'startedAt' => $session->started_at?->format('d/m/Y H:i'),
            'endedAt' => $session->ended_at?->format('d/m/Y H:i'),
            'durationMinutes' => $duration,
            'isActive' => (bool) $session->is_active,
        ];
    }

    /**
     * Date range from the request (supports both direct params and the
     * useDataTable extraParams style).
     *
     * @return array{0: string, 1: string}
     */
    public function range(Request $request): array
    {
        return [
            $request->input('date_from', now()->subDays(7)->format('Y-m-d')),
            $request->input('date_to', now()->format('Y-m-d')),
        ];
    }
}
