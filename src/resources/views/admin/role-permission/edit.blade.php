@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Role</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">Roles</a></div>
                <div class="breadcrumb-item">Editar rol</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar rol: <strong>{{ $role->name }}</strong></h4>
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

                            <form action="{{ route('admin.role.update', $role->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="role_name">Nombre del rol <span class="text-danger">*</span></label>
                                    <input type="text" id="role_name" class="form-control @error('role_name') is-invalid @enderror"
                                           name="role_name" value="{{ old('role_name', $role->name) }}"
                                           @if($role->name === 'Super Admin') readonly @endif>
                                    @error('role_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($role->name === 'Super Admin')
                                        <small class="text-muted"><i class="fas fa-lock"></i> El nombre del Super Admin no puede modificarse.</small>
                                    @endif
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permisos <span class="text-danger">*</span></h5>
                                    @if($role->name !== 'Super Admin')
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-success" id="selectAllBtn">
                                                <i class="fas fa-check-double"></i> Seleccionar todos
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselectAllBtn">
                                                <i class="fas fa-times"></i> Deseleccionar todos
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('permissions')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                @foreach ($permissions as $groupName => $groupPermissions)
                                    <div class="form-group permission-group mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 text-capitalize mr-3">
                                                <i class="fas fa-layer-group text-primary"></i>
                                                {{ $groupName }}
                                            </h6>
                                            @if($role->name !== 'Super Admin')
                                                <button type="button" class="btn btn-xs btn-outline-primary select-group-btn"
                                                        data-group="{{ $groupName }}">
                                                    Seleccionar grupo
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary ml-1 deselect-group-btn"
                                                        data-group="{{ $groupName }}">
                                                    Deseleccionar
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row">
                                            @foreach ($groupPermissions as $item)
                                                <div class="col-md-3 col-sm-6">
                                                    <label class="custom-switch mt-2">
                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               class="custom-switch-input permission-checkbox"
                                                               data-group="{{ $groupName }}"
                                                               value="{{ $item->name }}"
                                                               @checked(in_array($item->name, old('permissions', $rolePermissions)))
                                                               @if($role->name === 'Super Admin') disabled @endif>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">{{ $item->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr class="mt-3">
                                    </div>
                                @endforeach

                                @if($role->name !== 'Super Admin')
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Actualizar rol
                                        </button>
                                        <a href="{{ route('admin.role.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-shield-alt"></i>
                                        El rol <strong>Super Admin</strong> tiene todos los permisos de forma automática y no puede modificarse.
                                    </div>
                                @endif

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
        document.getElementById('selectAllBtn')?.addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox:not(:disabled)').forEach(cb => cb.checked = true);
        });
        document.getElementById('deselectAllBtn')?.addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox:not(:disabled)').forEach(cb => cb.checked = false);
        });

        document.querySelectorAll('.select-group-btn').forEach(btn => {
            btn.addEventListener('click', function () {
            const group = this.dataset.group;
            document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:not(:disabled)`).forEach(cb => cb.checked = true);
            });
        });

        document.querySelectorAll('.deselect-group-btn').forEach(btn => {
            btn.addEventListener('click', function () {
            const group = this.dataset.group;
            document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:not(:disabled)`).forEach(cb => cb.checked = false);
            });
        });
    </script>
@endpush
