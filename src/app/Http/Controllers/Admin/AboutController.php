<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutUsUpdateRequest;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\RedirectResponse;

class AboutController extends Controller
{
    public function __construct(private readonly StaticPageService $staticPageService)
    {
        $this->middleware(['permission:access management pages']);
    }

    /**
     * Legacy URL kept for old bookmarks: the page now lives in the
     * unified pages screen.
     */
    public function index(): RedirectResponse
    {
        return to_route('admin.pages.index', ['tab' => 'about']);
    }

    public function update(AboutUsUpdateRequest $request): RedirectResponse
    {
        $this->staticPageService->updateAbout($request, $request->validated());

        return back()->with('success', '¡Página "Sobre nosotros" actualizada correctamente!');
    }
}
