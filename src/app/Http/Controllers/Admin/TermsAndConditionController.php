<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TermsAndConditionUpdateRequest;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\RedirectResponse;

class TermsAndConditionController extends Controller
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
        return to_route('admin.pages.index', ['tab' => 'terms']);
    }

    public function update(TermsAndConditionUpdateRequest $request): RedirectResponse
    {
        $this->staticPageService->updateTermsAndConditions($request->validated()['description']);

        return back()->with('success', '¡Términos y condiciones actualizados correctamente!');
    }
}
