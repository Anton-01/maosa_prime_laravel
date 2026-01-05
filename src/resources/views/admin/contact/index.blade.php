@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Contáctanos</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Contáctanos</div>

            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusContact'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Actualizado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>Actualizar contacto</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.contact.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone" value="{{ $contact?->phone }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="email" value="{{ $contact?->email }}">
                                </div>

                                <div class="form-group">
                                    <label for="">Dirección <span class="text-danger">*</span></label>
                                   <textarea name="address" class="form-control">{!! $contact?->address !!}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="">Google Maps - Link <span class="text-danger">*</span></label>
                                   <textarea name="map_link" class="form-control">{!! $contact?->map_link !!}</textarea>
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
