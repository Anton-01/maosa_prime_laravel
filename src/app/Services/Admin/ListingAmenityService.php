<?php

namespace App\Services\Admin;

use App\Models\Amenity;
use App\Models\ListingAmenity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ListingAmenityService
{
    /**
     * Every amenity plus the ids currently assigned to the listing.
     *
     * @return array<string, mixed>
     */
    public function getForListing(int $listingId): array
    {
        return [
            'amenities' => Amenity::orderBy('name')
                ->get()
                ->map(fn (Amenity $amenity) => [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                ]),
            'assignedAmenityIds' => ListingAmenity::where('listing_id', $listingId)
                ->pluck('amenity_id')
                ->values(),
        ];
    }

    /**
     * Replace the listing's amenities with the submitted set.
     *
     * @param array<int, int> $amenityIds
     */
    public function sync(int $listingId, array $amenityIds): void
    {
        ListingAmenity::where('listing_id', $listingId)->delete();

        foreach ($amenityIds as $amenityId) {
            ListingAmenity::create([
                'listing_id' => $listingId,
                'amenity_id' => $amenityId,
            ]);
        }
    }

    /**
     * Create a new amenity and assign it to the listing right away.
     *
     * @param array<string, mixed> $data Validated payload.
     */
    public function createAndAssign(int $listingId, array $data): Amenity
    {
        $amenity = Amenity::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'status' => 1,
        ]);

        ListingAmenity::create([
            'listing_id' => $listingId,
            'amenity_id' => $amenity->id,
        ]);

        return $amenity;
    }
}
