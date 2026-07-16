<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationStoreRequest;
use App\Http\Requests\Admin\LocationUpdateRequest;
use App\Services\Admin\LocationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function __construct(private readonly LocationService $locationService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Locations/Index', [
            'locations' => $this->locationService->list(),
            'urls' => [
                'base' => route('admin.location.index'),
            ],
        ]);
    }

    /**
     * Legacy URL kept for old bookmarks: create/edit now happen in a
     * modal inside the index screen.
     */
    public function create(): RedirectResponse
    {
        return to_route('admin.location.index');
    }

    public function edit(string $id): RedirectResponse
    {
        return to_route('admin.location.index');
    }

    public function store(LocationStoreRequest $request): RedirectResponse
    {
        $this->locationService->create($request->validated());

        return back()->with('success', '¡Ubicación creada correctamente!');
    }

    public function update(LocationUpdateRequest $request, string $id): RedirectResponse
    {
        $this->locationService->update((int) $id, $request->validated());

        return back()->with('success', '¡Ubicación actualizada correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->locationService->delete((int) $id);

        return back()->with('success', '¡Ubicación eliminada correctamente!');
    }
}
