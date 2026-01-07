<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Listing;
use App\Models\ListingAmenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingAmenityController extends Controller
{
    /**
     * Display the amenities management page for a specific listing
     */
    public function index(string $listingId): View
    {
        $listing = Listing::findOrFail($listingId);
        $amenities = Amenity::all();
        $listingAmenities = ListingAmenity::where('listing_id', $listingId)
            ->pluck('amenity_id')
            ->toArray();

        return view('admin.listing.amenities.index', compact('listing', 'amenities', 'listingAmenities'));
    }

    /**
     * Update the amenities for a specific listing
     */
    public function update(Request $request, string $listingId): RedirectResponse
    {
        $request->validate([
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id'
        ]);

        $listing = Listing::findOrFail($listingId);

        // Delete existing amenities
        ListingAmenity::where('listing_id', $listingId)->delete();

        // Add new amenities if any
        if ($request->has('amenities') && is_array($request->amenities)) {
            foreach ($request->amenities as $amenityId) {
                ListingAmenity::create([
                    'listing_id' => $listingId,
                    'amenity_id' => $amenityId
                ]);
            }
        }

        return redirect()
            ->route('admin.listing.amenities.index', $listingId)
            ->with('success', '¡Servicios actualizados correctamente!');
    }
}
