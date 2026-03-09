<?php

namespace App\DataTables;

use App\Models\UserPriceList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserPriceListDataTable extends DataTable
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
                $view = '<a href="'.route('admin.user-price.show', $query->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                $edit = '<a href="'.route('admin.user-price.edit', $query->id).'" class="btn btn-sm btn-primary ml-1"><i class="fas fa-edit"></i></a>';
                $delete = '<a href="'.route('admin.user-price.destroy', $query->id).'" class="delete-item btn btn-sm btn-danger ml-1"><i class="fas fa-trash"></i></a>';

                return $view.$edit.$delete;
            })
            ->addColumn('user_name', function($query){
                return $query->user ? $query->user->name : '-';
            })
            ->addColumn('user_email', function($query){
                return $query->user ? $query->user->email : '-';
            })
            ->addColumn('branch_name', function($query){
                return $query->branch ? $query->branch->name : '-';
            })
            ->addColumn('items_count', function($query){
                return $query->items_count ?? 0;
            })
            ->addColumn('price_date', function($query){
                return $query->price_date->format('d/m/Y');
            })
            ->addColumn('is_active', function($query){
                if($query->is_active){
                    return "<span class='badge badge-success'>Activo</span>";
                }else{
                    return "<span class='badge badge-secondary'>Inactivo</span>";
                }
            })
            ->addColumn('created_by_name', function($query){
                return $query->createdBy ? $query->createdBy->name : '-';
            })
            ->rawColumns(['action', 'is_active'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(UserPriceList $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['user', 'createdBy', 'branch'])
            ->withCount('items');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-price-list-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
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
            Column::make('user_name')->title('Cliente'),
            Column::make('user_email')->title('Email'),
            Column::make('branch_name')->title('Sucursal')->width(120),
            Column::make('items_count')->title('Terminales')->width(90),
            Column::make('price_date')->title('Fecha')->width(100),
            Column::make('is_active')->title('Estado')->width(80),
            Column::make('created_by_name')->title('Creado por'),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(130)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UserPriceList_' . date('YmdHis');
    }
}
