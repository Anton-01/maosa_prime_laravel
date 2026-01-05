@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Secciones - Títulos</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Títulos</div>

            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusScnTtl'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Actualizado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>Secciones - Títulos</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.section-title.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <h6>Nuestras funciones:</h6>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="our_feature_title" value="{{ $title?->our_feature_title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger"></span></label>
                                    <textarea name="our_feature_sub_title" class="form-control">{{ $title?->our_feature_sub_title }}</textarea>
                                </div>

                                <hr>

                                <h6>Nuestras categorías:</h6>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="our_categories_title" value="{{ $title?->our_categories_title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger"></span></label>
                                    <textarea name="our_categories_sub_title" class="form-control">{{ $title?->our_categories_sub_title }}</textarea>
                                </div>

                                <hr>

                                <h6>Nuestras ubicaciones :</h6>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="our_location_title" value="{{ $title?->our_location_title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger"></span></label>
                                    <textarea name="our_location_sub_title" class="form-control">{{ $title?->our_location_sub_title }}</textarea>
                                </div>


                                <hr>

                                <h6>Nuestras ofertas destacadas :</h6>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="our_featured_listing_title" value="{{ $title?->our_featured_listing_title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger"></span></label>
                                    <textarea name="our_featured_listing_sub_title" class="form-control">{{ $title?->our_featured_listing_sub_title }}</textarea>
                                </div>

                                <hr>

                                <h6>Nuestro blog :</h6>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="our_blog_title" value="{{ $title?->our_blog_title }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger"></span></label>
                                    <textarea name="our_blog_sub_title" class="form-control">{{ $title?->our_blog_sub_title }}</textarea>
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
