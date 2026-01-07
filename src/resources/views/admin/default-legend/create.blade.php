@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.default-legend.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Leyenda por Defecto</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.default-legend.index') }}">Leyendas</a></div>
                <div class="breadcrumb-item">Crear</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear Leyenda</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.default-legend.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="">Texto de Leyenda <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('legend_text') is-invalid @enderror"
                                              name="legend_text" rows="3" required>{{ old('legend_text') }}</textarea>
                                    @error('legend_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="">Orden <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                           name="sort_order" value="{{ old('sort_order', 0) }}" min="0" required>
                                    @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="">Estatus <span class="text-danger">*</span></label>
                                    <select name="is_active" class="form-control">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Crear</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
