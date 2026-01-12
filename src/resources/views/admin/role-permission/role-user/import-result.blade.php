@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Resultado de Importación</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item">Resultado</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-alt"></i> Reporte de Importación</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.user-import.download') }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Descargar Reporte (.txt)
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Importante:</strong> Este reporte contiene las contraseñas generadas para los usuarios.
                                Descargue el archivo antes de salir de esta página, ya que las contraseñas no se podrán recuperar después.
                            </div>

                            <div class="form-group">
                                <label><strong>Vista previa del reporte:</strong></label>
                                <textarea class="form-control" rows="25" readonly style="font-family: monospace; font-size: 12px; background-color: #f8f9fa;">{{ $content }}</textarea>
                            </div>

                            <div class="form-group mt-4">
                                <a href="{{ route('admin.user-import.download') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-download"></i> Descargar Reporte (.txt)
                                </a>
                                <a href="{{ route('admin.role-user.index') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-users"></i> Ver Lista de Usuarios
                                </a>
                                <a href="{{ route('admin.user-import.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-upload"></i> Nueva Importación
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
