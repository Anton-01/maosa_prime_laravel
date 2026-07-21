<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserManagementService
{
    /**
     * Sortable columns allowed from the frontend table.
     *
     * @var array<int, string>
     */
    private const SORTABLE_FIELDS = ['id', 'name', 'email', 'created_at'];

    /**
     * Server-side data source for the users table. Follows the
     * useDataTable contract: page, per_page, sort_field, sort_order,
     * search and filters[...] in; { data, total } out.
     *
     * @return array<string, mixed>
     */
    public function tableData(Request $request): array
    {
        $query = User::query()->with(['roles', 'permissions']);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($estacionId = $request->input('filters.estacion_id')) {
            $query->where('id_estacion', $estacionId);
        }

        $sortField = $request->string('sort_field')->toString();

        if (in_array($sortField, self::SORTABLE_FIELDS, true)) {
            $query->orderBy($sortField, $request->input('sort_order') === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id');
        }

        $page = $query->paginate(
            perPage: min(100, max(1, $request->integer('per_page', 10))),
        );

        return [
            'data' => collect($page->items())->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'isSuperAdmin' => $user->hasRole('Super Admin'),
                'isApproved' => (bool) $user->is_approved,
                'createdAt' => $user->created_at?->format('d/m/Y'),
            ])->all(),
            'total' => $page->total(),
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function create(Request $request, array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->user_type = 'admin';
        $user->is_approved = $request->boolean('is_approved') ? 1 : 0;
        $this->applyPriceTableSettings($user, $request);
        $user->save();

        $user->syncRoles($data['role']);
        $this->flushPermissionCache();

        return $user;
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(Request $request, int $id, array $data): User
    {
        $user = User::findOrFail($id);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_approved = $request->boolean('is_approved') ? 1 : 0;
        $user->user_type = 'admin';

        if (! empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $this->applyPriceTableSettings($user, $request);
        $user->save();

        $user->syncRoles($data['role']);
        $this->flushPermissionCache();

        return $user;
    }

    /**
     * Delete the user unless it is a protected Super Admin.
     */
    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin')) {
            return false;
        }

        $user->delete();

        return true;
    }

    /**
     * The user shaped for the edit form.
     *
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        $user = User::findOrFail($id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
            'is_approved' => (int) $user->is_approved,
            'can_view_price_table' => (bool) $user->can_view_price_table,
            'id_estacion' => $user->id_estacion,
            'isSuperAdmin' => $user->hasRole('Super Admin'),
        ];
    }

    /**
     * Data for the direct-permissions screen.
     *
     * @return array<string, mixed>
     */
    public function getForPermissionsEdit(int $id): array
    {
        $user = User::with('roles')->findOrFail($id);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values(),
                'isSuperAdmin' => $user->hasRole('Super Admin'),
            ],
            'allPermissionsGrouped' => Permission::all()
                ->groupBy('group_name')
                ->map(fn ($permissions) => $permissions->pluck('name')->values())
                ->toArray(),
            'directPermissions' => $user->getDirectPermissions()->pluck('name')->values(),
            'rolePermissions' => $user->getPermissionsViaRoles()->pluck('name')->values(),
        ];
    }

    /**
     * Sync direct permissions unless the user is a Super Admin.
     *
     * @param array<int, string> $permissions
     */
    public function syncDirectPermissions(int $id, array $permissions): bool
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin')) {
            return false;
        }

        $user->syncPermissions($permissions);
        $this->flushPermissionCache();

        return true;
    }

    /**
     * Toggle the user's approval flag and report the resulting state.
     */
    public function toggleApproval(int $id): bool
    {
        $user = User::findOrFail($id);
        $user->is_approved = ! $user->is_approved;
        $user->save();

        return (bool) $user->is_approved;
    }

    /**
     * The user detail shaped for the show screen, with permissions
     * grouped by group_name.
     *
     * @return array<string, mixed>
     */
    public function getForShow(int $id): array
    {
        $user = User::with('roles.permissions')->findOrFail($id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'userType' => $user->user_type,
            'phone' => $user->phone,
            'address' => $user->address,
            'isApproved' => (bool) $user->is_approved,
            'canViewPriceTable' => (bool) $user->can_view_price_table,
            'stationId' => $user->id_estacion,
            'partnerId' => $user->id_socio,
            'createdAt' => $user->created_at?->format('d/m/Y H:i'),
            'roles' => $user->getRoleNames()->values(),
            'directPermissions' => $user->getDirectPermissions()->pluck('name')->values(),
            'rolePermissions' => $user->getPermissionsViaRoles()->pluck('name')->values(),
            'allPermissionsGrouped' => $user->getAllPermissions()
                ->groupBy('group_name')
                ->map(fn ($permissions) => $permissions->pluck('name')->values())
                ->toArray(),
        ];
    }

    /**
     * Apply the "Mostrar tabla de precios" toggle and station selection.
     * When enabled with a station, the station is assigned respecting the
     * check_socio_estacion_exclusivo constraint (id_socio and id_estacion
     * cannot coexist). When the toggle is off the station is cleared.
     */
    private function applyPriceTableSettings(User $user, Request $request): void
    {
        $user->can_view_price_table = $request->boolean('can_view_price_table');

        if ($user->can_view_price_table && $request->filled('id_estacion')) {
            $user->id_estacion = (int) $request->input('id_estacion');
            $user->id_socio = null;
        } elseif (! $user->can_view_price_table) {
            $user->id_estacion = null;
        }
    }

    private function flushPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
