<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrivacyPolicyUpdateRequest;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\RedirectResponse;

class PrivacyPolicyController extends Controller
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
        return to_route('admin.pages.index', ['tab' => 'privacy-policy']);
    }

    public function update(PrivacyPolicyUpdateRequest $request): RedirectResponse
    {
        $this->staticPageService->updatePrivacyPolicy($request->validated()['description']);

        return back()->with('success', '¡Política de privacidad actualizada correctamente!');
    }
}
