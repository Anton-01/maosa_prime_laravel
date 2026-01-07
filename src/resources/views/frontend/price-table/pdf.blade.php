<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Precios - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            color: #1a472a;
            margin-bottom: 5px;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .price-table .title-row {
            background: #1a472a;
            color: white;
        }
        .price-table .title-row td {
            padding: 12px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        .price-table .date-row {
            background: #1a472a;
            color: white;
        }
        .price-table .date-row td {
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }
        .price-table .header-row th {
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            color: white;
            border: 1px solid #ddd;
        }
        .price-table .header-row .terminal-col {
            background: #333;
            text-align: left;
        }
        .price-table .header-row .magna-col {
            background: #2e7d32;
        }
        .price-table .header-row .premium-col {
            background: #c62828;
        }
        .price-table .header-row .diesel-col {
            background: #424242;
        }
        .price-table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        .price-table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .price-table tbody .terminal-name {
            font-weight: 500;
        }
        .price-table tbody .price {
            text-align: right;
        }
        .price-table tbody .price-magna {
            color: #2e7d32;
        }
        .price-table tbody .price-premium {
            color: #c62828;
        }
        .legends {
            margin-top: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 3px;
        }
        .legends p {
            margin: 3px 0;
            font-size: 10px;
            color: #555;
        }
        .legends p.warning {
            color: #c62828;
            font-weight: 500;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
@if($priceList)
    <table class="price-table">
        <thead>
        <tr class="title-row">
            <td colspan="4">ESTIMADO {{ strtoupper($user->name) }}</td>
        </tr>
        <tr class="date-row">
            <td colspan="4">{{ $priceList->price_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</td>
        </tr>
        <tr class="header-row">
            <th class="terminal-col" style="width: 40%;">TERMINAL</th>
            <th class="magna-col" style="width: 20%;">MAGNA</th>
            <th class="premium-col" style="width: 20%;">PREMIUM</th>
            <th class="diesel-col" style="width: 20%;">DIÉSEL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($priceList->items as $item)
            <tr>
                <td class="terminal-name">{{ $item->terminal_name }}</td>
                <td class="price price-magna">
                    @if($item->magna_price)
                        $ {{ number_format($item->magna_price, 4) }}
                    @else
                        -
                    @endif
                </td>
                <td class="price price-premium">
                    @if($item->premium_price)
                        $ {{ number_format($item->premium_price, 4) }}
                    @else
                        -
                    @endif
                </td>
                <td class="price">
                    @if($item->diesel_price)
                        $ {{ number_format($item->diesel_price, 4) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($legends->count() > 0)
        <div class="legends">
            @foreach($legends as $index => $legend)
                <p class="{{ $loop->last ? 'warning' : '' }}">{{ $legend->legend_text }}</p>
            @endforeach
        </div>
    @endif

    <div class="footer">
        Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] HH:mm') }}
    </div>
@else
    <div style="text-align: center; padding: 50px;">
        <h2>No hay lista de precios disponible</h2>
    </div>
@endif
</body>
</html>
