<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Blog;
use App\Models\Category;
use App\Models\FuelTerminal;
use App\Models\Listing;
use App\Models\Location;
use App\Models\PageVisit;
use App\Models\User;
use App\Models\UserPriceList;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller {
    function index() : View {

        $stats = Cache::remember('admin_dashboard_stats', now()->addMinutes(5), function () {
            $lastWeek = Carbon::now()->subDays(7);

            // Datos para gráfico de actividad (últimos 7 días)
            $activityData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $activityData[] = [
                    'date'     => $date->format('d/m'),
                    'visits'   => PageVisit::whereDate('created_at', $date->toDateString())->count(),
                    'sessions' => UserSession::whereDate('created_at', $date->toDateString())->count(),
                ];
            }

            return [
                'totalListingCount'      => Listing::count(),
                'listingCategoryCount'   => Category::count(),
                'locationCount'          => Location::count(),
                'adminCount'             => User::where('user_type', 'admin')->count(),
                'usersCount'             => User::whereNot('user_type', 'admin')->count(),
                'verifiedListingsCount'  => Listing::where('is_verified', 1)->count(),
                'activeListingsCount'    => Listing::where('status', 1)->count(),
                'fuelTerminalsCount'     => FuelTerminal::where('status', 1)->count(),
                'amenitiesCount'         => Amenity::count(),
                'blogsCount'             => Blog::count(),
                'activePriceListsCount'  => UserPriceList::where('is_active', 1)->count(),
                'usersWithPriceAccess'   => User::where('can_view_price_table', 1)->whereNot('user_type', 'admin')->count(),
                'sessionsLastWeek'       => UserSession::where('created_at', '>=', $lastWeek)->count(),
                'pageVisitsLastWeek'     => PageVisit::where('created_at', '>=', $lastWeek)->count(),
                'newUsersLastWeek'       => User::whereNot('user_type', 'admin')->where('created_at', '>=', $lastWeek)->count(),
                'latestUsers'            => User::whereNot('user_type', 'admin')->orderBy('created_at', 'desc')->take(5)->get(),
                'latestListings'         => Listing::with(['category', 'location'])->orderBy('created_at', 'desc')->take(5)->get(),
                'activityData'           => $activityData,
                'listingsByCategory'     => Category::withCount('listings')->orderBy('listings_count', 'desc')->take(5)->get(),
            ];

        });

        return view('admin.dashboard.index', $stats);
    }
}
