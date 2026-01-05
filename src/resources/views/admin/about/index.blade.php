@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Nosotros</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Nosotros</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusAbout'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Actualizado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>Actualizar sobre nosotros</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.about-us.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Imagen <span class="text-danger">*</span></label>
                                        <div id="image-preview" class="image-preview">
                                            <label for="image-upload" id="image-label">Choose File</label>
                                            <input type="file" name="image" id="image-upload" />
                                            <input type="hidden" name="old_image" value="{{ $about?->image }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Video Url <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="video_url" value="{{ $about?->video_url }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Descripción <span class="text-danger">*</span></label>
                                    <textarea name="description" class="summernote">{!! $about?->description !!}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="">Butón url <span class="text-danger"></span></label>
                                    <input type="text" class="form-control" name="button_url" value="{{ $about?->button_url }}">
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
                'background-image': 'url({{ asset($about?->image) }})',
                'background-size': 'cover',
                'background-position': 'center center'
            });
        })
    </script>
@endpush
