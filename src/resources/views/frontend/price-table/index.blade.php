@extends('frontend.layouts.master')

@section('contents')
    <section id="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-lg-9">

                    <div class="row mb-4 align-items-end g-3">
                        @if(count($estaciones) > 1)
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                    ESTACIONES
                                </label>
                                <select id="select-estacion" class="form-select form-select-lg shadow-sm">
                                    @foreach($estaciones as $estacion)
                                        <option value="{{ $estacion['id'] }}">
                                            {{ $estacion['nombre'] ?? $estacion['razon_social'] ?? 'Estación ' . $estacion['id'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @if(count($estaciones) === 1)
                                <input type="hidden"
                                       id="select-estacion"
                                       value="{{ $estaciones[0]['id'] }}">
                            @endif
                        @endif

                        <div class="{{ count($estaciones) > 1 ? 'col-md-6' : 'col-md-6' }}">
                            <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                FECHA DE VIGENCIA
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <input type="date"
                                       id="input-fecha-vigencia"
                                       class="form-control form-control-lg"
                                       min="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                       value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard_content" data-content-reference="price-table-content-maosa-api">
                        <div class="text-center py-5">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando precios...</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    const priceHtmlUrl = '{{ route('user.price-table.html') }}';
    const container = document.querySelector('[data-content-reference="price-table-content-maosa-api"]');

    @if(count($estaciones) === 0)
        container.innerHTML = '<p class="text-center text-muted py-4">No tiene estaciones asignadas.</p>';
        return;
    @endif

    function getEstacionId() {
        const el = document.getElementById('select-estacion');
        return el ? el.value : null;
    }

    function getFechaVigencia() {
        const el = document.getElementById('input-fecha-vigencia');
        return el ? el.value : null;
    }

    function showLoading() {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-secondary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando precios...</p>
            </div>`;
    }

    async function loadPrices() {
        const estacionId = getEstacionId();
        if (!estacionId) return;

        const fecha = getFechaVigencia();

        const params = new URLSearchParams({ estacion_id: estacionId });
        if (fecha) params.append('fecha_vigencia', fecha);

        showLoading();

        try {
            const response = await fetch(`${priceHtmlUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const html = await response.text();
            container.innerHTML = html;
        } catch (e) {
            container.innerHTML = '<p class="text-center text-danger py-4">Error de conexión. Intente más tarde.</p>';
        }
    }

    // Eventos
    const selectEstacion = document.getElementById('select-estacion');
    if (selectEstacion && selectEstacion.tagName === 'SELECT') {
        selectEstacion.addEventListener('change', loadPrices);
    }

    const inputFecha = document.getElementById('input-fecha-vigencia');
    if (inputFecha) {
        inputFecha.addEventListener('change', loadPrices);
    }

    // Carga inicial
    loadPrices();
})();
</script>
@endpush
