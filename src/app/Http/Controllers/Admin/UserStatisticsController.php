<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserStatisticsController extends Controller
{
    public function __construct(private readonly StatisticsService $statisticsService)
    {
        $this->middleware(['permission:user statistics index'])->only(['index', 'breakdownData']);
        $this->middleware(['permission:user statistics view'])->only(['show', 'sessions', 'sessionDetail', 'activities']);
    }

    /**
     * Statistics overview: KPI metrics plus server-side breakdown tables.
     */
    public function index(Request $request): Response
    {
        [$dateFrom, $dateTo] = $this->statisticsService->range($request);

        return Inertia::render('Admin/Statistics/Index', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'metrics' => $this->statisticsService->overviewMetrics($dateFrom, $dateTo),
            'urls' => [
                'base' => route('admin.statistics.index'),
                'dataBase' => url('admin/statistics/data'),
                'exportBase' => url('admin/statistics/export'),
                'userBase' => url('admin/statistics/user'),
            ],
        ]);
    }

    /**
     * JSON data source for the overview tables (useDataTable contract).
     * Types: devices, browsers, countries, pages, active-users.
     */
    public function breakdownData(Request $request, string $type): JsonResponse
    {
        return response()->json($this->statisticsService->breakdownData($type, $request));
    }

    /**
     * Statistics detail for a specific user.
     */
    public function show(Request $request, int $userId): Response
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        return Inertia::render('Admin/Statistics/UserDetail', [
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            ...$this->statisticsService->userDetail($userId, $dateFrom, $dateTo),
            'urls' => [
                'base' => route('admin.statistics.index'),
                'self' => route('admin.statistics.show', $userId),
                'sessions' => route('admin.statistics.sessions', $userId),
                'activities' => route('admin.statistics.activities', $userId),
                'sessionDetailBase' => url('admin/statistics/session'),
            ],
        ]);
    }

    /**
     * Paginated sessions of a user.
     */
    public function sessions(Request $request, int $userId): Response
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        return Inertia::render('Admin/Statistics/Sessions', [
            'user' => ['id' => $user->id, 'name' => $user->name],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sessions' => $this->statisticsService->userSessions(
                $userId,
                $dateFrom,
                $dateTo,
                max(1, $request->integer('page', 1)),
            ),
            'urls' => [
                'self' => route('admin.statistics.sessions', $userId),
                'userDetail' => route('admin.statistics.show', $userId),
                'sessionDetailBase' => url('admin/statistics/session'),
            ],
        ]);
    }

    /**
     * A session with its page visits and activities.
     */
    public function sessionDetail(int $sessionId): Response
    {
        $detail = $this->statisticsService->sessionDetail($sessionId);

        return Inertia::render('Admin/Statistics/SessionDetail', [
            ...$detail,
            'urls' => [
                'userSessions' => route('admin.statistics.sessions', $detail['session']['userId']),
                'userDetail' => route('admin.statistics.show', $detail['session']['userId']),
            ],
        ]);
    }

    /**
     * Paginated activities of a user, filterable by type.
     */
    public function activities(Request $request, int $userId): Response
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $activityType = $request->get('activity_type');

        return Inertia::render('Admin/Statistics/Activities', [
            'user' => ['id' => $user->id, 'name' => $user->name],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'activityType' => $activityType,
            'activities' => $this->statisticsService->userActivities(
                $userId,
                $dateFrom,
                $dateTo,
                $activityType,
                max(1, $request->integer('page', 1)),
            ),
            'urls' => [
                'self' => route('admin.statistics.activities', $userId),
                'userDetail' => route('admin.statistics.show', $userId),
            ],
        ]);
    }
}
