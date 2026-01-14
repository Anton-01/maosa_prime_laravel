@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.bulk-price-import.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Resultado de Carga Masiva</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.user-price.index') }}">Listas de Precios</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.bulk-price-import.index') }}">Carga Masiva</a></div>
                <div class="breadcrumb-item">Resultado</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-clipboard-list"></i> Resumen de Importacion</h4>
                            <div class="card-header-action">
                                @if(count($results) > 0)
                                    <a href="{{ route('admin.bulk-price-import.download') }}" class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Descargar Resultados
                                    </a>
                                @endif
                                <a href="{{ route('admin.bulk-price-import.index') }}" class="btn btn-primary ml-2">
                                    <i class="fas fa-upload"></i> Nueva Carga
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(!empty($importDate))
                                <p class="text-muted">Importacion realizada el: <strong>{{ $importDate }}</strong></p>
                            @endif

                            @php
                                $successCount = collect($results)->where('status', 'success')->count();
                                $errorCount = collect($results)->where('status', 'error')->count();
                            @endphp

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-primary">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Total Procesados</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ count($results) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-success">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Exitosos</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ $successCount }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-danger">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Con Errores</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ $errorCount }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(count($results) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Email</th>
                                            <th>Usuario</th>
                                            <th>Sucursal</th>
                                            <th>Terminales</th>
                                            <th>Estado</th>
                                            <th>Mensaje</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($results as $index => $result)
                                            <tr class="{{ $result['status'] == 'success' ? 'table-success' : 'table-danger' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $result['email'] }}</td>
                                                <td>{{ $result['user_name'] ?? '-' }}</td>
                                                <td>
                                                    {{ $result['branch'] ?? 'Sin sucursal' }}
                                                    @if(isset($result['branch_created']) && $result['branch_created'])
                                                        <span class="badge badge-info">Nueva</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $result['items_count'] }}</td>
                                                <td class="text-center">
                                                    @if($result['status'] == 'success')
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Exitoso</span>
                                                    @else
                                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Error</span>
                                                    @endif
                                                </td>
                                                <td>{{ $result['message'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No hay resultados para mostrar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
