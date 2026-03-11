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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> ¡Usuario creado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session()->has('statusUsrU'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> ¡Usuario actualizado correctamente!
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                    @endif
                        {{-- Import Excel Card --}}
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-file-excel text-success"></i> Importar usuarios desde Excel</h4>
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
                                            <i class="fas fa-download"></i> Descargar layout
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
                                <a href="{{ route('admin.statistics.index') }}" class="btn btn-info mr-2" title="Estadísticas globales">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="{{ route('admin.role-user.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear usuario
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Use los switches en <strong>Aprobado</strong> y <strong>Tabla Precios</strong> para activar/desactivar directamente desde esta lista.
                                El ícono <i class="fas fa-key text-warning"></i> permite asignar permisos directos adicionales al rol.
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
        /* --- Contenedores Principales --- */
        .approval-wrapper,
        .price-access-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 4px 2px;
        }

        /* --- Reset de Bootstrap para el contenedor del Checkbox --- */
        .approval-wrapper .form-check,
        .price-access-wrapper .form-check {
            display: flex;
            justify-content: center;
            padding-left: 0;
            margin-bottom: 0;
        }

        /* --- Estilo Base del Switch (Cápsula) --- */
        .approval-wrapper .form-check-input,
        .price-access-wrapper .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            width: 2.8em;
            height: 1.4em;
            background-color: #dee2e6; /* Gris claro (Bootstrap secondary) */
            border-radius: 50px;
            position: relative;
            cursor: pointer;
            outline: none;
            border: none;
            transition: background-color 0.3s ease;
            margin-left: 0;
        }

        /* --- El "Radio" (Círculo Deslizante) --- */
        .approval-wrapper .form-check-input::before,
        .price-access-wrapper .form-check-input::before {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 1.1em;
            height: 1.1em;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        /* --- Estado: Checked (Activado / Aprobado) --- */
        .approval-wrapper .form-check-input:checked,
        .price-access-wrapper .form-check-input:checked {
            background-color: #198754; /* Verde Success */
        }

        /* Desplazamiento del círculo al activar */
        .approval-wrapper .form-check-input:checked::before,
        .price-access-wrapper .form-check-input:checked::before {
            transform: translateX(1.4em);
        }

        /* --- Estilos de los Textos de Estado --- */
        .approval-wrapper .approval-status-text,
        .price-access-wrapper .status-text {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.25s ease;
        }

        /* Toast notification styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out forwards;
            min-width: 260px;
            max-width: 380px;
        }
        .toast-notification.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .toast-notification.error   { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .toast-notification i { font-size: 18px; }
        .toast-notification .toast-close { margin-left: auto; cursor: pointer; opacity: 0.8; transition: opacity 0.2s; }
        .toast-notification .toast-close:hover { opacity: 1; }

        @keyframes slideIn {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0);    opacity: 1; }
            to   { transform: translateX(110%); opacity: 0; }
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

        // Toggle price table access
        $(document).on('change', '.toggle-price-table', function() {
            const userId = $(this).data('user-id');
            const checkbox = $(this);
            const wrapper = checkbox.closest('.price-access-wrapper');
            const statusText = wrapper.find('.status-text');
            $.ajax({
                url: `{{ url('admin/role-user') }}/${userId}/toggle-price-table`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status === 'success') {
                        if (response.can_view_price_table) {
                            statusText.removeClass('text-secondary').addClass('text-success').text('Activo');
                        } else {
                            statusText.removeClass('text-success').addClass('text-secondary').text('Inactivo');
                        }
                        showToast(response.message, 'success');
                    }else {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        showToast(response.message || 'Error al actualizar', 'error');
                    }
                },
                error: function () {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    showToast('Error al procesar la solicitud', 'error');
                }
            });
        });

        // Toggle approval status
        $(document).on('change', '.toggle-approval', function () {
            const userId = $(this).data('user-id');
            const checkbox = $(this);
            const wrapper = checkbox.closest('.approval-wrapper');
            const statusText = wrapper.find('.approval-status-text');
            $.ajax({
                url: `{{ url('admin/role-user') }}/${userId}/toggle-approval`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status === 'success') {
                        if (response.is_approved) {
                            statusText.removeClass('text-secondary').addClass('text-success').text('Aprobado');
                        } else {
                            statusText.removeClass('text-success').addClass('text-secondary').text('No aprobado');
                        }
                        showToast(response.message, 'success');
                    } else {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        showToast(response.message || 'Error al actualizar', 'error');
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
