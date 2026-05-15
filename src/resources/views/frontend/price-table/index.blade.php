@extends('frontend.layouts.master')

@section('contents')
    <section id="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-lg-9">

                    <div id="price-table-controls" class="row mb-4 align-items-end g-3" style="display: none !important;">
                        <div id="station-selector-wrapper" class="col-md-6" style="display: none;"></div>

                        <div id="date-picker-wrapper" class="col-md-6">
                            <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                FECHA DE VIGENCIA
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <input type="date" id="input-effective-date" class="form-control form-control-lg" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="dashboard_content" data-content-reference="price-table-content-maosa-api">
                        <div class="text-center py-5 px-4 rounded-3" style="background: rgba(20, 20, 30, 0.38);">
                            <div>
                                <div class="spinner-border text-light" role="status" style="width: 2.5rem; height: 2.5rem;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <p class="mt-3 text-white fw-semibold mb-1">Cargando tu configuración de precios asignada...</p>
                            <p class="text-white-50 small mb-0">Esto puede tomar unos segundos.</p>
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
    const stationsUrl   = '{{ route('user.price-table.stations') }}';
    const priceHtmlUrl  = '{{ route('user.price-table.html') }}';
    const logoUrl       = '{{ config('settings.logo') }}';
    const container     = document.querySelector('[data-content-reference="price-table-content-maosa-api"]');
    const controls      = document.getElementById('price-table-controls');
    const stationWrapper = document.getElementById('station-selector-wrapper');
    const datePicker    = document.getElementById('input-effective-date');

    let stationSelectEl = null;
    let hiddenStationEl = null;

    function getStationId() {
        if (stationSelectEl) return stationSelectEl.value;
        if (hiddenStationEl) return hiddenStationEl.value;
        return null;
    }

    function getEffectiveDate() {
        return datePicker ? datePicker.value : null;
    }

    function showContentLoader(stationName) {
        const label = stationName
            ? `Cargando precios de <strong>${stationName}</strong>...`
            : 'Cargando datos de precios...';

        container.innerHTML = `
            <div class="text-center py-5 px-4 rounded-3" style="background: rgba(20, 20, 30, 0.38);">
                <div>
                    <div class="spinner-border text-light" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                <p class="mt-3 text-white fw-semibold mb-0">${label}</p>
            </div>`;
    }

    async function loadPrices() {
        const stationId = getStationId();
        if (!stationId) return;

        const stationName = stationSelectEl
            ? stationSelectEl.options[stationSelectEl.selectedIndex]?.text
            : null;

        showContentLoader(stationName);

        const effectiveDate = getEffectiveDate();
        const params = new URLSearchParams({ estacion_id: stationId });
        if (effectiveDate) params.append('fecha_vigencia', effectiveDate);

        try {
            const response = await fetch(`${priceHtmlUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            container.innerHTML = await response.text();
        } catch (e) {
            container.innerHTML = '<p class="text-center text-danger py-4">Error de conexión. Intente más tarde.</p>';
        }

    }

    function buildStationSelect(stations) {
        const select = document.createElement('select');
        select.id = 'select-station';
        select.className = 'form-select form-select-lg shadow-sm';

        stations.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.id_estacion;
            opt.textContent = item.estacion ?? item.id_socio ?? ('Estación ' + item.id_estacion);
            select.appendChild(opt);
        });

        return select;
    }

    async function initStations() {
        try {
            const resp = await fetch(stationsUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!resp.ok) {
                container.innerHTML = '<p class="text-center text-danger py-4">Error al obtener la configuración de precios. Intente más tarde.</p>';
                return;
            }

            const stations = await resp.json();

            if (!stations || stations.length === 0) {
                container.innerHTML = '<p class="text-center text-muted py-4">No tiene estaciones asignadas.</p>';
                return;
            }

            controls.style.removeProperty('display');

            if (stations.length > 1) {
                const label = document.createElement('label');
                label.className = 'form-label fw-bold text-uppercase letter-spacing-2 small';
                label.textContent = 'ESTACIONES';

                stationSelectEl = buildStationSelect(stations);
                stationSelectEl.addEventListener('change', loadPrices);

                stationWrapper.appendChild(label);
                stationWrapper.appendChild(stationSelectEl);
                stationWrapper.style.removeProperty('display');
            } else {
                hiddenStationEl = document.createElement('input');
                hiddenStationEl.type = 'hidden';
                hiddenStationEl.id = 'select-station';
                hiddenStationEl.value = stations[0].id_estacion;
                stationWrapper.appendChild(hiddenStationEl);
            }

            datePicker.addEventListener('change', loadPrices);

            loadPrices();

        } catch (e) {
            container.innerHTML = '<p class="text-center text-danger py-4">Error de conexión. Intente más tarde.</p>';
        }
    }

    initStations();
})();
</script>
@endpush
