<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleUserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($query) {
                if ($query->getRoleNames()->first() === 'Super Admin') {
                    return '';
                }
                $show   = '<a href="' . route('admin.role-user.show', $query->id) . '" class="btn btn-sm btn-secondary" title="Ver detalle"><i class="fas fa-eye"></i></a>';
                $edit   = '<a href="' . route('admin.role-user.edit', $query->id) . '" class="btn btn-sm btn-primary ml-1" title="Editar"><i class="fas fa-edit"></i></a>';
                $perms  = '<a href="' . route('admin.user-permissions.edit', $query->id) . '" class="btn btn-sm btn-warning ml-1" title="Permisos directos"><i class="fas fa-key"></i></a>';
                $delete = '<a href="' . route('admin.role-user.destroy', $query->id) . '" class="delete-item btn btn-sm btn-danger ml-1" title="Eliminar"><i class="fas fa-trash"></i></a>';
                $stats  = '<a href="' . route('admin.statistics.show', $query->id) . '" class="btn btn-sm btn-info ml-1" title="Estadísticas"><i class="fas fa-chart-bar"></i></a>';
                return $show . $edit . $perms . $delete . $stats;
            })
            ->addColumn('role', function ($query) {
                $role = $query->getRoleNames()->first();
                return $role
                    ? "<span class='badge badge-success'>{$role}</span>"
                    : "<span class='badge badge-secondary'>Sin rol</span>";
            })
            ->addColumn('approved', function ($query) {
                $checked     = $query->is_approved ? 'checked' : '';
                $statusClass = $query->is_approved ? 'text-success' : 'text-secondary';
                $statusText  = $query->is_approved ? 'Aprobado' : 'No aprobado';

                return '<div class="approval-wrapper">
                    <div class="form-check form-switch justify-content-center">
                        <input class="form-check-input toggle-approval" type="checkbox" role="switch"
                               id="approval' . $query->id . '" data-user-id="' . $query->id . '" ' . $checked . '>
                    </div>
                    <div class="approval-status-text ' . $statusClass . '">' . $statusText . '</div>
                </div>';
            })
            ->addColumn('price_table_access', function ($query) {
                $checked     = $query->can_view_price_table ? 'checked' : '';
                $statusClass = $query->can_view_price_table ? 'text-success' : 'text-secondary';
                $statusText  = $query->can_view_price_table ? 'Activo' : 'Inactivo';

                return '<div class="price-access-wrapper">
                    <div class="form-check form-switch justify-content-center">
                        <input class="form-check-input toggle-price-table" type="checkbox" role="switch"
                               id="priceTable' . $query->id . '" data-user-id="' . $query->id . '" ' . $checked . '>
                    </div>
                    <div class="status-text ' . $statusClass . '">' . $statusText . '</div>
                </div>';
            })
            ->addColumn('direct_permissions', function ($query) {
                $count = $query->getDirectPermissions()->count();
                return $count > 0
                    ? "<span class='badge badge-warning'>{$count} directo(s)</span>"
                    : "<span class='badge badge-light text-muted'>Ninguno</span>";
            })
            ->rawColumns(['role', 'approved', 'action', 'price_table_access', 'direct_permissions'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('roleuser-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [

            Column::make('id')->width(50),
            Column::make('name')->title('Nombre'),
            Column::make('email')->title('Correo'),
            Column::make('role')->title('Rol'),
            Column::make('approved')->title('Aprobado')->width(110),
            Column::make('price_table_access')->title('Tabla Precios')->width(120),
            Column::make('direct_permissions')->title('Permisos Extra')->width(120),
            Column::computed('action')->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(190)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Usuarios_' . date('YmdHis');
    }
}
