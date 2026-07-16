<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OurFeatureCreateRequest;
use App\Http\Requests\Admin\OurFeatureUpdateRequest;
use App\Services\Admin\OurFeatureService;
use Illuminate\Http\RedirectResponse;

class OurFeatureController extends Controller
{
    public function __construct(private readonly OurFeatureService $ourFeatureService)
    {
        $this->middleware(['permission:access management sections content']);
    }

    /**
     * Legacy URLs kept for old bookmarks: features are now managed from
     * the unified sections screen (create/edit happen in a modal there).
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToFeaturesTab();
    }

    public function create(): RedirectResponse
    {
        return $this->redirectToFeaturesTab();
    }

    public function edit(string $id): RedirectResponse
    {
        return $this->redirectToFeaturesTab();
    }

    public function store(OurFeatureCreateRequest $request): RedirectResponse
    {
        $this->ourFeatureService->create($request->validated());

        return back()->with('success', '¡Función creada correctamente!');
    }

    public function update(OurFeatureUpdateRequest $request, string $id): RedirectResponse
    {
        $this->ourFeatureService->update((int) $id, $request->validated());

        return back()->with('success', '¡Función actualizada correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->ourFeatureService->delete((int) $id);

        return back()->with('success', '¡Función eliminada correctamente!');
    }

    private function redirectToFeaturesTab(): RedirectResponse
    {
        return to_route('admin.sections.index', ['tab' => 'features']);
    }
}
