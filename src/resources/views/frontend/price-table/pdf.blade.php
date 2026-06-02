<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Precios - {{ $effectiveDate->format('Y-m-d') }}</title>
    @if(!empty($tablePricesCss))
        <style>
            {!! $tablePricesCss !!}
        </style>
    @endif
    <style>
        @page {
            margin: 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #202124;
            /* El fondo del body puede seguir siendo blanco, la marca de agua va encima pero detrás del texto */
            background: #ffffff;
        }

        #watermark-fondo {
            position: fixed;
            top: 15%;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 1000;
        }

        #watermark-fondo img {
            width: 800px; /* Tamaño estático del logo */
            opacity: 0.35; /* Nivel de transparencia */
            margin: 0 auto;
        }

        /* ==========================================================================
           2. FORZAR TRANSPARENCIA EN EL CONTENIDO DE LA API
           ========================================================================== */
        .station-content,
        .station-content .precio-layout,
        .station-content table,
        .station-content tr,
        .station-content td,
        .station-content th {
            background: transparent !important;
            background-color: transparent !important;
        }

        /* Estilos originales de tu documento */
        .document-header {
            border-bottom: 2px solid #1f5f3a;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        .document-title {
            color: #1f5f3a;
            font-size: 19px;
            font-weight: bold;
            margin: 0 0 6px;
        }

        .document-meta {
            color: #4f5965;
            line-height: 1.45;
            margin: 0;
        }

        .notice {
            background: #fff7e6;
            border: 1px solid #f0c36d;
            color: #5f3b00;
            font-size: 10px;
            line-height: 1.45;
            margin: 0 0 16px;
            padding: 9px 11px;
        }

        .station-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .station-content table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
        }

        .station-content thead th,
        .station-content thead td,
        .station-content .header-row th,
        .station-content .header-row td,
        .station-content .terminal-col,
        .station-content .flete-col,
        .station-content .magna-col,
        .station-content .premium-col,
        .station-content .diesel-col,
        .station-content table > tbody > tr:first-child > th {
            color: #ffffff !important;
        }

        .empty-state {
            border: 1px solid #d9dfe7;
            color: #5f6875;
            padding: 18px;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #d9dfe7;
            color: #6b7280;
            font-size: 9px;
            margin-top: 18px;
            padding-top: 8px;
            text-align: center;
            /* Importante para que el footer no se superponga si la página está muy llena */
            position: relative;
            background: #ffffff;
        }

        .page-break {
            page-break-after: always;
        }

        /* ==========================================================================
           3. RECUPERAR COLORES ORIGINALES DEL ENCABEZADO (Sobreescritura forzada)
           ========================================================================== */

        /* Columnas 1, 2 y 3: Terminal, Calidad, Flete (Gris Oscuro) */
        .station-content table thead th:nth-child(1),
        .station-content table thead th:nth-child(2),
        .station-content table thead th:nth-child(3) {
            background-color: #4A4A4A !important;
            color: #FFFFFF !important;
        }

        /* Columna 4: MAGNA (Verde) */
        .station-content table thead th:nth-child(4) {
            background-color: #00B050 !important;
            color: #FFFFFF !important;
        }

        /* Columna 5: PREMIUM (Rojo) */
        .station-content table thead th:nth-child(5) {
            background-color: #FF0000 !important;
            color: #FFFFFF !important;
        }

        /* Columna 6: DIESEL (Negro) */
        .station-content table thead th:nth-child(6) {
            background-color: #000000 !important;
            color: #FFFFFF !important;
        }

    </style>
</head>
<body>

<div id="watermark-fondo">
    <img src="https://storage.googleapis.com/maosa-public-assets/img/maosa-logo-n.png" alt="Marca de agua">
</div>

<header class="document-header">
    <h1 class="document-title">Tabla de Precios</h1>
    <p class="document-meta">
        Usuario: {{ $user->name }}<br>
        Fecha de vigencia consultada: {{ $effectiveDate->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}<br>
        Descargado: {{ $downloadedAt->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm:ss') }}
    </p>
</header>

<div class="notice">
    La relación de precios descargada es una representación visual de la fecha y hora de los precios.
    Estos pueden variar de manera constante, por lo tanto solo la consulta en el sitio web se considera válida.
</div>

@forelse($priceTables as $entry)
    <section class="station-section">
        @if(!empty($entry['html']))
            <div class="station-content">
                {!! $entry['html'] !!}
            </div>
        @elseif(($entry['status'] ?? null) === 404)
            <div class="empty-state">
                Sin precios disponibles para {{ $entry['station_name'] }} en la fecha seleccionada.
            </div>
        @elseif(($entry['status'] ?? null) === 401)
            <div class="empty-state">
                No fue posible autenticar la consulta de precios para {{ $entry['station_name'] }}.
            </div>
        @else
            <div class="empty-state">
                No fue posible obtener precios para {{ $entry['station_name'] }}. Intente más tarde.
            </div>
        @endif
    </section>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@empty
    <div class="empty-state">
        No hay estaciones asignadas para generar la tabla de precios.
    </div>
@endforelse

<div class="footer">
    Documento protegido contra edición. Generado por {{ config('app.name') }}.
</div>

</body>
</html>
