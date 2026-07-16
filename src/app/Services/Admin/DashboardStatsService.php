<?php

namespace App\Services\Admin;

use App\Models\Amenity;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\PageVisit;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardStatsService
{
    private const CACHE_KEY = 'admin_dashboard_stats';

    private const CACHE_TTL_MINUTES = 5;

    /**
     * Aggregate every metric the admin dashboard needs, grouped by segment.
     *
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_TTL_MINUTES), function () {
            return [
                'providerStats' => $this->providerStats(),
                'catalogStats' => $this->catalogStats(),
                'userStats' => $this->userStats(),
                'weeklyActivity' => $this->weeklyActivity(),
                'activityChart' => $this->activityChart(),
                'listingsByCategory' => $this->listingsByCategory(),
                'latestUsers' => $this->latestUsers(),
                'latestListings' => $this->latestListings(),
            ];
        });
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, int>
     */
    private function providerStats(): array
    {
        return [
            'total' => Listing::count(),
            'verified' => Listing::where('is_verified', 1)->count(),
            'active' => Listing::where('status', 1)->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function catalogStats(): array
    {
        return [
            'categories' => Category::count(),
            'locations' => Location::count(),
            'amenities' => Amenity::count(),
            'blogPosts' => Blog::count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function userStats(): array
    {
        $lastWeek = Carbon::now()->subDays(7);

        return [
            'total' => User::whereNot('user_type', 'admin')->count(),
            'admins' => User::where('user_type', 'admin')->count(),
            'withPriceAccess' => User::where('can_view_price_table', 1)->whereNot('user_type', 'admin')->count(),
            'newLastWeek' => User::whereNot('user_type', 'admin')->where('created_at', '>=', $lastWeek)->count(),
        ];
    }

    /**
     * Current week totals plus the previous week for trend comparison.
     *
     * @return array<string, mixed>
     */
    private function weeklyActivity(): array
    {
        $lastWeek = Carbon::now()->subDays(7);
        $previousWeekStart = Carbon::now()->subDays(14);

        return [
            'sessions' => UserSession::where('created_at', '>=', $lastWeek)->count(),
            'visits' => PageVisit::where('created_at', '>=', $lastWeek)->count(),
            'newUsers' => User::whereNot('user_type', 'admin')->where('created_at', '>=', $lastWeek)->count(),
            'previous' => [
                'sessions' => UserSession::whereBetween('created_at', [$previousWeekStart, $lastWeek])->count(),
                'visits' => PageVisit::whereBetween('created_at', [$previousWeekStart, $lastWeek])->count(),
                'newUsers' => User::whereNot('user_type', 'admin')
                    ->whereBetween('created_at', [$previousWeekStart, $lastWeek])
                    ->count(),
            ],
        ];
    }

    /**
     * Daily visits and sessions for the last 7 days (line chart).
     *
     * @return array<int, array<string, mixed>>
     */
    private function activityChart(): array
    {
        $days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $days[] = [
                'date' => $date->format('d/m'),
                'visits' => PageVisit::whereDate('created_at', $date->toDateString())->count(),
                'sessions' => UserSession::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        return $days;
    }

    /**
     * Top 5 categories by listing count (pie chart).
     *
     * @return array<int, array<string, mixed>>
     */
    private function listingsByCategory(): array
    {
        return Category::withCount('listings')
            ->orderByDesc('listings_count')
            ->take(5)
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'count' => $category->listings_count,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function latestUsers(): array
    {
        return User::whereNot('user_type', 'admin')
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'createdAt' => $user->created_at?->format('d/m/Y'),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function latestListings(): array
    {
        return Listing::with(['category:id,name', 'location:id,name'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn (Listing $listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'category' => $listing->category->name ?? 'N/A',
                'location' => $listing->location->name ?? 'N/A',
            ])
            ->all();
    }
}
