@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.statistics.show', $user->id) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Sesiones de {{ $user->name }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.index') }}">Estadísticas</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.show', $user->id) }}">{{ $user->name }}</a></div>
                <div class="breadcrumb-item">Sesiones</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Todas las Sesiones</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Duración</th>
                                <th>Dispositivo</th>
                                <th>Navegador</th>
                                <th>IP</th>
                                <th>Ubicación</th>
                                <th class="text-right">Páginas</th>
                                <th class="text-center">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>{{ $session->started_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $session->ended_at ? $session->ended_at->format('H:i') : '-' }}</td>
                                    <td>{{ $session->formatted_duration }}</td>
                                    <td>{{ ucfirst($session->device_type ?? '-') }}</td>
                                    <td>{{ $session->browser ?? '-' }}</td>
                                    <td>{{ $session->ip_address ?? '-' }}</td>
                                    <td>{{ $session->city ? $session->city . ', ' . $session->country : ($session->country ?? '-') }}</td>
                                    <td class="text-right">{{ $session->page_visits_count }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.statistics.session-detail', $session->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">Sin sesiones registradas</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
