<?php

namespace App\Services\Admin;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public const PROTECTED_ROLE = 'Super Admin';

    /**
     * All roles shaped for the frontend table.
     */
    public function list(): Collection
    {
        return Role::with('permissions')
            ->orderBy('id')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'isProtected' => $role->name === self::PROTECTED_ROLE,
                'permissions' => $role->name === self::PROTECTED_ROLE
                    ? []
                    : $role->permissions->pluck('name')->values(),
            ]);
    }

    /**
     * Every permission name grouped by group_name, for the role form.
     *
     * @return array<string, mixed>
     */
    public function permissionsGrouped(): array
    {
        return Permission::all()
            ->groupBy('group_name')
            ->map(fn ($permissions) => $permissions->pluck('name')->values())
            ->toArray();
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(array $data): Role
    {
        $role = Role::create(['name' => $data['role_name']]);
        $role->syncPermissions($data['permissions']);

        return $role;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);

        abort_if($role->name === self::PROTECTED_ROLE, 403, 'No se puede modificar el rol Super Admin.');

        $role->name = $data['role_name'];
        $role->save();

        $role->syncPermissions($data['permissions']);

        return $role;
    }

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);

        abort_if($role->name === self::PROTECTED_ROLE, 403, 'No se puede eliminar el rol Super Admin.');

        $role->delete();
    }
}
