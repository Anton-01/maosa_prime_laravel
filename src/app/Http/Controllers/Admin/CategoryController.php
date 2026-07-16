<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Services\Admin\CategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
        $this->middleware(['permission:access management suppliers']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $this->categoryService->list(),
            'urls' => [
                'base' => route('admin.category.index'),
            ],
        ]);
    }

    /**
     * Legacy URL kept for old bookmarks: create/edit now happen in a
     * modal inside the index screen.
     */
    public function create(): RedirectResponse
    {
        return to_route('admin.category.index');
    }

    public function edit(string $id): RedirectResponse
    {
        return to_route('admin.category.index');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->categoryService->create($request, $request->validated());

        return back()->with('success', '¡Categoría creada correctamente!');
    }

    public function update(CategoryUpdateRequest $request, string $id): RedirectResponse
    {
        $this->categoryService->update($request, (int) $id, $request->validated());

        return back()->with('success', '¡Categoría actualizada correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->categoryService->delete((int) $id);

        return back()->with('success', '¡Categoría eliminada correctamente!');
    }
}
