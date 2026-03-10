@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Usuarios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item">Crear usuario</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear usuario</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.role-user.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" placeholder="Nombre completo">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="email" id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password" placeholder="Mínimo 8 caracteres">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
                                    <input type="password" id="password_confirmation"
                                           class="form-control"
                                           name="password_confirmation" placeholder="Repita la contraseña">
                                </div>

                                <div class="form-group">
                                    <label for="role">Rol <span class="text-danger">*</span></label>
                                    <select name="role" id="role"
                                            class="form-control @error('role') is-invalid @enderror">
                                        <option value="">-- Seleccionar rol --</option>
                                    @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                                {{ $role->name }}
                                            </option>
                                    @endforeach
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="is_approved">¿Aprobado?</label>
                                    <select name="is_approved" id="is_approved" class="form-control">
                                        <option value="0" @selected(old('is_approved', '0') === '0')>No</option>
                                        <option value="1" @selected(old('is_approved') === '1')>Sí</option>
                                    </select>
                                </div>


                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Crear usuario
                                    </button>
                                    <a href="{{ route('admin.role-user.index') }}" class="btn btn-secondary ml-2">
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
