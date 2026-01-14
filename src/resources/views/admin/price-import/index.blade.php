@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.user-price.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Importar Precios desde Excel</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.user-price.index') }}">Listas de Precios</a></div>
                <div class="breadcrumb-item">Importar</div>
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
                            <h4>Importar Archivo Excel</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.price-import.layout') }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Descargar Layout de Ejemplo
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Instrucciones</h5>
                                <ul class="mb-0">
                                    <li>Descargue el layout de ejemplo para ver el formato correcto</li>
                                    <li>El archivo debe contener las columnas: <strong>TERMINAL, FLETE, MAGNA, PREMIUM, DIESEL</strong></li>
                                    <li>La primera fila debe ser el encabezado</li>
                                    <li>Los precios deben ser numericos con hasta 4 decimales</li>
                                    <li>El <strong>FLETE</strong> es el costo de envio por terminal (puede ser 0)</li>
                                    <li>Al importar se desactivaran las listas de precios anteriores del usuario (y sucursal si aplica)</li>
                                </ul>
                            </div>

                            <form action="{{ route('admin.price-import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Usuario <span class="text-danger">*</span></label>
                                            <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                                <option value="">Seleccionar usuario...</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Sucursal <small class="text-muted">(Opcional)</small></label>
                                            <select name="user_branch_id" id="user_branch_id" class="form-control @error('user_branch_id') is-invalid @enderror">
                                                <option value="">Sin sucursal</option>
                                            </select>
                                            <small class="text-muted" id="branch-help">Seleccione un usuario para ver sus sucursales</small>
                                            @error('user_branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha de Precios <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('price_date') is-invalid @enderror"
                                                   name="price_date" value="{{ old('price_date', date('Y-m-d')) }}" required>
                                            @error('price_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Archivo Excel <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('excel_file') is-invalid @enderror"
                                           name="excel_file" accept=".xlsx,.xls" required>
                                    @error('excel_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formatos aceptados: .xlsx, .xls</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Importar Precios
                                    </button>
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
        // Load branches when user changes
        $('#user_id').on('change', function() {
            const userId = $(this).val();
            const branchSelect = $('#user_branch_id');
            const branchHelp = $('#branch-help');

            branchSelect.html('<option value="">Sin sucursal</option>');

            if (userId) {
                $.ajax({
                    url: '{{ route("admin.user-branch.get-branches") }}',
                    data: { user_id: userId },
                    success: function(branches) {
                        if (branches.length > 0) {
                            branches.forEach(function(branch) {
                                branchSelect.append(`<option value="${branch.id}">${branch.name}</option>`);
                            });
                            branchHelp.text('Seleccione una sucursal o deje vacio');
                        } else {
                            branchHelp.text('Este usuario no tiene sucursales');
                        }
                    }
                });
            } else {
                branchHelp.text('Seleccione un usuario para ver sus sucursales');
            }
        });
    </script>
@endpush
