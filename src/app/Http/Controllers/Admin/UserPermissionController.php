<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserPermissionController extends Controller
{
    public function __construct(private readonly UserManagementService $userManagementService)
    {
        $this->middleware(['permission:access management users permissions']);
    }

    /**
     * Show form to assign direct permissions to a user.
     */
    public function edit(string $id): Response
    {
        return Inertia::render('Admin/Users/Permissions', [
            ...$this->userManagementService->getForPermissionsEdit((int) $id),
            'urls' => [
                'base' => route('admin.role-user.index'),
                'update' => route('admin.user-permissions.update', $id),
                'show' => route('admin.role-user.show', $id),
            ],
        ]);
    }

    /**
     * Sync direct permissions for a user.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if (! $this->userManagementService->syncDirectPermissions((int) $id, $request->permissions ?? [])) {
            return back()->withErrors(['permissions' => 'No se pueden modificar permisos directos del Super Admin.']);
        }

        return to_route('admin.role-user.show', $id)->with('success', '¡Permisos directos actualizados correctamente!');
    }
}
