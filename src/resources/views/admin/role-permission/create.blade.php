@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.role.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Roles</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">Roles</a></div>
                <div class="breadcrumb-item">Crear rol</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear Rol</h4>
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

                            <form action="{{ route('admin.role.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="role_name">Nombre del rol <span class="text-danger">*</span></label>
                                    <input type="text" id="role_name" class="form-control @error('role_name') is-invalid @enderror"
                                           name="role_name" value="{{ old('role_name') }}" placeholder="Ej: Vendedor, Editor, Supervisor...">
                                    @error('role_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permisos <span class="text-danger">*</span></h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-success" id="selectAllBtn">
                                            <i class="fas fa-check-double"></i> Seleccionar todos
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselectAllBtn">
                                            <i class="fas fa-times"></i> Deseleccionar todos
                                        </button>
                                    </div>

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
                                            <button type="button" class="btn btn-xs btn-outline-primary select-group-btn"
                                                    data-group="{{ $groupName }}">
                                                Seleccionar grupo
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-1 deselect-group-btn"
                                                    data-group="{{ $groupName }}">
                                                Deseleccionar
                                            </button>
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
                                                            @checked(is_array(old('permissions')) && in_array($item->name, old('permissions')))>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">{{ $item->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr class="mt-3">
                                    </div>
                                @endforeach

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Crear rol
                                    </button>
                                    <a href="{{ route('admin.role.index') }}" class="btn btn-secondary ml-2">
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
        // Select / deselect all permissions
        document.getElementById('selectAllBtn').addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        });
        document.getElementById('deselectAllBtn').addEventListener('click', function () {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        });

        // Select / deselect by group
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
