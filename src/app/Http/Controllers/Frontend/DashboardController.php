<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('User/Profile', [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'about' => $user->about,
                'avatar' => $user->avatar,
                'banner' => $user->banner,
                'user_type' => $user->user_type,
                'can_view_price_table' => $user->canViewPriceTable(),
            ],
        ]);
    }
}
