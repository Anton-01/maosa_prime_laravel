@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Usuarios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Todos los usuarios</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusUsrC'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Usuario creado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session()->has('statusUsrU'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Actualizado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>Todos los usuarios</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.statistics.index') }}" class="btn btn-info mr-2">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="{{ route('admin.role-user.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Crear
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Use el switch en "Acceso Precios" para activar/desactivar la vista de tabla de precios para cada usuario.
                            </div>
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $(document).on('change', '.toggle-price-table', function() {
            const userId = $(this).data('user-id');
            const checkbox = $(this);
            const label = checkbox.siblings('label').find('.badge');
            $.ajax({
                url: `{{ url('admin/role-user') }}/${userId}/toggle-price-table`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.can_view_price_table) {
                            label.removeClass('badge-secondary').addClass('badge-success').text('Activo');
                        } else {
                            label.removeClass('badge-success').addClass('badge-secondary').text('Inactivo');
                        }
                        toastr.success(response.message || 'Acceso actualizado correctamente');
                    } else {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        toastr.error(response.message || 'Error al actualizar el acceso');
                    }
                },
                error: function() {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    toastr.error('Error al procesar la solicitud');
                }
            });
        });
    </script>
@endpush
