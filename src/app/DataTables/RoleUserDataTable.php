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

                // Todas las acciones agrupadas en un dropdown con etiquetas de texto
                return '<div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                            data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> Acciones
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="' . route('admin.role-user.show', $query->id) . '">
                            <i class="fas fa-eye text-secondary"></i> Ver detalle
                        </a>
                        <a class="dropdown-item" href="' . route('admin.role-user.edit', $query->id) . '">
                            <i class="fas fa-edit text-primary"></i> Editar
                        </a>
                        <a class="dropdown-item" href="' . route('admin.user-permissions.edit', $query->id) . '">
                            <i class="fas fa-key text-warning"></i> Permisos directos
                        </a>
                        <a class="dropdown-item" href="' . route('admin.statistics.show', $query->id) . '">
                            <i class="fas fa-chart-bar text-info"></i> Estadísticas
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger delete-item" href="' . route('admin.role-user.destroy', $query->id) . '">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>';
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
            ->addColumn('direct_permissions', function ($query) {
                $count = $query->getDirectPermissions()->count();
                return $count > 0
                    ? "<span class='badge badge-warning'>{$count} directo(s)</span>"
                    : "<span class='badge badge-light text-muted'>Ninguno</span>";
            })
            ->rawColumns(['role', 'approved', 'action', 'direct_permissions'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            // Filtro por estación (maosa_internal.cat_usuarios_importado) enviado
            // desde el select superior del listado.
            ->when(request('estacion_id'), function ($query, $estacionId) {
                $query->where('id_estacion', $estacionId);
            });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('roleuser-table')
            ->columns($this->getColumns())
            ->ajax([
                // Reenvía el filtro de estación seleccionado en cada petición ajax
                'data' => 'function(d){ var e = document.querySelector("#filter-estacion"); d.estacion_id = e ? e.value : ""; }',
            ])
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
            Column::make('direct_permissions')->title('Permisos Extra')->width(120),
            Column::computed('action')->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->width(130)
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
