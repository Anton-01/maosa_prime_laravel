<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AmenityStoreRequest;
use App\Http\Requests\Admin\AmenityUpdateRequest;
use App\Services\Admin\AmenityService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AmenityController extends Controller
{
    public function __construct(private readonly AmenityService $amenityService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Amenities/Index', [
            'amenities' => $this->amenityService->list(),
            'urls' => [
                'base' => route('admin.amenity.index'),
            ],
        ]);
    }

    /**
     * Legacy URL kept for old bookmarks: create/edit now happen in a
     * modal inside the index screen.
     */
    public function create(): RedirectResponse
    {
        return to_route('admin.amenity.index');
    }

    public function edit(string $id): RedirectResponse
    {
        return to_route('admin.amenity.index');
    }

    public function store(AmenityStoreRequest $request): RedirectResponse
    {
        $this->amenityService->create($request->validated());

        return back()->with('success', '¡Servicio creado correctamente!');
    }

    public function update(AmenityUpdateRequest $request, string $id): RedirectResponse
    {
        $this->amenityService->update((int) $id, $request->validated());

        return back()->with('success', '¡Servicio actualizado correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->amenityService->delete((int) $id);

        return back()->with('success', '¡Servicio eliminado correctamente!');
    }
}
