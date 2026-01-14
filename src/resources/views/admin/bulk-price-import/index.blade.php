@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.user-price.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Carga Masiva de Precios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.user-price.index') }}">Listas de Precios</a></div>
                <div class="breadcrumb-item">Carga Masiva</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-excel text-success"></i> Carga Masiva Multi-Usuario</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.bulk-price-import.layout') }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Descargar Layout de Ejemplo
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Formato del Archivo Excel</h5>
                                <ul class="mb-0">
                                    <li><strong>[USUARIO: email@ejemplo.com]</strong> - Marca el inicio de precios para un usuario</li>
                                    <li><strong>[SUCURSAL: Nombre]</strong> - (Opcional) Marca la sucursal para los precios siguientes</li>
                                    <li>Luego la fila de encabezados: <strong>TERMINAL, FLETE, MAGNA, PREMIUM, DIESEL</strong></li>
                                    <li>Finalmente las filas de datos con precios por terminal</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Importante</h5>
                                <ul class="mb-0">
                                    <li>El usuario debe existir en el sistema (se busca por email)</li>
                                    <li>El usuario debe tener acceso a tabla de precios activado</li>
                                    <li>Si la sucursal no existe, se creara automaticamente</li>
                                    <li>Al importar se desactivan las listas anteriores del usuario/sucursal</li>
                                </ul>
                            </div>

                            <form action="{{ route('admin.bulk-price-import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Fecha de Precios <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('price_date') is-invalid @enderror"
                                                   name="price_date" value="{{ old('price_date', date('Y-m-d')) }}" required>
                                            @error('price_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Archivo Excel <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror"
                                                   name="excel_file" accept=".xlsx,.xls" required>
                                            @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Formatos aceptados: .xlsx, .xls</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upload"></i> Procesar Carga Masiva
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Example visualization -->
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-eye"></i> Ejemplo de Estructura del Excel</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                    <tr style="background: #1565C0; color: white;">
                                        <td colspan="5"><strong>[USUARIO: cliente1@ejemplo.com]</strong></td>
                                    </tr>
                                    <tr style="background: #7B1FA2; color: white;">
                                        <td colspan="5"><strong>[SUCURSAL: Sucursal Norte]</strong></td>
                                    </tr>
                                    <tr style="background: #1B5E20; color: white;">
                                        <td><strong>TERMINAL</strong></td>
                                        <td style="background: #5c6bc0;"><strong>FLETE</strong></td>
                                        <td><strong>MAGNA</strong></td>
                                        <td><strong>PREMIUM</strong></td>
                                        <td><strong>DIESEL</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Valero Veracruz (RDP)</td>
                                        <td>0.50</td>
                                        <td>19.9943</td>
                                        <td>21.1387</td>
                                        <td>21.9464</td>
                                    </tr>
                                    <tr>
                                        <td>Valero Puebla (RDP)</td>
                                        <td>0.45</td>
                                        <td>20.5295</td>
                                        <td>21.4457</td>
                                        <td>22.5617</td>
                                    </tr>
                                    <tr><td colspan="5"></td></tr>
                                    <tr style="background: #7B1FA2; color: white;">
                                        <td colspan="5"><strong>[SUCURSAL: Sucursal Sur]</strong></td>
                                    </tr>
                                    <tr style="background: #1B5E20; color: white;">
                                        <td><strong>TERMINAL</strong></td>
                                        <td style="background: #5c6bc0;"><strong>FLETE</strong></td>
                                        <td><strong>MAGNA</strong></td>
                                        <td><strong>PREMIUM</strong></td>
                                        <td><strong>DIESEL</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Shell MAOSA</td>
                                        <td>0</td>
                                        <td>19.4625</td>
                                        <td>19.2873</td>
                                        <td>21.0592</td>
                                    </tr>
                                    <tr><td colspan="5"></td></tr>
                                    <tr style="background: #1565C0; color: white;">
                                        <td colspan="5"><strong>[USUARIO: cliente2@ejemplo.com]</strong></td>
                                    </tr>
                                    <tr style="background: #1B5E20; color: white;">
                                        <td><strong>TERMINAL</strong></td>
                                        <td style="background: #5c6bc0;"><strong>FLETE</strong></td>
                                        <td><strong>MAGNA</strong></td>
                                        <td><strong>PREMIUM</strong></td>
                                        <td><strong>DIESEL</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Altamira (RDP)</td>
                                        <td>0.55</td>
                                        <td>19.8867</td>
                                        <td>20.8386</td>
                                        <td>21.8722</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Note que el segundo usuario no tiene [SUCURSAL:], por lo que sus precios se cargaran sin sucursal asignada.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
