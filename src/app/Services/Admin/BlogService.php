<?php

namespace App\Services\Admin;

use App\Models\Blog;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogService
{
    public function __construct(private readonly ImageUploadService $imageUploadService){}

    /**
     * All posts shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Blog::with('author:id,name')
            ->orderByDesc('created_at')
            ->get()->map(fn (Blog $blog) => [
                'id' => $blog->id,
                'title' => $blog->title,
                'imageUrl' => $blog->image ? asset($blog->image) : null,
                'author' => $blog->author->name ?? 'N/A',
                'status' => (int) $blog->status,
                'createdAt' => $blog->created_at?->format('d/m/Y'),
            ]);
    }

    /**
     * The post shaped for the edit form.
     *
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        $blog = Blog::findOrFail($id);

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'description' => $blog->description,
            'is_popular' => (int) $blog->is_popular,
            'status' => (int) $blog->status,
            'image' => $blog->image,
            'imageUrl' => $blog->image ? asset($blog->image) : null,
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(Request $request, array $data): Blog
    {
        $blog = new Blog();
        $blog->image = $this->imageUploadService->upload($request, 'image');
        $this->fill($blog, $data);
        $blog->save();

        return $blog;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(Request $request, int $id, array $data): Blog
    {
        $blog = Blog::findOrFail($id);
        $imagePath = $this->imageUploadService->upload($request, 'image', $blog->image);
        $blog->image = $imagePath ?: $blog->image;
        $this->fill($blog, $data);
        $blog->save();
        return $blog;
    }

    public function delete(int $id): void
    {
        $blog = Blog::findOrFail($id);
        $this->imageUploadService->deleteFile($blog->image);
        $blog->delete();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fill(Blog $blog, array $data): void
    {
        $blog->author_id = Auth::id();
        $blog->title = $data['title'];
        $blog->slug = Str::slug($data['title']);
        $blog->description = $data['description'];
        $blog->is_popular = $data['is_popular'];
        $blog->status = $data['status'];
    }
}
