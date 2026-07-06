<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InactiveUserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($user) {
                return '<input type="checkbox" class="inactive-user-checkbox" value="' . $user->id . '">';
            })
            ->editColumn('last_session_at', function ($user) {
                return $user->last_session_at
                    ? Carbon::parse($user->last_session_at)->format('d/m/Y H:i')
                    : 'Nunca ha iniciado sesión';
            })
            ->editColumn('last_session_location', fn ($user) => $user->last_session_location ?: 'Desconocida')
            ->addColumn('days_inactive', function ($user) {
                $reference = $user->last_session_at
                    ? Carbon::parse($user->last_session_at)
                    : Carbon::parse($user->created_at);
                return number_format($reference->diffInDays(now())) . ' días';
            })
            ->addColumn('role', function ($user) {
                $role = $user->getRoleNames()->first();
                return $role
                    ? "<span class='badge badge-success'>{$role}</span>"
                    : "<span class='badge badge-secondary'>Sin rol</span>";
            })
            ->filterColumn('name', fn ($query, $keyword) => $query->where('users.name', 'ILIKE', "%{$keyword}%"))
            ->filterColumn('email', fn ($query, $keyword) => $query->where('users.email', 'ILIKE', "%{$keyword}%"))
            ->orderColumn('last_session_at', 'last_session.last_session_at $1')
            ->rawColumns(['checkbox', 'role'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $days = max(1, (int) request('days', 30));
        $cutoff = now()->subDays($days);

        return $model->newQuery()
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->selectRaw('last_session.last_session_at')
            ->selectRaw("NULLIF(CONCAT_WS(', ', last_session.city, last_session.region, last_session.country), '') as last_session_location")
            ->withLastSession()
            ->inactiveSince($cutoff)
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'Super Admin'))
            ->where('users.id', '!=', auth()->id());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inactive-users-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => "function(d) { d.days = $('input[name=days]').val(); }",
            ])
            ->orderBy(4, 'asc')
            ->parameters([
                'lengthMenu' => [[10, 20, 50], [10, 20, 50]],
                'pageLength' => 10,
                'responsive' => true,
                'language' => [
                    'search' => 'Buscar:',
                    'lengthMenu' => 'Mostrar _MENU_ registros',
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoEmpty' => 'Sin registros',
                    'infoFiltered' => '(filtrado de _MAX_ registros)',
                    'zeroRecords' => 'No se encontraron usuarios inactivos',
                    'processing' => 'Procesando...',
                    'paginate' => [
                        'first' => 'Primero',
                        'last' => 'Último',
                        'next' => 'Siguiente',
                        'previous' => 'Anterior',
                    ],
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->title('<input type="checkbox" id="select-all-inactive">')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('name')->title('Usuario'),
            Column::make('email')->title('Correo'),
            Column::make('role')->title('Rol')->orderable(false)->searchable(false),
            Column::make('last_session_at')->title('Fecha última sesión')->searchable(false),
            Column::make('last_session_location')->title('Ubicación última sesión')->orderable(false)->searchable(false),
            Column::make('days_inactive')->title('Días inactivo')->orderable(false)->searchable(false)->addClass('text-right'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UsuariosInactivos_' . date('YmdHis');
    }
}
