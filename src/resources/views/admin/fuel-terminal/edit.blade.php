@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.fuel-terminal.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Terminal de Combustible</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.fuel-terminal.index') }}">Terminales</a></div>
                <div class="breadcrumb-item">Editar</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar Terminal</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.fuel-terminal.update', $terminal->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name', $terminal->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="">Código</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           name="code" value="{{ old('code', $terminal->code) }}">
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="">Estatus <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $terminal->status ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ !$terminal->status ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
