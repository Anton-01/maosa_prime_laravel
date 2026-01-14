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
                        <!-- Import Excel Card -->
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-file-excel text-success"></i> Importar Usuarios desde Excel</h4>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <p class="mb-0">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Puede cargar un archivo Excel con la información de los usuarios (nombre y email).
                                            Los usuarios serán creados con el rol "User", aprobados y con una contraseña segura generada automáticamente.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a href="{{ route('admin.user-import.layout') }}" class="btn btn-outline-success btn-sm mr-2">
                                            <i class="fas fa-download"></i> Descargar Layout
                                        </a>
                                        <a href="{{ route('admin.user-import.index') }}" class="btn btn-success">
                                            <i class="fas fa-file-upload"></i> Importar Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Todos los usuarios</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.role-user.export') }}" class="btn btn-success mr-2">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </a>
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

@push('styles')
    <style>
        .price-access-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        .price-access-wrapper .custom-switch {
            padding-left: 2.25rem;
        }
        .price-access-wrapper .status-text {
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
            transition: all 0.3s ease;
        }
        .price-access-wrapper .status-text.text-success {
            color: #28a745 !important;
        }
        .price-access-wrapper .status-text.text-secondary {
            color: #6c757d !important;
        }
        /* Toast notification styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            padding: 15px 20px;
            border-radius: 8px;
            color: #fff;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toast-notification.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .toast-notification.error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .toast-notification i {
            font-size: 18px;
        }
        .toast-notification .toast-close {
            margin-left: auto;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .toast-notification .toast-close:hover {
            opacity: 1;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
@endpush
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        // Custom toast notification function
        function showToast(message, type = 'success') {
            // Remove any existing toast
            $('.toast-notification').remove();
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            const toast = $(`
                <div class="toast-notification ${type}">
                    <i class="${icon}"></i>
                    <span>${message}</span>
                    <span class="toast-close"><i class="fas fa-times"></i></span>
                </div>
            `);
            $('body').append(toast);
            // Close on click
            toast.find('.toast-close').on('click', function() {
                toast.css('animation', 'slideOut 0.3s ease-out forwards');
                setTimeout(() => toast.remove(), 300);
            });
            // Auto remove after 4 seconds
            setTimeout(() => {
                if (toast.length) {
                    toast.css('animation', 'slideOut 0.3s ease-out forwards');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4000);
        }

        $(document).on('change', '.toggle-price-table', function() {
            const userId = $(this).data('user-id');
            const checkbox = $(this);
            const wrapper = checkbox.closest('.price-access-wrapper');
            const statusText = wrapper.find('.status-text');
            $.ajax({
                url: `{{ url('admin/role-user') }}/${userId}/toggle-price-table`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.can_view_price_table) {
                            statusText.removeClass('text-secondary').addClass('text-success').text('Activo');
                        } else {
                            statusText.removeClass('text-success').addClass('text-secondary').text('Inactivo');
                        }
                        showToast(response.message || 'Acceso actualizado correctamente', 'success');
                    } else {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        showToast(response.message || 'Error al actualizar el acceso', 'error');
                    }
                },
                error: function() {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    showToast('Error al procesar la solicitud', 'error');
                }
            });
        });
    </script>
@endpush
