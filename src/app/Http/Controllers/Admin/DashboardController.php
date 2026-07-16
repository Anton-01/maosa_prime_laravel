<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardStatsService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $dashboardStatsService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard/Index', [
            ...$this->dashboardStatsService->summary(),
            'urls' => [
                'createListing' => route('admin.listing.create'),
                'createUser' => route('admin.role-user.create'),
                'categories' => route('admin.category.index'),
                'statistics' => route('admin.statistics.index'),
                'allUsers' => route('admin.role-user.index'),
                'allListings' => route('admin.listing.index'),
            ],
        ]);
    }
}
