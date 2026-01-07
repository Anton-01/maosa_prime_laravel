@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.listing.edit', $listing->id) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Gestionar Servicios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.listing.index') }}">Proveedores</a></div>
                <div class="breadcrumb-item">Servicios</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h4>Servicios para: {{ $listing->title }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Selecciona los servicios que ofrece este proveedor
                            </div>

                            <form action="{{ route('admin.listing.amenities.update', $listing->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label class="d-block mb-3">
                                        <strong>Servicios Disponibles</strong>
                                        <small class="text-muted">(Selecciona todos los que apliquen)</small>
                                    </label>

                                    <div class="row">
                                        @foreach($amenities as $amenity)
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="amenity-{{ $amenity->id }}"
                                                        name="amenities[]"
                                                        value="{{ $amenity->id }}"
                                                        {{ in_array($amenity->id, $listingAmenities) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="amenity-{{ $amenity->id }}">
                                                        <i class="{{ $amenity->icon }}" style="font-size: 20px; margin-right: 8px; color: #6777ef;"></i>
                                                        {{ $amenity->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($amenities->isEmpty())
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> No hay servicios disponibles en el sistema.
                                            <a href="{{ route('admin.amenity.create') }}">Crear un nuevo servicio</a>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Servicios
                                    </button>
                                    <a href="{{ route('admin.listing.edit', $listing->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Resumen</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Proveedor:</strong> {{ $listing->title }}</p>
                            <p class="mb-2"><strong>Servicios actuales:</strong> {{ count($listingAmenities) }}</p>
                            <p class="mb-0"><strong>Servicios totales disponibles:</strong> {{ $amenities->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
