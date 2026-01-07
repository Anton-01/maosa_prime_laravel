<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserNavigationFlow;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user statistics index'])->only(['index']);
        $this->middleware(['permission:user statistics view'])->only(['show', 'sessions', 'activities']);
    }

    /**
     * Display statistics overview.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // General stats
        $totalSessions = UserSession::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $totalPageViews = PageVisit::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $totalActivities = UserActivity::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])->count();
        $uniqueUsers = UserSession::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->distinct('user_id')
            ->count('user_id');

        // Most visited pages
        $topPages = PageVisit::select('url', DB::raw('COUNT(*) as visits'))
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('url')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // User activity by day
        $activityByDay = PageVisit::select(
            DB::raw('DATE(visited_at) as date'),
            DB::raw('COUNT(*) as visits')
        )
            ->whereBetween('visited_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Device breakdown
        $deviceBreakdown = UserSession::select('device_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->get();

        // Browser breakdown
        $browserBreakdown = UserSession::select('browser', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top users by activity
        $topUsers = User::select('users.*')
            ->withCount(['pageVisits' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
            }])
            ->withCount(['sessions' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
            }])
            ->having('page_visits_count', '>', 0)
            ->orderByDesc('page_visits_count')
            ->limit(10)
            ->get();

        // Country breakdown
        $countryBreakdown = UserSession::select('country', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.statistics.index', compact(
            'dateFrom',
            'dateTo',
            'totalSessions',
            'totalPageViews',
            'totalActivities',
            'uniqueUsers',
            'topPages',
            'activityByDay',
            'deviceBreakdown',
            'browserBreakdown',
            'topUsers',
            'countryBreakdown'
        ));
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
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, ended_at)) as avg_duration')
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
