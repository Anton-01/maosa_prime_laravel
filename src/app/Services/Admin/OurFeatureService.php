<?php

namespace App\Services\Admin;

use App\Models\OurFeature;
use Illuminate\Support\Collection;

class OurFeatureService
{
    /**
     * All features ordered by newest first, shaped for the frontend.
     */
    public function list(): Collection
    {
        return OurFeature::orderByDesc('created_at')
            ->get()
            ->map(fn (OurFeature $feature) => [
                'id' => $feature->id,
                'icon' => $feature->icon,
                'title' => $feature->title,
                'shortDescription' => $feature->short_description,
                'status' => (int) $feature->status,
                'createdAt' => $feature->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(array $data): OurFeature
    {
        return OurFeature::create($this->mapPayload($data));
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(int $id, array $data): OurFeature
    {
        $feature = OurFeature::findOrFail($id);
        $feature->update($this->mapPayload($data));

        return $feature;
    }

    public function delete(int $id): void
    {
        OurFeature::findOrFail($id)->delete();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function mapPayload(array $data): array
    {
        return [
            'icon' => $data['icon'],
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'status' => $data['status'],
        ];
    }
}
