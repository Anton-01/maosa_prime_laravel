<?php

namespace App\DataTables;

use App\Models\UserBranch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserBranchDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->addColumn('user_email', function ($row) {
                return $row->user ? $row->user->email : '-';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Activa</span>'
                    : '<span class="badge badge-danger">Inactiva</span>';
            })
            ->addColumn('price_lists_count', function ($row) {
                return $row->priceLists()->count();
            })
            ->addColumn('action', function ($row) {
                $editBtn = '<a href="' . route('admin.user-branch.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="' . route('admin.user-branch.destroy', $row->id) . '" class="btn btn-sm btn-danger delete-item ml-2"><i class="fas fa-trash"></i></a>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(UserBranch $model): QueryBuilder
    {
        return $model->newQuery()->with('user');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-branch-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
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
            Column::make('id')->title('ID'),
            Column::make('user_name')->title('Usuario'),
            Column::make('user_email')->title('Email'),
            Column::make('name')->title('Sucursal'),
            Column::make('status')->title('Estado'),
            Column::make('price_lists_count')->title('Listas de Precios'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center')
                ->title('Acciones'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UserBranch_' . date('YmdHis');
    }
}
