@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.listing.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Proveedores</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.listing.index') }}">Proveedores</a></div>
                <div class="breadcrumb-item">Crear</div>

            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear Proveedor</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.listing.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Imagen de Perfil <span class="text-danger">*</span></label>
                                            <div id="image-preview" class="image-preview">
                                                <label for="image-upload" id="image-label">Seleccionar archivo</label>
                                                <input type="file" name="image" id="image-upload" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Imagen de fondo <span class="text-danger">*</span></label>
                                            <div id="image-preview-2" class="image-preview">
                                                <label for="image-upload-2" id="image-label-2">Seleccionar archivo</label>
                                                <input type="file" name="thumbnail_image" id="image-upload-2" required />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Nombre Comercial / Razón Social <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Categoría <span class="text-danger">*</span></label>
                                            <select class="form-control" name="category" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Ubicación <span class="text-danger">*</span></label>
                                            <select class="form-control" name="location" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Dirección <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Teléfono <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="phone" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Correo electrónico <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="email" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Sitio web <span class="text-danger"></span></label>
                                            <input type="text" class="form-control" name="website">
                                        </div>
                                    </div>
                                </div>

                                {{-- Social Links Manager --}}
                                @include('admin.listing.partials.social-links-manager')

                                <div class="form-group">
                                    <label for="">Descripción general <span class="text-danger">*</span></label>
                                    <textarea name="description" class="summernote" required ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="">Google Maps - Embed Code <span class="text-danger">(size: 1000x400)</span></label>
                                    <textarea name="google_map_embed_code" class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="">Título SEO <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="seo_title">
                                </div>

                                <div class="form-group">
                                    <label for="">Descripción SEO <span class="text-danger"></span></label>
                                    <textarea name="seo_description" class="form-control"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Estatus <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">¿Es destacado? <span class="text-danger"></span></label>
                                            <select name="is_featured" class="form-control" required>
                                                <option value="0">No</option>
                                                <option value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">¿Está verificado?<span class="text-danger"></span></label>
                                            <select name="is_verified" class="form-control" required>
                                                <option value="0">No</option>
                                                <option value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">¿Es previligiado?<span class="text-danger"></span></label>
                                            <select name="is_previliged" class="form-control" required>
                                                <option value="0">No</option>
                                                <option value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
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
    <script src="{{ asset('admin/assets/js/social-links-manager.js') }}"></script>
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
