<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Listing;
use App\Models\ListingAmenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Str;

class ListingAmenityController extends Controller
{
    /**
     * Display the amenities management page for a specific listing
     */
    public function index(string $listingId): View
    {
        $listing = Listing::findOrFail($listingId);

        // Get listing amenity IDs
        $listingAmenityIds  = ListingAmenity::where('listing_id', $listingId)
            ->pluck('amenity_id')
            ->toArray();

        // Get assigned amenities with full details
        $assignedAmenities = Amenity::whereIn('id', $listingAmenityIds)->get();

        // Get available amenities (not assigned)
        $availableAmenities = Amenity::whereNotIn('id', $listingAmenityIds)->get();

        return view('admin.listing.amenities.index', compact('listing', 'assignedAmenities', 'availableAmenities'));
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

        return redirect()->route('admin.listing.amenities.index', $listingId)
            ->with('success', '¡Servicios actualizados correctamente!');
    }

    /**
     * Add a single amenity to listing
     */
    public function addAmenity(Request $request, string $listingId)
    {
        $request->validate([
            'amenity_id' => 'required|exists:amenities,id'
        ]);

        // Check if already exists
        $exists = ListingAmenity::where('listing_id', $listingId)
            ->where('amenity_id', $request->amenity_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este servicio ya está asignado'
            ], 400);
        }

        ListingAmenity::create([
            'listing_id' => $listingId,
            'amenity_id' => $request->amenity_id
        ]);

        $amenity = Amenity::find($request->amenity_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Servicio agregado correctamente',
            'amenity' => $amenity
        ]);
    }

    /**
     * Remove a single amenity from listing
     */
    public function removeAmenity(string $listingId, string $amenityId)
    {
        ListingAmenity::where('listing_id', $listingId)
            ->where('amenity_id', $amenityId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Servicio eliminado correctamente'
        ]);
    }

    /**
     * Create a new amenity and optionally add it to listing
     */
    public function createAmenity(Request $request, string $listingId)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
            'icon' => 'required|string|max:255'
        ]);

        $amenity = Amenity::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'slug' => Str::slug($request->name),
            'status' => 1,
        ]);

        // Automatically add to listing
        ListingAmenity::create([
            'listing_id' => $listingId,
            'amenity_id' => $amenity->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Servicio creado y agregado correctamente',
            'amenity' => $amenity
        ]);
    }
}
