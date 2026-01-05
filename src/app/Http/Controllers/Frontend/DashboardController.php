<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Auth;

class DashboardController extends Controller
{
    function index() : View {
        $user = Auth::user();
        return view('frontend.dashboard.profile.index', compact('user'));
    }
}
