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
                        @if(count($stations) > 1)
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                    ESTACIONES
                                </label>
                                <select id="select-station" class="form-select form-select-lg shadow-sm">
                                    @foreach($stations as $item)
                                        <option value="{{ $item['id_estacion'] }}">
                                            {{ $item['estacion'] ?? $item['id_socio'] ?? 'Estación ' . $item['id_estacion'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @if(count($stations) === 1)
                                <input type="hidden" id="select-station" value="{{ $stations[0]['id_estacion'] }}">
                            @endif
                        @endif

                        <div class="{{ count($stations) > 1 ? 'col-md-6' : 'col-md-6' }}">
                            <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                FECHA DE VIGENCIA
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <input type="date" id="input-effective-date" class="form-control form-control-lg" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
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

    @if(count($stations) === 0)
        container.innerHTML = '<p class="text-center text-muted py-4">No tiene estaciones asignadas.</p>';
        return;
    @endif

    function getStationId() {
        const el = document.getElementById('select-station');
        return el ? el.value : null;
    }

    function getEffectiveDate() {
        const el = document.getElementById('input-effective-date');
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
        const stationId = getStationId();
        if (!stationId) return;

        const effectiveDate = getEffectiveDate();

        const params = new URLSearchParams({ estacion_id: stationId });
        if (effectiveDate) params.append('fecha_vigencia', effectiveDate);

        showLoading();

        try {
            const response = await fetch(`${priceHtmlUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            container.innerHTML = await response.text();
        } catch (e) {
            container.innerHTML = '<p class="text-center text-danger py-4">Error de conexión. Intente más tarde.</p>';
        }
    }

    const selectStation = document.getElementById('select-station');
    if (selectStation && selectStation.tagName === 'SELECT') {
        selectStation.addEventListener('change', loadPrices);
    }

    const inputDate = document.getElementById('input-effective-date');
    if (inputDate) {
        inputDate.addEventListener('change', loadPrices);
    }

    loadPrices();
})();
</script>
@endpush
