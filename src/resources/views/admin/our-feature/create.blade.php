@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.category.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Funciones</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.our-features.index') }}">Nuestras funciones</a></div>
                <div class="breadcrumb-item">Crear</div>

            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear función</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.our-features.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="">Icono <span class="text-danger">*</span></label>
                                    <div role="iconpicker" data-align="left"
                                    data-selected-class="btn-primary"
                                    data-unselected-class=""
                                    name="icon" ></div>
                                </div>

                                <div class="form-group">
                                    <label for="">Título <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title">
                                </div>

                                <div class="form-group">
                                    <label for="">Descripción corta <span class="text-danger">*</span></label>
                                    <textarea name="short_description" class="form-control"></textarea>
                                </div>


                                <div class="form-group">
                                    <label for="">Estatus <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
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
