@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.statistics.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Estadísticas de {{ $user->name }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.index') }}">Estadísticas</a></div>
                <div class="breadcrumb-item">{{ $user->name }}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Date Filter -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-3">
                            <label>Fecha Desde</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('admin.statistics.sessions', $user->id) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="btn btn-info">
                                <i class="fas fa-list"></i> Ver Sesiones
                            </a>
                            <a href="{{ route('admin.statistics.activities', $user->id) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="btn btn-success">
                                <i class="fas fa-mouse-pointer"></i> Ver Actividades
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Info -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-10">
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            @if($user->canViewPriceTable())
                                <span class="badge badge-success">Acceso a Tabla de Precios</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Sesiones</h4>
                            </div>
                            <div class="card-body">{{ number_format($totalSessions) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Páginas Vistas</h4>
                            </div>
                            <div class="card-body">{{ number_format($totalPageViews) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-mouse-pointer"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Actividades</h4>
                            </div>
                            <div class="card-body">{{ number_format($totalActivities) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Tiempo Promedio</h4>
                            </div>
                            <div class="card-body">
                                @if($avgSessionDuration)
                                    {{ floor($avgSessionDuration / 60) }}m {{ $avgSessionDuration % 60 }}s
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Top Pages -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Páginas más Visitadas</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th class="text-right">Visitas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($topPages as $page)
                                        <tr>
                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $page->url }}">
                                                {{ $page->page_title ?? $page->url }}
                                            </td>
                                            <td class="text-right">{{ number_format($page->visits) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center">Sin datos</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Sesiones Recientes</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Dispositivo</th>
                                        <th>Navegador</th>
                                        <th class="text-center">Ver</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recentSessions as $session)
                                        <tr>
                                            <td>{{ $session->started_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ ucfirst($session->device_type ?? '-') }}</td>
                                            <td>{{ $session->browser ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.statistics.session-detail', $session->id) }}" class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">Sin datos</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Flows -->
            @if($navigationFlows->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4>Flujo de Navegación más Común</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th></th>
                                    <th>Hacia</th>
                                    <th class="text-right">Veces</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($navigationFlows as $flow)
                                    <tr>
                                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $flow->from_url ?? '-' }}
                                        </td>
                                        <td class="text-center"><i class="fas fa-arrow-right text-primary"></i></td>
                                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $flow->to_url ?? '-' }}
                                        </td>
                                        <td class="text-right">{{ number_format($flow->count) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
