@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.statistics.sessions', $session->user_id) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Detalle de Sesión</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.index') }}">Estadísticas</a></div>
                <div class="breadcrumb-item">Sesión #{{ $session->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Session Info -->
            <div class="card">
                <div class="card-header">
                    <h4>Información de la Sesión</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Usuario:</strong><br>
                            {{ $session->user->name ?? 'Anónimo' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Dispositivo:</strong><br>
                            {{ ucfirst($session->device_type ?? '-') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Navegador:</strong><br>
                            {{ $session->browser }} {{ $session->browser_version }}
                        </div>
                        <div class="col-md-3">
                            <strong>Sistema:</strong><br>
                            {{ $session->platform }} {{ $session->platform_version }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Inicio:</strong><br>
                            {{ $session->started_at->format('d/m/Y H:i:s') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Fin:</strong><br>
                            {{ $session->ended_at ? $session->ended_at->format('H:i:s') : 'En curso' }}
                        </div>
                        <div class="col-md-3">
                            <strong>IP:</strong><br>
                            {{ $session->ip_address ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ubicación:</strong><br>
                            {{ $session->city ? $session->city . ', ' : '' }}{{ $session->region ? $session->region . ', ' : '' }}{{ $session->country ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Visits Timeline -->
            <div class="card">
                <div class="card-header">
                    <h4>Recorrido de Páginas ({{ $pageVisits->count() }} páginas)</h4>
                </div>
                <div class="card-body">
                    <div class="activities">
                        @forelse($pageVisits as $visit)
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job">{{ $visit->visited_at->format('H:i:s') }}</span>
                                        @if($visit->time_on_page)
                                            <span class="badge badge-info ml-2">{{ $visit->formatted_time_on_page }}</span>
                                        @endif
                                    </div>
                                    <p class="mb-0" style="word-break: break-all;">{{ $visit->url }}</p>
                                    @if($visit->referrer)
                                        <small class="text-muted">Desde: {{ $visit->referrer }}</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Sin visitas registradas</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activities -->
            @if($activities->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4>Actividades ({{ $activities->count() }})</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Elemento</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('H:i:s') }}</td>
                                        <td><span class="badge badge-primary">{{ $activity->activity_type }}</span></td>
                                        <td>{{ $activity->activity_description ?? '-' }}</td>
                                        <td>{{ $activity->element_text ?? $activity->element_id ?? '-' }}</td>
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
