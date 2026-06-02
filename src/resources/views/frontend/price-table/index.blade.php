@extends('frontend.layouts.master')

@section('contents')
    <style>
        .price-table-download-overlay {
            align-items: center;
            background: rgba(9, 14, 24, 0.68);
            bottom: 0;
            display: none;
            justify-content: center;
            left: 0;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 9999;
        }

        .price-table-download-overlay.is-active {
            display: flex;
        }

        .price-table-download-panel {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.28);
            max-width: 360px;
            padding: 26px;
            text-align: center;
            width: calc(100% - 32px);
        }

        .price-table-download-panel p {
            color: #4f5965;
            margin: 10px 0 0;
        }
    </style>

    <section id="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-lg-9">

                    <div id="price-table-controls" class="row mb-4 align-items-end g-3" style="display: none !important;">
                        <div id="station-selector-wrapper" class="col-md-6" style="display: none;"></div>

                        <div id="date-picker-wrapper" class="col-md-4">
                            <label class="form-label fw-bold text-uppercase letter-spacing-2 small">
                                FECHA DE VIGENCIA
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <input type="date" id="input-effective-date" class="form-control form-control-lg"
                                       min="{{ Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                                       value="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                       max="{{ Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('user.price-table.pdf') }}"
                               id="download-price-pdf"
                               class="btn btn-danger btn-lg w-100 shadow-sm"
                               rel="noopener">
                                <i class="fas fa-file-pdf"></i>
                                PDF
                            </a>
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

    <div id="price-table-download-overlay" class="price-table-download-overlay" aria-hidden="true">
        <div class="price-table-download-panel" role="status" aria-live="polite">
            <div class="spinner-border text-danger" role="status" style="width: 2.75rem; height: 2.75rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="fw-bold mb-0">Generando PDF</p>
            <p>Preparando la descarga de la tabla de precios.</p>
        </div>
    </div>
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
    const downloadPdfBtn = document.getElementById('download-price-pdf');
    const downloadOverlay = document.getElementById('price-table-download-overlay');

    let stationSelectEl = null;
    let hiddenStationEl = null;
    let isDownloadingPdf = false;

    function getStationId() {
        if (stationSelectEl) return stationSelectEl.value;
        if (hiddenStationEl) return hiddenStationEl.value;
        return null;
    }

    function getEffectiveDate() {
        return datePicker ? datePicker.value : null;
    }

    function updatePdfUrl() {
        const effectiveDate = getEffectiveDate();
        const params = new URLSearchParams();

        if (effectiveDate) params.append('fecha_vigencia', effectiveDate);

        downloadPdfBtn.href = `{{ route('user.price-table.pdf') }}?${params.toString()}`;
    }

    function setPdfDownloadState(isLoading) {
        isDownloadingPdf = isLoading;
        downloadPdfBtn.classList.toggle('disabled', isLoading);
        downloadPdfBtn.setAttribute('aria-disabled', isLoading ? 'true' : 'false');

        if (datePicker) datePicker.disabled = isLoading;
        if (stationSelectEl) stationSelectEl.disabled = isLoading;

        if (downloadOverlay) {
            downloadOverlay.classList.toggle('is-active', isLoading);
            downloadOverlay.setAttribute('aria-hidden', isLoading ? 'false' : 'true');
        }
    }

    function fileNameFromDisposition(disposition) {
        if (!disposition) return null;

        const utf8Match = disposition.match(/filename\*=UTF-8''([^;]+)/i);
        if (utf8Match) return decodeURIComponent(utf8Match[1].replace(/['"]/g, ''));

        const filenameMatch = disposition.match(/filename="?([^"]+)"?/i);
        return filenameMatch ? filenameMatch[1] : null;
    }

    async function downloadPdf(event) {
        event.preventDefault();

        if (isDownloadingPdf) return;

        updatePdfUrl();
        setPdfDownloadState(true);

        try {
            const response = await fetch(downloadPdfBtn.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error('No fue posible generar el PDF.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = fileNameFromDisposition(response.headers.get('Content-Disposition'))
                || `maosa-prime-precios-combustible-${getEffectiveDate() || 'consulta'}.pdf`;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (e) {
            alert('No fue posible descargar el PDF. Intente más tarde.');
        } finally {
            setPdfDownloadState(false);
        }
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

            updatePdfUrl();
            downloadPdfBtn.addEventListener('click', downloadPdf);
            datePicker.addEventListener('change', function () {
                updatePdfUrl();
                loadPrices();
            });

            loadPrices();

        } catch (e) {
            container.innerHTML = '<p class="text-center text-danger py-4">Error de conexión. Intente más tarde.</p>';
        }
    }

    initStations();
})();
</script>
@endpush
