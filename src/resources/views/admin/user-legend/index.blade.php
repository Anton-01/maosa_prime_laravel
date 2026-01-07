@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Leyendas por Usuario</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Leyendas por Usuario</div>
            </div>
        </div>

        <div class="section-body">
            <!-- User Selector -->
            <div class="card">
                <div class="card-header">
                    <h4>Seleccionar Usuario</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-6">
                            <label>Usuario</label>
                            <select name="user_id" class="form-control select2">
                                <option value="">Seleccione un usuario...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $user && $user->id == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Ver Leyendas
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($user)
                <!-- User Info -->
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">{{ $user->name }}</h5>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <div class="col-md-4 text-right">
                                <form method="POST" action="{{ route('admin.user-legend.copy-defaults') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Esto reemplazara todas las leyendas actuales del usuario con las leyendas por defecto. Continuar?')">
                                        <i class="fas fa-copy"></i> Copiar Leyendas por Defecto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Legend Form -->
                <div class="card">
                    <div class="card-header">
                        <h4>Agregar Nueva Leyenda</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.user-legend.store') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Texto de Leyenda <span class="text-danger">*</span></label>
                                        <input type="text" name="legend_text" class="form-control" required placeholder="Ej: Precios sujetos a cambio sin previo aviso">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Orden <span class="text-danger">*</span></label>
                                        <input type="number" name="sort_order" class="form-control" required value="{{ $legends->count() + 1 }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estado <span class="text-danger">*</span></label>
                                        <select name="is_active" class="form-control" required>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Legends List -->
                <div class="card">
                    <div class="card-header">
                        <h4>Leyendas de {{ $user->name }} ({{ $legends->count() }})</h4>
                    </div>
                    <div class="card-body">
                        @if($legends->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 80px;">Orden</th>
                                        <th>Texto de Leyenda</th>
                                        <th style="width: 100px;">Estado</th>
                                        <th style="width: 150px;" class="text-center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($legends as $legend)
                                        <tr id="legend-row-{{ $legend->id }}">
                                            <form method="POST" action="{{ route('admin.user-legend.update', $legend->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <td>
                                                    <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $legend->sort_order }}" min="0" style="width: 60px;">
                                                </td>
                                                <td>
                                                    <input type="text" name="legend_text" class="form-control form-control-sm" value="{{ $legend->legend_text }}" required>
                                                </td>
                                                <td>
                                                    <select name="is_active" class="form-control form-control-sm">
                                                        <option value="1" {{ $legend->is_active ? 'selected' : '' }}>Activo</option>
                                                        <option value="0" {{ !$legend->is_active ? 'selected' : '' }}>Inactivo</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="submit" class="btn btn-sm btn-info" title="Guardar">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-legend" data-id="{{ $legend->id }}" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-3">Este usuario no tiene leyendas configuradas.</p>
                                <form method="POST" action="{{ route('admin.user-legend.copy-defaults') }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-copy"></i> Copiar Leyendas por Defecto
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- No user selected -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Seleccione un usuario para gestionar sus leyendas personalizadas.</p>
                        <p class="small text-muted">Solo se muestran usuarios que tienen acceso a la tabla de precios.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();
            // Delete legend
            $('.delete-legend').on('click', function() {
                var id = $(this).data('id');
                if (confirm('Esta seguro de eliminar esta leyenda?')) {
                    $.ajax({
                        url: '{{ url("admin/user-legend") }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#legend-row-' + id).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                new FilamentNotification()
                                    .title('Éxito')
                                    .body(response.message)
                                    .success()
                                    .send();
                            }
                        },
                        error: function() {
                            new FilamentNotification()
                                .title('Error')
                                .body('Error al eliminar la leyenda')
                                .danger()
                                .send();
                        }
                    });
                }
            });
        });
    </script>
@endpush
