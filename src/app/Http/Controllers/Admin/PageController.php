<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Unified "Páginas" screen: about us, contact, privacy policy and
 * terms & conditions live together in one tabbed Inertia page.
 */
class PageController extends Controller
{
    public function __construct(private readonly StaticPageService $staticPageService)
    {
        $this->middleware(['permission:access management pages']);
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'initialTab' => $request->query('tab', 'about'),
            ...$this->staticPageService->getPagesData(),
            'urls' => [
                'aboutUpdate' => route('admin.about-us.update'),
                'contactUpdate' => route('admin.contact.update'),
                'privacyPolicyUpdate' => route('admin.privacy-policy.update'),
                'termsUpdate' => route('admin.terms-and-condition.update'),
            ],
        ]);
    }
}
