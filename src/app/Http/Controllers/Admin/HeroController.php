<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroUpdateRequest;
use App\Services\Admin\HeroService;
use Illuminate\Http\RedirectResponse;

class HeroController extends Controller
{
    public function __construct(private readonly HeroService $heroService)
    {
        $this->middleware(['permission:access management sections content']);
    }

    /**
     * Legacy URL kept for old bookmarks: the banner now lives in the
     * unified sections screen.
     */
    public function index(): RedirectResponse
    {
        return to_route('admin.sections.index', ['tab' => 'private-banner']);
    }

    public function indexPublic(): RedirectResponse
    {
        return to_route('admin.sections.index', ['tab' => 'public-banner']);
    }

    public function update(HeroUpdateRequest $request): RedirectResponse
    {
        $this->heroService->updateByType(HeroService::TYPE_PRIVATE, $request, $request->validated());

        return back()->with('success', '¡Banner actualizado correctamente!');
    }

    public function updatePublic(HeroUpdateRequest $request): RedirectResponse
    {
        $this->heroService->updateByType(HeroService::TYPE_PUBLIC, $request, $request->validated());

        return back()->with('success', '¡Banner público actualizado correctamente!');
    }
}
