<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /**
     * All categories shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Category::orderByDesc('created_at')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'imageIcon' => $category->image_icon,
                'imageIconUrl' => $category->image_icon ? asset($category->image_icon) : null,
                'backgroundImage' => $category->background_image,
                'backgroundImageUrl' => $category->background_image ? asset($category->background_image) : null,
                'showAtHome' => (int) $category->show_at_home,
                'status' => (int) $category->status,
                'createdAt' => $category->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(Request $request, array $data): Category
    {
        $category = new Category();
        $category->image_icon = $this->imageUploadService->upload($request, 'image_icon');
        $category->background_image = $this->imageUploadService->upload($request, 'background_image');
        $this->fill($category, $data);
        $category->save();

        $this->flushCache();

        return $category;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(Request $request, int $id, array $data): Category
    {
        $category = Category::findOrFail($id);

        $iconPath = $this->imageUploadService->upload($request, 'image_icon', $category->image_icon);
        $backgroundPath = $this->imageUploadService->upload($request, 'background_image', $category->background_image);

        $category->image_icon = $iconPath ?: $category->image_icon;
        $category->background_image = $backgroundPath ?: $category->background_image;
        $this->fill($category, $data);
        $category->save();

        $this->flushCache();

        return $category;
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->imageUploadService->deleteFile($category->image_icon);
        $this->imageUploadService->deleteFile($category->background_image);

        $category->delete();

        $this->flushCache();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fill(Category $category, array $data): void
    {
        $category->name = $data['name'];
        $category->slug = Str::slug($data['name']);
        $category->show_at_home = $data['show_at_home'];
        $category->status = $data['status'];
    }

    private function flushCache(): void
    {
        Cache::forget('home_page_data');
        Cache::forget('frontend_categories_active');
        Cache::forget('about_page_data');
        Cache::forget('admin_dashboard_stats');
    }
}
