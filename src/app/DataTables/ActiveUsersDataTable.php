<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActiveUsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('last_session_at', function ($user) {
                return $user->last_session_at
                    ? Carbon::parse($user->last_session_at)->format('d/m/Y H:i')
                    : '—';
            })
            ->editColumn('last_session_location', function ($user) {
                return $user->last_session_location ?: 'Desconocida';
            })
            ->editColumn('page_visits_count', fn ($user) => number_format($user->page_visits_count))
            ->editColumn('sessions_count', fn ($user) => number_format($user->sessions_count))
            // Búsqueda global restringida a USUARIO y UBICACIÓN ÚLTIMA SESIÓN
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('users.name', 'ILIKE', "%{$keyword}%");
            })
            ->filterColumn('last_session_location', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT_WS(', ', last_session.city, last_session.region, last_session.country) ILIKE ?",
                    ["%{$keyword}%"]
                );
            })
            ->addColumn('action', function ($user) {
                return '<a href="' . route('admin.statistics.show', $user->id) . '" class="btn btn-sm btn-info" title="Ver estadísticas"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $dateFrom = request('date_from', now()->subDays(7)->format('Y-m-d'));
        $dateTo = request('date_to', now()->format('Y-m-d'));
        $range = [$dateFrom, $dateTo . ' 23:59:59'];

        return $model->newQuery()
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('last_session.last_session_at')
            ->selectRaw("NULLIF(CONCAT_WS(', ', last_session.city, last_session.region, last_session.country), '') as last_session_location")
            ->withLastSession()
            ->withCount([
                'pageVisits' => fn ($query) => $query->whereBetween('created_at', $range),
                'sessions' => fn ($query) => $query->whereBetween('created_at', $range),
            ])
            ->whereHas('pageVisits', fn ($query) => $query->whereBetween('created_at', $range));
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('active-users-table')
            ->columns($this->getColumns())
            ->ajax([
                // Reenvía el filtro de fechas vigente del panel en cada request ajax
                'data' => "function(d) {
                    d.date_from = $('input[name=date_from]').val();
                    d.date_to = $('input[name=date_to]').val();
                }",
            ])
            ->orderBy(1, 'desc')
            ->parameters([
                'lengthMenu' => [[10, 20, 50], [10, 20, 50]],
                'pageLength' => 10,
                'responsive' => true,
                'language' => [
                    'search' => 'Buscar (usuario / ubicación):',
                    'lengthMenu' => 'Mostrar _MENU_ registros',
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoEmpty' => 'Sin registros',
                    'infoFiltered' => '(filtrado de _MAX_ registros)',
                    'zeroRecords' => 'No se encontraron resultados',
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
     * Ordenamiento habilitado solo en VISITAS y SESIONES;
     * búsqueda global solo contra USUARIO y UBICACIÓN ÚLTIMA SESIÓN.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Usuario')->orderable(false)->searchable(true),
            Column::make('page_visits_count')->title('Visitas')->orderable(true)->searchable(false)->addClass('text-right'),
            Column::make('sessions_count')->title('Sesiones')->orderable(true)->searchable(false)->addClass('text-right'),
            Column::make('last_session_at')->title('Fecha última sesión')->orderable(false)->searchable(false),
            Column::make('last_session_location')->title('Ubicación última sesión')->orderable(false)->searchable(true),
            Column::computed('action')->title('Acción')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(70)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UsuariosMasActivos_' . date('YmdHis');
    }
}
