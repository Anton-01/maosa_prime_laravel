<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RolePermissionDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:access management roles index'])->only(['index']);
        $this->middleware(['permission:access management roles create'])->only(['create', 'store']);
        $this->middleware(['permission:access management roles update'])->only(['edit', 'update']);
        $this->middleware(['permission:access management roles delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RolePermissionDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.role-permission.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::all()->groupBy('group_name');
        return view('admin.role-permission.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role_name'   => ['required', 'max:40', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
        ]);

        $role = Role::create(['name' => $request->role_name]);
        $role->syncPermissions($request->permissions);

        return to_route('admin.role.index')->with('statusRoleC', true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $role = Role::findOrFail($id);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::all()->groupBy('group_name');
        return view('admin.role-permission.edit', compact('permissions', 'role', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'role_name'   => ['required', 'max:40', 'unique:roles,name,' . $id],
            'permissions' => ['required', 'array'],
        ]);

        $role = Role::findOrFail($id);
        if($role->name === 'Super Admin'){
            abort(403, 'No se puede modificar el rol Super Admin.');;
        }
        $role->name = $request->role_name;
        $role->save();

        $role->syncPermissions($request->permissions);

        return to_route('admin.role.index')->with('statusRoleU', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            if($role->name === 'Super Admin'){
                abort(403);
            }
            $role->delete();
            return response(['status' => 'success', 'message' => 'Rol eliminado correctamente']);
        } catch (\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
