<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListingStoreRequest;
use App\Http\Requests\Admin\ListingUpdateRequest;
use App\Services\Admin\ListingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    public function __construct(private readonly ListingService $listingService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Listings/Index', [
            'listings' => $this->listingService->list(),
            'urls' => [
                'base' => route('admin.listing.index'),
                'create' => route('admin.listing.create'),
                'schedulesBase' => url('admin/listing-schedule'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Listings/Form', [
            'listing' => null,
            'options' => $this->listingService->getFormOptions(),
            'urls' => [
                'base' => route('admin.listing.index'),
            ],
        ]);
    }

    public function store(ListingStoreRequest $request): RedirectResponse
    {
        $this->listingService->create($request, $request->validated());

        return to_route('admin.listing.index')->with('success', '¡Proveedor creado correctamente!');
    }

    public function edit(string $id): Response
    {
        return Inertia::render('Admin/Listings/Form', [
            'listing' => $this->listingService->getForEdit((int) $id),
            'options' => $this->listingService->getFormOptions(),
            'urls' => [
                'base' => route('admin.listing.index'),
            ],
        ]);
    }

    public function update(ListingUpdateRequest $request, string $id): RedirectResponse
    {
        $this->listingService->update($request, (int) $id, $request->validated());

        return to_route('admin.listing.index')->with('success', '¡Proveedor actualizado correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->listingService->delete((int) $id);

        return back()->with('success', '¡Proveedor eliminado correctamente!');
    }
}
