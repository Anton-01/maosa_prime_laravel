<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:access management users permissions']);
    }

    /**
     * Show form to assign direct permissions to a user.
     */
    public function edit(string $id): View
    {
        $user = User::with('roles')->findOrFail($id);

        $allPermissions     = Permission::all()->groupBy('group_name');
        $directPermissions  = $user->getDirectPermissions()->pluck('name')->toArray();
        $rolePermissions    = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        return view('admin.role-permission.user-permissions.edit', compact(
            'user', 'allPermissions', 'directPermissions', 'rolePermissions'
        ));
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

        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin')) {
            return back()->withErrors(['permissions' => 'No se pueden modificar permisos directos del Super Admin.']);
        }

        $user->syncPermissions($request->permissions ?? []);

        return to_route('admin.role-user.show', $user->id)->with('statusPermU', true);
    }
}
