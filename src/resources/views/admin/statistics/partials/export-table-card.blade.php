{{--
    Tarjeta con una tabla DataTable y sus botones de exportación.
    Params:
      $title   -> título de la tarjeta
      $table   -> html builder del DataTable (yajra)
      $tableId -> id del <table> en el DOM
      $type    -> tipo para la ruta de exportación (browsers|countries|devices|pages|active-users)
--}}
<div class="card">
    <div class="card-header">
        <h4>{{ $title }}</h4>
        <div class="card-header-action">
            <button type="button" class="btn btn-sm btn-success stat-export-all"
                    data-table="{{ $tableId }}" data-url="{{ route('admin.statistics.export', $type) }}">
                <i class="fas fa-file-excel"></i> Exportar Todos
            </button>
            <button type="button" class="btn btn-sm btn-outline-success ml-1 stat-export-selected"
                    data-table="{{ $tableId }}" data-url="{{ route('admin.statistics.export', $type) }}">
                <i class="fas fa-check-square"></i> Exportar Seleccionados
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{ $table->table(['class' => 'table table-striped', 'style' => 'width: 100%']) }}
        </div>
    </div>
</div>
