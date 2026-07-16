<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactUpdateRequest;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
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
        return to_route('admin.pages.index', ['tab' => 'contact']);
    }

    public function update(ContactUpdateRequest $request): RedirectResponse
    {
        $this->staticPageService->updateContact($request->validated());

        return back()->with('success', '¡Página de contacto actualizada correctamente!');
    }
}
