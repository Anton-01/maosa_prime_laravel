<?php

namespace App\Services\Admin;

use App\Models\Amenity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AmenityService
{
    /**
     * All amenities shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Amenity::orderByDesc('created_at')
            ->get()
            ->map(fn (Amenity $amenity) => [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'status' => (int) $amenity->status,
                'createdAt' => $amenity->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(array $data): Amenity
    {
        return Amenity::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'status' => $data['status'],
        ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(int $id, array $data): Amenity
    {
        $amenity = Amenity::findOrFail($id);

        $amenity->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'status' => $data['status'],
        ]);

        return $amenity;
    }

    public function delete(int $id): void
    {
        Amenity::findOrFail($id)->delete();
    }
}
