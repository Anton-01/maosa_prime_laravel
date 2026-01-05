@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.category.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Nuestras funciones</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.our-features.index') }}">Nuestras funciones</a></div>
                <div class="breadcrumb-item">Actualizar</div>

            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Actualizar función</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.our-features.update', $ourFeature->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">Icono <span class="text-danger">*</span></label>
                                    <div role="iconpicker" data-align="left"
                                    data-selected-class="btn-primary"
                                    data-unselected-class=""
                                    name="icon" data-icon="{{ $ourFeature->icon }}" ></div>
                                </div>

                                <div class="form-group">
                                    <label for="">Título <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ $ourFeature->title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Descripción corta <span class="text-danger">*</span></label>
                                    <textarea name="short_description" class="form-control">{{ $ourFeature->short_description }}</textarea>
                                </div>


                                <div class="form-group">
                                    <label for="">Estatus <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option @selected($ourFeature->status === 1) value="1">Activo</option>
                                        <option @selected($ourFeature->status === 0) value="0">Inactivo</option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
