<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Services\Admin\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __construct(private readonly RoleService $roleService)
    {
        $this->middleware(['permission:access management roles index'])->only(['index']);
        $this->middleware(['permission:access management roles create'])->only(['create', 'store']);
        $this->middleware(['permission:access management roles update'])->only(['edit', 'update']);
        $this->middleware(['permission:access management roles delete'])->only(['destroy']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => $this->roleService->list(),
            'urls' => [
                'base' => route('admin.role.index'),
                'create' => route('admin.role.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Roles/Form', [
            'role' => null,
            'permissionsGrouped' => $this->roleService->permissionsGrouped(),
            'urls' => [
                'base' => route('admin.role.index'),
            ],
        ]);
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->roleService->create($request->validated());

        return to_route('admin.role.index')->with('success', '¡Rol creado correctamente!');
    }

    public function edit(string $id): Response
    {
        $role = Role::findOrFail($id);

        return Inertia::render('Admin/Roles/Form', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->values(),
            ],
            'permissionsGrouped' => $this->roleService->permissionsGrouped(),
            'urls' => [
                'base' => route('admin.role.index'),
            ],
        ]);
    }

    public function update(RoleUpdateRequest $request, string $id): RedirectResponse
    {
        $this->roleService->update((int) $id, $request->validated());

        return to_route('admin.role.index')->with('success', '¡Rol actualizado correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->roleService->delete((int) $id);

        return back()->with('success', 'Rol eliminado correctamente');
    }
}
