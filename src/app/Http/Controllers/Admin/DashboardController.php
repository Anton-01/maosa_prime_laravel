<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    function index() : View {
        $totalListingCount = Listing::count();
        $listingCategoryCount = Category::count();
        $locationCount = Location::count();
        $adminCount = User::where('user_type', 'admin')->count();
        $usersCount = User::whereNot('user_type', 'admin')->count();

        return view('admin.dashboard.index', compact(
            'totalListingCount',
            'listingCategoryCount',
            'locationCount',
            'adminCount',
            'usersCount',
        ));
    }
}
