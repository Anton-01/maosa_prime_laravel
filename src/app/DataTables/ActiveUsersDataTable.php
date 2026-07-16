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
            ->addColumn('checkbox', function ($user) {
                return '<input type="checkbox" class="stat-row-check" value="' . $user->id . '">';
            })
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
            // SR-016: las 3 IPs de sesión más frecuentes ya vienen concatenadas
            // desde el query; aquí solo se resuelve el placeholder cuando el
            // usuario no tuvo sesiones en el periodo filtrado.
            ->editColumn('top_ips_sesion', fn ($user) => $user->top_ips_sesion ?: 'N/A')
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
            ->rawColumns(['checkbox', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $dateFrom = request('date_from', now()->subDays(7)->format('Y-m-d'));
        $dateTo = request('date_to', now()->format('Y-m-d'));

        // SR-016: se agrega SOLO en el DataTable (no en la exportación) las 3 IPs
        // de sesión más frecuentes por usuario, respetando el mismo filtro de
        // fechas del panel. Es una subconsulta correlacionada evaluada únicamente
        // sobre las filas de la página visible (paginación server-side), por lo
        // que no penaliza el tiempo de carga del panel.
        return static::baseQuery($dateFrom, $dateTo)
            ->selectRaw(
                <<<'SQL'
                (
                    SELECT string_agg(top_ips.ip_address, ', ' ORDER BY top_ips.hits DESC)
                    FROM (
                        SELECT s.ip_address, COUNT(*) AS hits
                        FROM user_sessions s
                        WHERE s.user_id = users.id
                          AND s.ip_address IS NOT NULL
                          AND s.created_at BETWEEN ? AND ?
                        GROUP BY s.ip_address
                        ORDER BY hits DESC
                        LIMIT 3
                    ) top_ips
                ) AS top_ips_sesion
                SQL,
                [$dateFrom, $dateTo . ' 23:59:59']
            );
    }

    /**
     * Base query shared by the DataTable and the Excel export: users with page
     * visits in range, with their visit/session counts and last-session data.
     */
    public static function baseQuery(string $dateFrom, string $dateTo): QueryBuilder
    {
        $range = [$dateFrom, $dateTo . ' 23:59:59'];

        return (new User)->newQuery()
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
            ->orderBy(2, 'desc')
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
            Column::computed('checkbox')
                ->title('<input type="checkbox" class="stat-check-all">')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('name')->title('Usuario')->orderable(false)->searchable(true),
            Column::make('page_visits_count')->title('Visitas')->orderable(true)->searchable(false)->addClass('text-right'),
            Column::make('sessions_count')->title('Sesiones')->orderable(true)->searchable(false)->addClass('text-right'),
            Column::make('last_session_at')->title('Fecha última sesión')->orderable(false)->searchable(false),
            Column::make('last_session_location')->title('Ubicación última sesión')->orderable(false)->searchable(true),
            // SR-016: columna de monitoreo de seguridad. El backend entrega ya el
            // String con las IPs; el frontend solo lo renderiza (sin ordenar/buscar).
            Column::make('top_ips_sesion')->title('Top IPs de sesión')->orderable(false)->searchable(false),
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
