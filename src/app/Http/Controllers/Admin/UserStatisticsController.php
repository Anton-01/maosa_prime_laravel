<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ActiveUsersDataTable;
use App\DataTables\Statistics\BrowserStatsDataTable;
use App\DataTables\Statistics\CountryStatsDataTable;
use App\DataTables\Statistics\DeviceStatsDataTable;
use App\DataTables\Statistics\TopPageStatsDataTable;
use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserNavigationFlow;
use App\Models\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user statistics index'])->only(['index', 'breakdownData']);
        $this->middleware(['permission:user statistics view'])->only(['show', 'sessions', 'activities']);
    }

    /**
     * Display statistics overview.
     */
    public function index(ActiveUsersDataTable $dataTable, Request $request): View | JsonResponse
    {
        // Peticiones ajax del DataTable "Usuarios más activos": solo devolver JSON
        if ($request->ajax()) {
            return $dataTable->render('admin.statistics.index');
        }

        // Por defecto: últimos 7 días (NOW() - 7 días → NOW())
        $dateFrom = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Métricas escalares de las cards (Tab "Métricas Generales")
        $totalSessions = UserSession::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $totalPageViews = PageVisit::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $totalActivities = UserActivity::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $uniqueUsers = UserSession::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->distinct('user_id')
            ->count('user_id');

        // Tablas de desglose: cada una es un DataTable server-side propio.
        // Se pasan sus html builders para renderizar tabla + scripts en la vista.
        $devicesTable   = app(DeviceStatsDataTable::class)->html();
        $browsersTable  = app(BrowserStatsDataTable::class)->html();
        $countriesTable = app(CountryStatsDataTable::class)->html();
        $pagesTable     = app(TopPageStatsDataTable::class)->html();

        return $dataTable->render('admin.statistics.index', compact(
            'dateFrom',
            'dateTo',
            'totalSessions',
            'totalPageViews',
            'totalActivities',
            'uniqueUsers',
            'devicesTable',
            'browsersTable',
            'countriesTable',
            'pagesTable'
        ));
    }

    /**
     * Ajax endpoint que sirve los datos de las tablas de desglose del panel.
     */
    public function breakdownData(string $type): JsonResponse
    {
        $dataTable = match ($type) {
            'browsers'  => app(BrowserStatsDataTable::class),
            'countries' => app(CountryStatsDataTable::class),
            'devices'   => app(DeviceStatsDataTable::class),
            'pages'     => app(TopPageStatsDataTable::class),
            default     => abort(404),
        };

        return $dataTable->ajax();
    }

    /**
     * Display statistics for a specific user.
     */
    public function show(Request $request, int $userId): View
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // User stats
        $totalSessions = UserSession::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->count();

        $totalPageViews = PageVisit::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->count();

        $totalActivities = UserActivity::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->count();

        // Average session duration
        $avgSessionDuration = UserSession::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull('ended_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (ended_at - started_at))) as avg_duration')
            ->value('avg_duration');

        // Most visited pages by this user
        $topPages = PageVisit::select('url', 'page_title', DB::raw('COUNT(*) as visits'))
            ->forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('url', 'page_title')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // Recent sessions
        $recentSessions = UserSession::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Activity types breakdown
        $activityTypes = UserActivity::select('activity_type', DB::raw('COUNT(*) as count'))
            ->forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('activity_type')
            ->orderByDesc('count')
            ->get();

        // Activity by day
        $activityByDay = PageVisit::select(
            DB::raw('DATE(visited_at) as date'),
            DB::raw('COUNT(*) as visits')
        )
            ->forUser($userId)
            ->whereBetween('visited_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Navigation flow (top paths)
        $navigationFlows = UserNavigationFlow::select('from_url', 'to_url', DB::raw('COUNT(*) as count'))
            ->forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('from_url', 'to_url')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        return view('admin.statistics.show', compact(
            'user',
            'dateFrom',
            'dateTo',
            'totalSessions',
            'totalPageViews',
            'totalActivities',
            'avgSessionDuration',
            'topPages',
            'recentSessions',
            'activityTypes',
            'activityByDay',
            'navigationFlows'
        ));
    }

    /**
     * Display session details.
     */
    public function sessions(Request $request, int $userId): View
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $sessions = UserSession::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->withCount('pageVisits')
            ->withCount('activities')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.statistics.sessions', compact('user', 'sessions', 'dateFrom', 'dateTo'));
    }

    /**
     * Display session detail with page visits.
     */
    public function sessionDetail(int $sessionId): View
    {
        $session = UserSession::with(['user', 'pageVisits', 'activities'])
            ->findOrFail($sessionId);

        $pageVisits = PageVisit::where('user_session_id', $sessionId)
            ->orderBy('visited_at')
            ->get();

        $activities = UserActivity::where('user_session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        return view('admin.statistics.session-detail', compact('session', 'pageVisits', 'activities'));
    }

    /**
     * Display user activities.
     */
    public function activities(Request $request, int $userId): View
    {
        $user = User::findOrFail($userId);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $activityType = $request->get('activity_type');

        $query = UserActivity::forUser($userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        $activities = $query->orderByDesc('created_at')->paginate(50);

        $activityTypes = UserActivity::forUser($userId)
            ->distinct('activity_type')
            ->pluck('activity_type');

        return view('admin.statistics.activities', compact(
            'user',
            'activities',
            'activityTypes',
            'activityType',
            'dateFrom',
            'dateTo'
        ));
    }
}
