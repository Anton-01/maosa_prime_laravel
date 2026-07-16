<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemStoreRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Http\Requests\Admin\MenuStoreRequest;
use App\Services\Admin\MenuBuilderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuBuilderController extends Controller
{
    public function __construct(private readonly MenuBuilderService $menuBuilderService)
    {
        $this->middleware(['permission:menu builder index']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Menus/Index', [
            'menus' => $this->menuBuilderService->list(),
            // Known site links, offered as suggestions for the link field.
            'linkSuggestions' => collect(config('menu-builder', []))
                ->map(fn ($link, $label) => ['label' => $label, 'value' => $link])
                ->values(),
            'urls' => [
                'menusStore' => route('admin.menu-builder.menus.store'),
                'menusBase' => url('admin/menu-builder/menus'),
                'itemsStore' => route('admin.menu-builder.items.store'),
                'itemsBase' => url('admin/menu-builder/items'),
            ],
        ]);
    }

    public function storeMenu(MenuStoreRequest $request): RedirectResponse
    {
        $this->menuBuilderService->createMenu($request->validated());

        return back()->with('success', '¡Menú creado correctamente!');
    }

    public function destroyMenu(string $id): RedirectResponse
    {
        $this->menuBuilderService->deleteMenu((int) $id);

        return back()->with('success', '¡Menú eliminado correctamente!');
    }

    public function storeItem(MenuItemStoreRequest $request): RedirectResponse
    {
        $this->menuBuilderService->createItem($request->validated());

        return back()->with('success', '¡Elemento agregado correctamente!');
    }

    public function updateItem(MenuItemUpdateRequest $request, string $id): RedirectResponse
    {
        $this->menuBuilderService->updateItem((int) $id, $request->validated());

        return back()->with('success', '¡Elemento actualizado correctamente!');
    }

    public function destroyItem(string $id): RedirectResponse
    {
        $this->menuBuilderService->deleteItem((int) $id);

        return back()->with('success', '¡Elemento eliminado correctamente!');
    }

    public function moveItem(Request $request, string $id): RedirectResponse
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);

        $this->menuBuilderService->moveItem((int) $id, $request->input('direction'));

        return back();
    }
}
