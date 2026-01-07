@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Panel de Estadísticas</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Estadísticas</div>
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
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Usuarios Únicos</h4>
                            </div>
                            <div class="card-body">{{ number_format($uniqueUsers) }}</div>
                        </div>
                    </div>
                </div>
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
                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $page->url }}
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

                <!-- Top Users -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Usuarios más Activos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th class="text-right">Visitas</th>
                                        <th class="text-right">Sesiones</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($topUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td class="text-right">{{ number_format($user->page_visits_count) }}</td>
                                            <td class="text-right">{{ number_format($user->sessions_count) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.statistics.show', $user->id) }}" class="btn btn-sm btn-info">
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

            <div class="row">
                <!-- Device Breakdown -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Dispositivos</h4>
                        </div>
                        <div class="card-body">
                            @forelse($deviceBreakdown as $device)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ ucfirst($device->device_type ?? 'Desconocido') }}</span>
                                        <span>{{ number_format($device->count) }}</span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $totalSessions > 0 ? ($device->count / $totalSessions * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Sin datos</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Browser Breakdown -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Navegadores</h4>
                        </div>
                        <div class="card-body">
                            @forelse($browserBreakdown as $browser)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $browser->browser ?? 'Desconocido' }}</span>
                                        <span>{{ number_format($browser->count) }}</span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: {{ $totalSessions > 0 ? ($browser->count / $totalSessions * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Sin datos</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Country Breakdown -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Países</h4>
                        </div>
                        <div class="card-body">
                            @forelse($countryBreakdown as $country)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $country->country ?? 'Desconocido' }}</span>
                                        <span>{{ number_format($country->count) }}</span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $totalSessions > 0 ? ($country->count / $totalSessions * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Sin datos</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
