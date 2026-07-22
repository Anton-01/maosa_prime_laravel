<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogCreateRequest;
use App\Http\Requests\Admin\BlogUpdateRequest;
use App\Services\Admin\BlogService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(private readonly BlogService $blogService)
    {
        $this->middleware(['permission:blog index'])->only(['index']);
        $this->middleware(['permission:blog create'])->only(['create', 'store']);
        $this->middleware(['permission:blog update'])->only(['edit', 'update']);
        $this->middleware(['permission:blog delete'])->only(['destroy']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Blogs/Index', [
            'blogs' => $this->blogService->list(),
            'urls' => [
                'base' => route('admin.blog.index'),
                'create' => route('admin.blog.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Blogs/Form', [
            'blog' => null,
            'urls' => [
                'base' => route('admin.blog.index'),
            ],
        ]);
    }

    public function store(BlogCreateRequest $request): RedirectResponse
    {
        $this->blogService->create($request, $request->validated());

        return to_route('admin.blog.index')->with('success', '¡Publicación creada correctamente!');
    }

    public function edit(string $id): Response
    {
        return Inertia::render('Admin/Blogs/Form', [
            'blog' => $this->blogService->getForEdit((int) $id),
            'urls' => [
                'base' => route('admin.blog.index'),
            ],
        ]);
    }

    public function update(BlogUpdateRequest $request, string $id): RedirectResponse
    {
        $this->blogService->update($request, (int) $id, $request->validated());

        return to_route('admin.blog.index')->with('success', '¡Publicación actualizada correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->blogService->delete((int) $id);

        return back()->with('success', '¡Publicación eliminada correctamente!');
    }
}
