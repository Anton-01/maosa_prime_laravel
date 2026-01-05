@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Nuestras funciones</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Nuestras funciones</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('statusCdFeature'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            ¡Creado correctamente!
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session()->has('statusUpFeature'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                ¡Actualizado correctamente!
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>Funciones</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.our-features.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create</a>
                            </div>
                        </div>
                        <div class="card-body">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
