@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role-user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Detalle de usuario</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role-user.index') }}">Usuarios</a></div>
                <div class="breadcrumb-item">{{ $user->name }}</div>
            </div>
        </div>

        <div class="section-body">

            @if(session()->has('statusPermU'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Permisos directos actualizados correctamente.
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                {{-- User Info Card --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ asset($user->avatar ?? 'upload/default.png') }}"
                                 class="rounded-circle mb-3"
                                 style="width: 80px; height: 80px; object-fit: cover;"
                                 alt="{{ $user->name }}">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-2">{{ $user->email }}</p>

                            <div class="mb-3">
                                @if($user->is_approved)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Aprobado</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> No aprobado</span>
                                @endif

                                @if($user->can_view_price_table)
                                    <span class="badge badge-info ml-1"><i class="fas fa-table"></i> Acceso precios</span>
                                @endif
                            </div>

                            <hr>

                            <div class="text-left">
                                <p class="mb-1">
                                    <strong>Tipo:</strong>
                                    {{ $user->user_type === 'admin' ? 'Administrador' : 'Usuario' }}
                                </p>
                                <p class="mb-1">
                                    <strong>Rol:</strong>
                                    @if($user->roles->count())
                                        <span class="badge badge-success">{{ $user->roles->first()->name }}</span>
                                    @else
                                        <span class="text-muted">Sin rol</span>
                                    @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Registro:</strong>
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </p>
                                @if($user->branches->count())
                                    <p class="mb-1">
                                        <strong>Sucursales:</strong>
                                        {{ $user->branches->pluck('name')->implode(', ') }}
                                    </p>
                                @endif
                            </div>

                            <hr>

                            <div class="d-flex justify-content-center flex-wrap gap-1">
                                @can('access management users update')
                                    <a href="{{ route('admin.role-user.edit', $user->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                @endcan
                                @can('access management users permissions')
                                    <a href="{{ route('admin.user-permissions.edit', $user->id) }}" class="btn btn-sm btn-warning mb-1">
                                        <i class="fas fa-key"></i> Permisos directos
                                    </a>
                                @endcan
                                <a href="{{ route('admin.statistics.show', $user->id) }}" class="btn btn-sm btn-info mb-1">
                                    <i class="fas fa-chart-bar"></i> Estadísticas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permissions Summary --}}
                <div class="col-md-8">
                    {{-- Direct permissions --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                <i class="fas fa-key text-warning"></i>
                                Permisos directos
                                <span class="badge badge-warning ml-2">{{ $directPermissions->count() }}</span>
                            </h4>
                            <div class="card-header-action">
                                @can('access management users permissions')
                                    <a href="{{ route('admin.user-permissions.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Gestionar
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            @if($directPermissions->count())
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($directPermissions->groupBy('group_name') as $group => $perms)
                                        <div class="mb-2 w-100">
                                            <strong class="text-capitalize text-muted" style="font-size: 12px;">{{ $group }}</strong>
                                            <div class="mt-1">
                                                @foreach($perms as $perm)
                                                    <span class="badge badge-warning mr-1 mb-1">{{ $perm->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    Este usuario no tiene permisos directos asignados. Los permisos se heredan de su rol.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Role permissions --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                <i class="fas fa-shield-alt text-success"></i>
                                Permisos heredados del rol
                                @if($user->roles->count())
                                    <span class="badge badge-success ml-2">{{ $user->roles->first()->name }}</span>
                                @endif
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($rolePermissions->count())
                                @foreach($rolePermissions->groupBy('group_name') as $group => $perms)
                                    <div class="mb-3">
                                        <strong class="text-capitalize text-muted" style="font-size: 12px;">{{ $group }}</strong>
                                        <div class="mt-1">
                                            @foreach($perms as $perm)
                                                <span class="badge badge-success mr-1 mb-1">{{ $perm->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    Sin permisos por rol.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- All effective permissions --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                <i class="fas fa-list-check text-primary"></i>
                                Permisos efectivos totales
                                <span class="badge badge-primary ml-2">{{ $allPermissions->flatten()->count() }}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($allPermissions->count())
                                @foreach($allPermissions as $group => $perms)
                                    <div class="mb-3">
                                        <strong class="text-capitalize text-muted d-block mb-1" style="font-size: 12px;">
                                            <i class="fas fa-layer-group text-primary"></i> {{ $group }}
                                        </strong>
                                        @foreach($perms as $perm)
                                            @php $isDirect = $directPermissions->contains('name', $perm->name); @endphp
                                            <span class="badge {{ $isDirect ? 'badge-warning' : 'badge-primary' }} mr-1 mb-1"
                                                  title="{{ $isDirect ? 'Permiso directo' : 'Heredado del rol' }}">
                                                {{ $perm->name }}
                                                @if($isDirect)<i class="fas fa-key ml-1" style="font-size:9px;"></i>@endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endforeach
                                <p class="text-muted mt-2 mb-0" style="font-size: 12px;">
                                    <span class="badge badge-warning">Amarillo</span> = permiso directo &nbsp;
                                    <span class="badge badge-primary">Azul</span> = heredado del rol
                                </p>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                    Este usuario no tiene ningún permiso efectivo.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
