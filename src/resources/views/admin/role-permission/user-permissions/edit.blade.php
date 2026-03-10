@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.show', $user->id) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Permisos directos</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.show', $user->id) }}">{{ $user->name }}</a></div>
                <div class="breadcrumb-item">Permisos directos</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    {{-- User summary --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($user->avatar ?? 'upload/default.png') }}"
                                     class="rounded-circle mr-3"
                                     style="width: 50px; height: 50px; object-fit: cover;"
                                     alt="{{ $user->name }}">
                                <div>
                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>
                                <div class="ml-auto">
                                    @if($user->roles->count())
                                        <span class="badge badge-success">
                                            <i class="fas fa-user-tag"></i> {{ $user->roles->first()->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>
                                <i class="fas fa-key text-warning"></i>
                                Asignar permisos directos a: <strong>{{ $user->name }}</strong>
                            </h4>
                        </div>
                        <div class="card-body">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Los <strong>permisos directos</strong> se suman a los permisos heredados por el rol del usuario.
                                Úsalos para dar acceso excepcional a funciones específicas sin cambiar el rol.
                                <br>Los permisos marcados en <span class="badge badge-success">verde</span> son los que el usuario ya tiene por su rol.
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.user-permissions.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permisos disponibles</h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-warning" id="selectAllBtn">
                                            <i class="fas fa-check-double"></i> Seleccionar todos
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselectAllBtn">
                                            <i class="fas fa-times"></i> Deseleccionar todos
                                        </button>
                                    </div>
                                </div>

                                @foreach ($allPermissions as $groupName => $groupPerms)
                                    <div class="form-group permission-group mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 text-capitalize mr-3">
                                                <i class="fas fa-layer-group text-primary"></i>
                                                {{ $groupName }}
                                            </h6>
                                            <button type="button" class="btn btn-xs btn-outline-warning select-group-btn"
                                                    data-group="{{ $groupName }}">
                                                Seleccionar grupo
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-1 deselect-group-btn"
                                                    data-group="{{ $groupName }}">
                                                Deseleccionar
                                            </button>
                                        </div>
                                        <div class="row">
                                            @foreach ($groupPerms as $perm)
                                                @php
                                                    $isDirectPerm = in_array($perm->name, $directPermissions);
                                                    $isRolePerm   = in_array($perm->name, $rolePermissions);
                                                @endphp
                                                <div class="col-md-3 col-sm-6">
                                                    <label class="custom-switch mt-2 {{ $isRolePerm ? 'text-success' : '' }}"
                                                           title="{{ $isRolePerm ? 'Ya incluido en el rol' : '' }}">
                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               class="custom-switch-input permission-checkbox"
                                                               data-group="{{ $groupName }}"
                                                               value="{{ $perm->name }}"
                                                            @checked($isDirectPerm)>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description" style="font-size: 12px;">
                                                            {{ $perm->name }}
                                                            @if($isRolePerm)
                                                                <i class="fas fa-shield-alt text-success ml-1" title="Incluido en el rol" style="font-size:10px;"></i>
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr class="mt-3">
                                    </div>
                                @endforeach

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Guardar permisos directos
                                    </button>
                                    <a href="{{ route('admin.role-user.show', $user->id) }}" class="btn btn-secondary ml-2">
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
        document.getElementById('selectAllBtn').addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        });
        document.getElementById('deselectAllBtn').addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        });
        document.querySelectorAll('.select-group-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const group = this.dataset.group;
                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(cb => cb.checked = true);
            });
        });
        document.querySelectorAll('.deselect-group-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const group = this.dataset.group;
                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(cb => cb.checked = false);
            });
        });
    </script>
@endpush
