{{--
    Toggle "MOSTRAR TABLA DE PRECIOS" + select de estaciones activas.
    Variables: $estaciones (Collection), $user (opcional, en edición).
--}}
@php
    $priceTableOn = (bool) old('can_view_price_table', isset($user) ? $user->can_view_price_table : false);
    $selectedEstacion = old('id_estacion', isset($user) ? $user->id_estacion : null);
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="d-block text-uppercase font-weight-bold" for="can_view_price_table">Mostrar tabla de precios</label>
            <input type="hidden" name="can_view_price_table" value="0">
            <label class="price-table-switch mb-0">
                <input type="checkbox" name="can_view_price_table" id="can_view_price_table" value="1" @checked($priceTableOn)>
                <span class="price-table-slider"></span>
            </label>
            <small class="form-text text-muted">
                Al activarlo podrá asignar la estación cuya tabla de precios verá el usuario.
            </small>
        </div>
    </div>
    <div class="col-md-6" id="estacion-wrapper" @if(!$priceTableOn) style="display: none" @endif>
        <div class="form-group">
            <label for="id_estacion">Estación <small class="text-muted">(solo estaciones activas)</small></label>
            <select name="id_estacion" id="id_estacion"
                    class="form-control @error('id_estacion') is-invalid @enderror"
                    data-placeholder="-- Buscar y seleccionar estación --">
                <option value=""></option>
                @foreach ($estaciones as $estacion)
                    <option value="{{ $estacion->id_estacion }}" @selected((string) $selectedEstacion === (string) $estacion->id_estacion)>
                        {{ $estacion->estacion }}
                    </option>
                @endforeach
            </select>
            @error('id_estacion')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @if ($estaciones->isEmpty())
                <small class="form-text text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No hay estaciones activas disponibles en el catálogo (maosa_internal.cat_usuarios_importado).
                </small>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Switch estilo toggle para "Mostrar tabla de precios" */
        .price-table-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 26px;
            vertical-align: middle;
        }
        .price-table-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .price-table-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #dee2e6;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }
        .price-table-slider::before {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background-color: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .price-table-switch input:checked + .price-table-slider {
            background-color: #198754;
        }
        .price-table-switch input:checked + .price-table-slider::before {
            transform: translateX(26px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function () {
            const $toggle = $('#can_view_price_table');
            const $wrapper = $('#estacion-wrapper');
            const $select = $('#id_estacion');

            // Select de selección única con buscador
            $select.select2({
                width: '100%',
                allowClear: true,
                placeholder: $select.data('placeholder')
            });

            $toggle.on('change', function () {
                if (this.checked) {
                    $wrapper.slideDown(150);
                } else {
                    $wrapper.slideUp(150);
                    $select.val(null).trigger('change');
                }
            });
        });
    </script>
@endpush
