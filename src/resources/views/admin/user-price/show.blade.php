@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.user-price.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Vista Previa - Lista de Precios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.user-price.index') }}">Listas de Precios</a></div>
                <div class="breadcrumb-item">Vista Previa</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $priceList->user->name }}
                                @if($priceList->branch)
                                    <small class="text-muted">- {{ $priceList->branch->name }}</small>
                                @endif
                            </h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.user-price.edit', $priceList->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Preview of how the user will see it -->
                            <div class="price-table-preview" style="max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <thead>
                                    <tr>
                                        <th colspan="5" style="background: #1a472a; color: white; padding: 10px; text-align: center; font-size: 16px; font-weight: bold;">
                                            ESTIMADO {{ strtoupper($priceList->user->name) }}
                                            @if($priceList->branch)
                                                <br><span style="font-size: 14px; font-weight: normal;">{{ $priceList->branch->name }}</span>
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" style="background: #1a472a; color: white; padding: 5px; text-align: center; font-size: 12px;">
                                            {{ $priceList->price_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                        </th>
                                    </tr>
                                    <tr style="background: #333; color: white;">
                                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">TERMINAL</th>
                                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd; background: #5c6bc0; width: 90px;">FLETE</th>
                                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd; background: #2e7d32; width: 100px;">MAGNA</th>
                                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd; background: #c62828; width: 100px;">PREMIUM</th>
                                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd; background: #333; width: 100px;">DIESEL</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($priceList->items as $item)
                                        <tr style="background: {{ $loop->even ? '#f5f5f5' : 'white' }};">
                                            <td style="padding: 6px 8px; border: 1px solid #ddd;">{{ $item->terminal_name }}</td>
                                            <td style="padding: 6px 8px; border: 1px solid #ddd; text-align: right; color: #5c6bc0;">
                                                @if($item->shipping_price)
                                                    $ {{ number_format($item->shipping_price, 4) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="padding: 6px 8px; border: 1px solid #ddd; text-align: right; color: #2e7d32;">
                                                @if($item->magna_price)
                                                    $ {{ number_format($item->magna_price, 4) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="padding: 6px 8px; border: 1px solid #ddd; text-align: right; color: #c62828;">
                                                @if($item->premium_price)
                                                    $ {{ number_format($item->premium_price, 4) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="padding: 6px 8px; border: 1px solid #ddd; text-align: right;">
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
                                    <div style="margin-top: 15px; font-size: 11px; color: #333;">
                                        @foreach($legends as $legend)
                                            <p style="margin: 3px 0; {{ $loop->last ? 'color: #c62828;' : '' }}">
                                                {{ $legend->legend_text }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
