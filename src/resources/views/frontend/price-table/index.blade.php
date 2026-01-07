@extends('frontend.layouts.master')

@section('contents')
    <section class="wsus__breadcrumb" style="background: url({{ asset(config('settings.breadcrumb')) }});">
        <div class="wsus__breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12 wow fadeInUp">
                        <div class="wsus__breadcrumb_text">
                            <h1>Tabla de Precios</h1>
                            <ul>
                                <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                                <li>Tabla de Precios</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="wsus__dashboard mt_90 xs_mt_70 pb_120 xs_pb_100">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="wsus__dashboard_contant">
                        <div class="wsus__dashboard_contant_top">
                            <div class="wsus__dashboard_heading">
                                <h5>Mi Tabla de Precios</h5>
                                @if($priceList)
                                    <a href="{{ route('user.price-table.pdf') }}" class="common_btn">
                                        <i class="fas fa-file-pdf"></i> Exportar PDF
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($priceList)
                            <div class="price-table-container" style="background: white; padding: 20px; border-radius: 8px;">
                                <table class="price-table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                    <thead>
                                    <tr>
                                        <th colspan="4" style="background: #1a472a; color: white; padding: 15px; text-align: center; font-size: 18px; font-weight: bold; border-radius: 8px 8px 0 0;">
                                            ESTIMADO {{ strtoupper($user->name) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="background: #1a472a; color: white; padding: 8px; text-align: center; font-size: 13px;">
                                            {{ $priceList->price_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                        </th>
                                    </tr>
                                    <tr style="background: #333; color: white;">
                                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">TERMINAL</th>
                                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #2e7d32; width: 120px; font-weight: bold;">MAGNA</th>
                                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #c62828; width: 120px; font-weight: bold;">PREMIUM</th>
                                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd; background: #424242; width: 120px; font-weight: bold;">DIÉSEL</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($priceList->items as $item)
                                        <tr style="background: {{ $loop->even ? '#f9f9f9' : 'white' }};">
                                            <td style="padding: 10px 12px; border: 1px solid #ddd; font-weight: 500;">{{ $item->terminal_name }}</td>
                                            <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #2e7d32;">
                                                @if($item->magna_price)
                                                    <span style="color: #666;">$</span> {{ number_format($item->magna_price, 4) }}
                                                @else
                                                    <span style="color: #999;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right; color: #c62828;">
                                                @if($item->premium_price)
                                                    <span style="color: #666;">$</span> {{ number_format($item->premium_price, 4) }}
                                                @else
                                                    <span style="color: #999;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding: 10px 12px; border: 1px solid #ddd; text-align: right;">
                                                @if($item->diesel_price)
                                                    <span style="color: #666;">$</span> {{ number_format($item->diesel_price, 4) }}
                                                @else
                                                    <span style="color: #999;">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                @if($legends->count() > 0)
                                    <div class="legends" style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
                                        @foreach($legends as $legend)
                                            <p style="margin: 5px 0; font-size: 12px; {{ $loop->last ? 'color: #c62828; font-weight: 500;' : 'color: #555;' }}">
                                                {{ $legend->legend_text }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="supplier-link" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
                                    <a href="{{ route('listings') }}" class="common_btn" style="display: inline-block;">
                                        <i class="fas fa-building"></i> Ver Proveedores
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="no-price-list" style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px;">
                                <i class="fas fa-gas-pump" style="font-size: 60px; color: #ccc; margin-bottom: 20px;"></i>
                                <h4 style="color: #666;">No hay lista de precios disponible</h4>
                                <p style="color: #999;">Tu lista de precios personalizada aún no ha sido configurada.</p>
                                <a href="{{ route('listings') }}" class="common_btn" style="margin-top: 20px;">
                                    <i class="fas fa-building"></i> Ver Proveedores
                                </a>
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
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .price-table th, .price-table td {
            transition: background 0.2s;
        }
        .price-table tbody tr:hover {
            background: #e8f5e9 !important;
        }
        @media print {
            .wsus__breadcrumb, .dashboard_sidebar, .supplier-link, .common_btn {
                display: none !important;
            }
            .col-xl-9 {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
@endpush
