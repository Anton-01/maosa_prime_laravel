@extends('frontend.layouts.master')

@section('contents')
    <section id="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-lg-9">
                    <div class="dashboard_content">

                        <div class="my_listing">
                            <h4 class="mb-0" style="margin-bottom: 8px;">ESTIMADO - {{ strtoupper($user->name) }}</h4>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <h4 class="mb-0" style="border-bottom: none;">Mi Tabla de Precios</h4>
                                @if($priceLists->count() > 0)
                                    <a href="{{ route('user.price-table.pdf') }}" class="pdf-export-btn">
                                        <i class="fas fa-file-pdf"></i> Exportar PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if($priceLists->count() > 0)
                            @foreach($priceLists as $priceList)
                                <div class="price-table-container {{ !$loop->first ? 'mt-4' : '' }}">
                                    <table class="price-table">
                                        <thead>
                                        <tr>
                                            <th colspan="6" style="background: #1a472a; color: white; padding: 5px 15px; font-size: 18px; font-weight: bold; border-radius: 8px 8px 0 0;">
                                                @if($priceList->branch)
                                                    <span style="font-size: 23px; font-weight: bold; color: white;">{{ $priceList->branch->name }}</span>
                                                @endif
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="6" style="background: #1a472a; color: white; padding: 2px 15px; font-size: 13px;">
                                                Vigencia de los costos asignados: {{ $priceList->price_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                            </th>
                                        </tr>
                                        <tr style="background: #333; color: white;">
                                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">TERMINAL</th>
                                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #5c6bc0; width: 100px; font-weight: bold;">CALIDAD</th>
                                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #5c6bc0; width: 100px; font-weight: bold;">FLETE</th>
                                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #2e7d32; width: 120px; font-weight: bold;">MAGNA</th>
                                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #c62828; width: 120px; font-weight: bold;">PREMIUM</th>
                                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #424242; width: 120px; font-weight: bold;">DIESEL</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($priceList->items as $item)
                                            <tr style="background: {{ $loop->even ? '#f9f9f9' : 'white' }};">
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; font-weight: 500;">{{ $item->terminal_name }}</td>
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #5c6bc0;">
                                                    {{ $item->getFormattedQualityCode() }}
                                                </td>
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #5c6bc0;">
                                                    <span style="color: #666;">$</span> {{ $item->getFormattedShippingPriceAttribute() }}
                                                </td>
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #2e7d32;">
                                                    <span style="color: #666;">$</span> {{ $item->getFormattedMagnaPriceAttribute() }}
                                                </td>
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #c62828;">
                                                    <span style="color: #666;">$</span> {{ $item->getFormattedPremiumPriceAttribute() }}
                                                </td>
                                                <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right;">
                                                    <span style="color: #666;">$</span> {{ $item->getFormattedDieselPriceAttribute() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            @if($legends->count() > 0)

                                <div class="legends">
                                    @foreach($legends as $legend)
                                        <p class="{{ $loop->last ? 'legend-important' : '' }}">
                                            {{ $legend->legend_text }}
                                        </p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="supplier-link" style="background-color: #f5f5f5;border-radius: 5px;">
                                <a href="{{ route('listings') }}" class="read_btn">
                                    <i class="fas fa-building"></i> Ver Proveedores
                                </a>
                            </div>

                        @else
                            <div class="no-price-list">
                                <div class="no-price-list" style="display: flex; flex-direction: column; align-items: center;">
                                    <i class="fas fa-gas-pump"></i>
                                    <h4>No hay lista de precios disponible</h4>
                                    <p>Tu lista de precios personalizada aún no ha sido configurada.</p>
                                    <a href="{{ route('listings') }}" class="read_btn">
                                        <i class="fas fa-building"></i> Ver Proveedores
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .price-table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        .price-table th, .price-table td {
            transition: background 0.2s;
        }
        .price-table tbody tr:hover {
            background: #e8f5e9 !important;
        }
        .pdf-export-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: #dc3545;
            color: white !important;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        }
        .pdf-export-btn:hover {
            background: #c82333;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            transform: translateY(-2px);
        }
        .pdf-export-btn i {
            font-size: 16px;
        }
        .legends {
            margin-top: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .legends p {
            margin: 5px 0;
            font-size: 12px;
            color: #555;
        }
        .legends p.legend-important {
            color: #c62828;
            font-weight: 500;
        }
        .supplier-link {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        .no-price-list {
            text-align: center;
            padding: 50px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .no-price-list i {
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
            display: block;
        }
        .no-price-list h4 {
            color: #666;
            margin-bottom: 15px;
        }
        .no-price-list p {
            color: #999;
            margin-bottom: 20px;
        }

        @media print {
            .dashboard_sidebar, .supplier-link, .pdf-export-btn {
                display: none !important;
            }
            .col-lg-9 {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
@endpush
