@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Usuarios inactivos</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item">Usuarios inactivos</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Usuarios sin actividad</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.inactive-users.export', ['days' => $days]) }}"
                                   id="export-inactive" class="btn btn-success mr-2">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </a>
                                @can('access management users delete')
                                    <button type="button" id="delete-selected" class="btn btn-danger" disabled>
                                        <i class="fas fa-trash"></i> Eliminar seleccionados
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Se listan usuarios cuya última sesión (o fecha de registro, si nunca han iniciado sesión)
                                es anterior al umbral de inactividad. La eliminación es <strong>permanente</strong> y requiere
                                confirmar su contraseña de administrador.
                            </div>

                            <form method="GET" action="{{ route('admin.inactive-users.index') }}" class="form-inline mb-4">
                                <label for="days" class="mr-2">Días de inactividad mínimos:</label>
                                <input type="number" name="days" id="days" class="form-control mr-2"
                                       style="width: 110px" min="1" value="{{ $days }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Aplicar
                                </button>
                            </form>

                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal de confirmación con contraseña --}}
    <div class="modal fade" id="confirm-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h5>
                </div>
                <div class="modal-body">
                    <p>
                        Está a punto de eliminar <strong><span id="selected-count">0</span> usuario(s)</strong> de forma
                        <strong>permanente</strong>. Esta acción no se puede deshacer.
                    </p>
                    <div class="form-group mb-0">
                        <label for="admin-password">Ingrese su contraseña actual para confirmar <span class="text-danger">*</span></label>
                        <input type="password" id="admin-password" class="form-control"
                               placeholder="Contraseña de administrador" autocomplete="current-password">
                        <div class="invalid-feedback d-block" id="password-error" style="display:none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancel-delete">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        <i class="fas fa-trash"></i> Eliminar definitivamente
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $(function () {
            const deleteBtn = $('#delete-selected');
            const modal = $('#confirm-delete-modal');
            const passwordInput = $('#admin-password');
            const passwordError = $('#password-error');

            function selectedIds() {
                return $('.inactive-user-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();
            }

            function refreshDeleteButton() {
                deleteBtn.prop('disabled', selectedIds().length === 0);
            }

            // Seleccionar / deseleccionar todos (solo página visible)
            $(document).on('change', '#select-all-inactive', function () {
                $('.inactive-user-checkbox').prop('checked', $(this).prop('checked'));
                refreshDeleteButton();
            });
            $(document).on('change', '.inactive-user-checkbox', refreshDeleteButton);

            // Mantener el link de exportación sincronizado con el filtro de días
            $('#days').on('change', function () {
                const url = new URL($('#export-inactive').attr('href'), window.location.origin);
                url.searchParams.set('days', $(this).val());
                $('#export-inactive').attr('href', url.toString());
            });

            deleteBtn.on('click', function () {
                const ids = selectedIds();
                if (!ids.length) return;
                $('#selected-count').text(ids.length);
                passwordInput.val('');
                passwordError.hide().text('');
                modal.modal('show');
            });

            $('#cancel-delete').on('click', () => modal.modal('hide'));

            $('#confirm-delete').on('click', function () {
                const ids = selectedIds();
                const password = passwordInput.val();

                if (!password) {
                    passwordError.text('Debe ingresar su contraseña actual.').show();
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.inactive-users.destroy') }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_ids: ids,
                        admin_password: password
                    },
                    success: function (response) {
                        modal.modal('hide');
                        alert(response.message);
                        window.LaravelDataTables['inactive-users-table'].ajax.reload();
                        refreshDeleteButton();
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON?.errors;
                        const message = errors
                            ? Object.values(errors).flat().join(' ')
                            : (xhr.responseJSON?.message || 'Error al procesar la solicitud.');
                        passwordError.text(message).show();
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
