<?php

namespace App\Services\Admin;

use App\Models\Location;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocationService
{
    /**
     * All locations shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Location::orderByDesc('created_at')
            ->get()
            ->map(fn (Location $location) => [
                'id' => $location->id,
                'name' => $location->name,
                'showAtHome' => (int) $location->show_at_home,
                'status' => (int) $location->status,
                'createdAt' => $location->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(array $data): Location
    {
        $location = new Location();
        $this->fill($location, $data);
        $location->save();

        $this->flushCache();

        return $location;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $this->fill($location, $data);
        $location->save();

        $this->flushCache();

        return $location;
    }

    public function delete(int $id): void
    {
        Location::findOrFail($id)->delete();

        $this->flushCache();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fill(Location $location, array $data): void
    {
        $location->name = $data['name'];
        $location->slug = Str::slug($data['name']);
        $location->show_at_home = $data['show_at_home'];
        $location->status = $data['status'];
    }

    private function flushCache(): void
    {
        Cache::forget('home_page_data');
        Cache::forget('frontend_locations_active');
        Cache::forget('admin_dashboard_stats');
    }
}
