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

                        <!-- Servicios Actuales -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Servicios Asignados para: {{ $listing->title }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Estos son los servicios actualmente asignados a este proveedor
                            </div>

                            <div id="assigned-amenities-container">
                                @if($assignedAmenities->count() > 0)
                                    <div class="row">
                                        @foreach($assignedAmenities as $amenity)
                                            <div class="col-md-4 col-sm-6 mb-3" data-amenity-id="{{ $amenity->id }}">
                                                <div class="card amenity-card">
                                                    <div class="card-body d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="{{ $amenity->icon }}" style="font-size: 24px; margin-right: 10px; color: #6777ef;"></i>
                                                            <span class="font-weight-bold">{{ $amenity->name }}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-danger remove-amenity" data-amenity-id="{{ $amenity->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning" id="no-amenities-message">
                                        <i class="fas fa-exclamation-triangle"></i> No hay servicios asignados a este proveedor.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                        <!-- Agregar Servicios -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Agregar Servicios</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Busca y selecciona servicios existentes, o crea uno nuevo si no existe
                                </div>
                                <div class="form-group">
                                    <label><strong>Seleccionar Servicio Existente</strong></label>
                                    <select class="form-control select2" id="amenity-select" style="width: 100%;">
                                        <option value="">-- Buscar servicio --</option>
                                        @foreach($availableAmenities as $amenity)
                                            <option value="{{ $amenity->id }}" data-icon="{{ $amenity->icon }}">
                                                {{ $amenity->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="button" class="btn btn-success" id="add-amenity-btn">
                                        <i class="fas fa-plus"></i> Agregar Servicio Seleccionado
                                    </button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAmenityModal">
                                        <i class="fas fa-sparkles"></i> Crear Nuevo Servicio
                                    </button>
                                </div>
                            </div>
                        </div>

                    <!-- Summary Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Resumen</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Proveedor:</strong> {{ $listing->title }}</p>
                            <p class="mb-2"><strong>Servicios actuales:</strong> <span id="current-count">{{ $assignedAmenities->count() }}</span></p>
                            <p class="mb-0"><strong>Servicios disponibles:</strong> <span id="available-count">{{ $availableAmenities->count() }}</span></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <!-- Create Amenity Modal -->
    <div class="modal fade" id="createAmenityModal" tabindex="-1" role="dialog" aria-labelledby="createAmenityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAmenityModalLabel">Crear Nuevo Servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="create-amenity-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Servicio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new-amenity-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>Seleccionar Icono <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-primary btn-block" id="icon-picker-btn">
                                <i class="fas fa-icons"></i> Seleccionar Icono
                            </button>
                            <input type="hidden" id="selected-icon" name="icon" required>
                            <div id="selected-icon-preview" class="mt-3 text-center" style="display: none;">
                                <i id="preview-icon" style="font-size: 48px; color: #6777ef;"></i>
                                <p class="mt-2">Icono seleccionado: <code id="icon-class-name"></code></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear y Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#amenity-select').select2({
                placeholder: 'Buscar servicio...',
                allowClear: true,
                templateResult: formatAmenity,
                templateSelection: formatAmenitySelection
            });
            function formatAmenity(amenity) {
                if (!amenity.id) return amenity.text;
                var icon = $(amenity.element).data('icon');
                return $('<span><i class="' + icon + '" style="margin-right: 8px;"></i>' + amenity.text + '</span>');
            }
            function formatAmenitySelection(amenity) {
                return amenity.text;
            }
            // Initialize Icon Picker
            $('#icon-picker-btn').iconpicker({
                align: 'center',
                arrowClass: 'btn-primary',
                arrowPrevIconClass: 'fas fa-angle-left',
                arrowNextIconClass: 'fas fa-angle-right',
                cols: 10,
                footer: true,
                header: true,
                icon: 'fas fa-bomb',
                iconset: 'fontawesome5',
                labelHeader: '{0} de {1} páginas',
                labelFooter: '{0} - {1} de {2} iconos',
                placement: 'bottom',
                rows: 5,
                search: true,
                searchText: 'Buscar icono...',
                selectedClass: 'btn-success',
                unselectedClass: ''
            }).on('change', function(e) {
                var icon = e.icon;
                $('#selected-icon').val(icon);
                $('#preview-icon').attr('class', icon);
                $('#icon-class-name').text(icon);
                $('#selected-icon-preview').show();
            });

            // Fix iconpicker search input inside modal - prevent Bootstrap from stealing focus
            $('#createAmenityModal').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });

            $(document).on('click', '.iconpicker-popover .iconpicker-search input', function(e) {
                e.stopPropagation();
                $(this).focus();
            });

            $(document).on('focus', '.iconpicker-popover .iconpicker-search input', function(e) {
                e.stopPropagation();
            });

            // Add amenity
            $('#add-amenity-btn').click(function() {
                var amenityId = $('#amenity-select').val();
                if (!amenityId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor selecciona un servicio'
                    });
                    return;
                }
                $.ajax({
                    url: '{{ route('admin.listing.amenities.add', $listing->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amenity_id: amenityId,
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            addAmenityToUI(response.amenity);
                            $('#amenity-select option[value="' + amenityId + '"]').remove();
                            $('#amenity-select').val('').trigger('change');
                            updateCounts();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Error al agregar el servicio';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });
            // Remove amenity
            $(document).on('click', '.remove-amenity', function() {
                const amenityId = $(this).data('amenity-id');
                const card = $(this).closest('[data-amenity-id]');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se eliminará este servicio del proveedor",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.listing.amenities.remove', ['listing_id' => $listing->id, 'amenity_id' => '__ID__']) }}'.replace('__ID__', amenityId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    card.fadeOut(300, function() {
                                        $(this).remove();
                                        updateCounts();
                                        checkEmptyState();
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Eliminado!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            }
                        });
                    }
                });
            });
            // Create new amenity
            $('#create-amenity-form').submit(function(e) {
                e.preventDefault();
                const name = $('#new-amenity-name').val();
                const icon = $('#selected-icon').val();

                if (!name || !icon) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor completa todos los campos'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.listing.amenities.create', $listing->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name,
                        icon: icon
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            addAmenityToUI(response.amenity);
                            $('#createAmenityModal').modal('hide');
                            $('#create-amenity-form')[0].reset();
                            $('#selected-icon-preview').hide();
                            updateCounts();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Error al crear el servicio';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });
            function addAmenityToUI(amenity) {
                $('#no-amenities-message').remove();
                const html = `
                    <div class="col-md-4 col-sm-6 mb-3" data-amenity-id="${amenity.id}" style="display: none;">
                        <div class="card amenity-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="${amenity.icon}" style="font-size: 24px; margin-right: 10px; color: #6777ef;"></i>
                                    <span class="font-weight-bold">${amenity.name}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-amenity" data-amenity-id="${amenity.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                if ($('#assigned-amenities-container .row').length === 0) {
                    $('#assigned-amenities-container').html('<div class="row"></div>');
                }
                $('#assigned-amenities-container .row').append(html);
                $('[data-amenity-id="' + amenity.id + '"]').fadeIn(300);
            }
            function updateCounts() {
                const currentCount = $('#assigned-amenities-container [data-amenity-id]').length;
                const availableCount = $('#amenity-select option').length - 1; // -1 for placeholder
                $('#current-count').text(currentCount);
                $('#available-count').text(availableCount);
            }
            function checkEmptyState() {
                if ($('#assigned-amenities-container [data-amenity-id]').length === 0) {
                    $('#assigned-amenities-container').html(`
                        <div class="alert alert-warning" id="no-amenities-message">
                            <i class="fas fa-exclamation-triangle"></i> No hay servicios asignados a este proveedor.
                        </div>
                    `);
                }
            }
        });
    </script>
    <style>
        .amenity-card {
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
        }
        .amenity-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 4px 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* Fix iconpicker inside modal */
        .iconpicker-popover {
            z-index: 1060 !important;
        }
        .iconpicker-popover .popover-body {
            max-height: 300px;
            overflow-y: auto;
        }
        .iconpicker-popover .iconpicker-search {
            position: relative;
            z-index: 1061;
        }
        .iconpicker-popover .iconpicker-search input {
            pointer-events: auto !important;
        }
    </style>
@endpush
