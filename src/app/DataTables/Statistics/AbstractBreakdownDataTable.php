<?php

namespace App\DataTables\Statistics;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * Base común para las tablas de desglose del panel de estadísticas
 * (Navegadores, Países, Dispositivos, Páginas más visitadas).
 *
 * Todas comparten la misma forma: una etiqueta (label) y un total agrupado,
 * una primera columna de checkbox para la exportación de seleccionados, y el
 * reenvío del filtro de fechas en cada petición ajax. Las subclases solo
 * definen el query agrupado y unos pocos textos/rutas.
 */
abstract class AbstractBreakdownDataTable extends DataTable
{
    /** Id del <table> en el DOM. */
    abstract protected function tableId(): string;

    /** Título de la columna de etiqueta (p. ej. "Navegador"). */
    abstract protected function labelTitle(): string;

    /** Columna real de la BD usada en la búsqueda ILIKE (p. ej. "browser"). */
    abstract protected function searchColumn(): string;

    /** URL del endpoint ajax que sirve los datos de esta tabla. */
    abstract protected function ajaxUrl(): string;

    /**
     * Rango de fechas vigente (por defecto últimos 7 días), reenviado por el
     * data callback del ajax desde los inputs del filtro del panel.
     */
    protected function range(): array
    {
        $from = request('date_from', now()->subDays(7)->format('Y-m-d'));
        $to   = request('date_to', now()->format('Y-m-d'));

        return [$from, $to . ' 23:59:59'];
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="stat-row-check" value="' . e($row->label) . '">';
            })
            ->editColumn('total', fn ($row) => number_format($row->total))
            // Búsqueda global restringida a la etiqueta (columna real de la BD)
            ->filterColumn('label', function ($query, $keyword) {
                $query->where($this->searchColumn(), 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('total', 'COUNT(*) $1')
            ->rawColumns(['checkbox']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->tableId())
            ->columns($this->getColumns())
            ->ajax([
                'url'  => $this->ajaxUrl(),
                'data' => 'function(d){ d.date_from = document.querySelector("input[name=date_from]").value; d.date_to = document.querySelector("input[name=date_to]").value; }',
            ])
            ->orderBy(2, 'desc')
            ->parameters([
                'lengthMenu' => [[10, 20, 50], [10, 20, 50]],
                'pageLength' => 10,
                'responsive' => true,
                'dom'        => 'frtip',
                'language'   => [
                    'search'       => 'Buscar:',
                    'lengthMenu'   => 'Mostrar _MENU_ registros',
                    'info'         => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoEmpty'    => 'Sin registros',
                    'infoFiltered' => '(filtrado de _MAX_ registros)',
                    'zeroRecords'  => 'No se encontraron resultados',
                    'processing'   => 'Procesando...',
                    'paginate'     => [
                        'first'    => 'Primero',
                        'last'     => 'Último',
                        'next'     => 'Siguiente',
                        'previous' => 'Anterior',
                    ],
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->title('<input type="checkbox" class="stat-check-all">')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('label')->title($this->labelTitle())->orderable(false)->searchable(true),
            Column::make('total')->title('Total')->orderable(true)->searchable(false)->addClass('text-right'),
        ];
    }
}
