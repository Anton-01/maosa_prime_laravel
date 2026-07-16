<?php

namespace App\Services\Admin;

use App\Models\Hero;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HeroService
{
    public const TYPE_PRIVATE = 'private';

    public const TYPE_PUBLIC = 'public';

    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    public function findActiveByType(string $type): ?Hero
    {
        return Hero::where(['type' => $type, 'active' => 1])->first();
    }

    /**
     * Update the active hero of the given type, replacing the background
     * image when a new file is uploaded.
     *
     * @param array<string, mixed> $data Validated payload (title, sub_title, old_background).
     */
    public function updateByType(string $type, Request $request, array $data): ?Hero
    {
        $hero = $this->findActiveByType($type);

        if (! $hero) {
            return null;
        }

        $imagePath = $this->imageUploadService->upload($request, 'background', $data['old_background'] ?? null);

        $hero->update([
            'background' => $imagePath ?: ($data['old_background'] ?? $hero->background),
            'title' => $data['title'],
            'sub_title' => $data['sub_title'],
        ]);

        Cache::forget($type === self::TYPE_PUBLIC ? 'hero_public' : 'hero_private');

        return $hero;
    }
}
