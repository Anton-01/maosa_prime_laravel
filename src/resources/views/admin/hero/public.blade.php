@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Baner - invitado</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Actualizar baner</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusHeroPublic'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Actualizado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h4>Actualizar baner</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.hero.public.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">Imagen de fondo</label>
                                    <div id="image-preview" class="image-preview">
                                        <label for="image-upload" id="image-label">Selecciona imagen</label>
                                        <input type="file" name="background" id="image-upload" />
                                        <input type="hidden" name="old_background" value="{{ @$hero->background }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Título <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ @$hero->title }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Sub título <span class="text-danger">*</span></label>
                                    <textarea name="sub_title" class="form-control">{{ @$hero->sub_title }}</textarea>
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
        $(document).ready(function() {
            $('.image-preview').css({
                'background-image': 'url({{ asset(@$hero->background) }})',
                'background-size': 'cover',
                'background-position': 'center center'
            });
        })
    </script>
@endpush
