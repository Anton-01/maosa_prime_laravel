@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Importar Usuarios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item">Importar</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-upload"></i> Cargar Archivo Excel</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Instrucciones:</h5>
                                <ul class="mb-0">
                                    <li>El archivo debe ser formato Excel (.xlsx o .xls)</li>
                                    <li>La primera fila debe contener los encabezados: <strong>NOMBRE</strong> y <strong>EMAIL</strong></li>
                                    <li>Cada fila representa un usuario a importar</li>
                                    <li>El email debe ser único (no duplicados en el sistema)</li>
                                    <li>Los usuarios creados tendrán:
                                        <ul>
                                            <li>Rol: <strong>User</strong></li>
                                            <li>Estado: <strong>Aprobado</strong></li>
                                            <li>Contraseña: <strong>Generada automáticamente</strong> (12 caracteres con mayúsculas, minúsculas, números y símbolos)</li>
                                        </ul>
                                    </li>
                                    <li>Al finalizar, podrá descargar un archivo TXT con los resultados, incluyendo las contraseñas generadas</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <a href="{{ route('admin.user-import.layout') }}" class="btn btn-outline-success">
                                    <i class="fas fa-download"></i> Descargar Layout de Ejemplo
                                </a>
                            </div>

                            <form action="{{ route('admin.user-import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="excel_file"><strong>Archivo Excel</strong> <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                                        <label class="custom-file-label" for="excel_file">Seleccionar archivo...</label>
                                    </div>
                                    @error('excel_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Formatos aceptados: .xlsx, .xls</small>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upload"></i> Importar Usuarios
                                    </button>
                                    <a href="{{ route('admin.role-user.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Update file input label with selected filename
        $(document).ready(function() {
            $('#excel_file').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo...');
            });
        });
    </script>
@endpush
