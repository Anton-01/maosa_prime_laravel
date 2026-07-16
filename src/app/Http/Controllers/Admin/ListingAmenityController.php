<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListingAmenitiesUpdateRequest;
use App\Http\Requests\Admin\ListingAmenityCreateRequest;
use App\Models\Listing;
use App\Services\Admin\ListingAmenityService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ListingAmenityController extends Controller
{
    public function __construct(private readonly ListingAmenityService $listingAmenityService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    /**
     * Amenities management screen for a specific listing.
     */
    public function index(string $listingId): Response
    {
        $listing = Listing::select('id', 'title')->findOrFail($listingId);

        return Inertia::render('Admin/Listings/Amenities', [
            'listing' => ['id' => $listing->id, 'title' => $listing->title],
            ...$this->listingAmenityService->getForListing((int) $listingId),
            'urls' => [
                'update' => route('admin.listing.amenities.update', $listingId),
                'create' => route('admin.listing.amenities.create', $listingId),
                'listings' => route('admin.listing.index'),
            ],
        ]);
    }

    /**
     * Replace the listing's amenities with the submitted selection.
     */
    public function update(ListingAmenitiesUpdateRequest $request, string $listingId): RedirectResponse
    {
        Listing::findOrFail($listingId);

        $this->listingAmenityService->sync((int) $listingId, $request->validated()['amenities'] ?? []);

        return back()->with('success', '¡Servicios actualizados correctamente!');
    }

    /**
     * Create a new amenity and assign it to the listing.
     */
    public function createAmenity(ListingAmenityCreateRequest $request, string $listingId): RedirectResponse
    {
        Listing::findOrFail($listingId);

        $this->listingAmenityService->createAndAssign((int) $listingId, $request->validated());

        return back()->with('success', '¡Servicio creado y agregado correctamente!');
    }
}
