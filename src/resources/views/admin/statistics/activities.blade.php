@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.statistics.show', $user->id) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Actividades de {{ $user->name }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.index') }}">Estadísticas</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.statistics.show', $user->id) }}">{{ $user->name }}</a></div>
                <div class="breadcrumb-item">Actividades</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Filter -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-2">
                            <label>Fecha Desde</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-2">
                            <label>Fecha Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-3">
                            <label>Tipo de Actividad</label>
                            <select name="activity_type" class="form-control">
                                <option value="">Todos</option>
                                @foreach($activityTypes as $type)
                                    <option value="{{ $type }}" {{ $activityType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Todas las Actividades</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>URL</th>
                                <th>IP</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td><span class="badge badge-primary">{{ $activity->activity_type }}</span></td>
                                    <td>{{ $activity->activity_description ?? '-' }}</td>
                                    <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $activity->url ?? '-' }}
                                    </td>
                                    <td>{{ $activity->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">Sin actividades registradas</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $activities->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
