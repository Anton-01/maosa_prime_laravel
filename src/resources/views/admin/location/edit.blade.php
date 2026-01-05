@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.location.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Ubicación</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.location.index') }}">Ubicación</a></div>
                <div class="breadcrumb-item">Actualizar</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Actualizar ubicación</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.location.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $location->name }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Mostrar en inicio <span class="text-danger">*</span></label>
                                    <select name="show_at_home" class="form-control">
                                        <option @selected($location->show_at_home === 0) value="0">No</option>
                                        <option @selected($location->show_at_home === 1) value="1">Sí</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">Estatus <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option @selected($location->status === 1) value="1">Activo</option>
                                        <option @selected($location->status === 0) value="0">Inactivo</option>
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

@push('scripts')
    <script>
        $.uploadPreview({
            input_field: "#image-upload-2", // Default: .image-upload
            preview_box: "#image-preview-2", // Default: .image-preview
            label_field: "#image-label-2", // Default: .image-label
            label_default: "Choose File", // Default: Choose File
            label_selected: "Change File", // Default: Change File
            no_label: false, // Default: false
            success_callback: null // Default: null
        });
    </script>
@endpush
