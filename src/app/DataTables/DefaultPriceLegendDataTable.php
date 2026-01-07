<?php

namespace App\DataTables;

use App\Models\DefaultPriceLegend;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DefaultPriceLegendDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($query){
                $edit = '<a href="'.route('admin.default-legend.edit', $query->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                $delete = '<a href="'.route('admin.default-legend.destroy', $query->id).'" class="delete-item btn btn-sm btn-danger ml-2"><i class="fas fa-trash"></i></a>';

                return $edit.$delete;
            })
            ->addColumn('legend_text', function($query){
                return \Str::limit($query->legend_text, 80);
            })
            ->addColumn('is_active', function($query){
                if($query->is_active){
                    return "<span class='badge badge-primary'>Activo</span>";
                }else{
                    return "<span class='badge badge-danger'>Inactivo</span>";
                }
            })
            ->rawColumns(['action', 'is_active'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DefaultPriceLegend $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('sort_order');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('default-price-legend-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2)
            ->selectStyleSingle()
            ->buttons([
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->width(60),
            Column::make('legend_text')->title('Texto de Leyenda'),
            Column::make('sort_order')->title('Orden')->width(80),
            Column::make('is_active')->title('Estado')->width(80),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DefaultPriceLegend_' . date('YmdHis');
    }
}
