@extends('admin.layouts.master')

@section('contents')
<section class="section">
    <div class="section-header">
      <h1>Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Panel de Control</a></div>
            <div class="breadcrumb-item">Resumen General</div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-building"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Proveedores</h4>
            </div>
            <div class="card-body">
              {{ $totalListingCount }}
            </div>
          </div>
        </div>
      </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Proveedores Verificados</h4>
                    </div>
                    <div class="card-body">
                        {{ $verifiedListingsCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-toggle-on"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Proveedores Activos</h4>
                    </div>
                    <div class="card-body">
                        {{ $activeListingsCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-tags"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Categorías</h4>
            </div>
            <div class="card-body">
              {{ $listingCategoryCount }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Segunda fila de estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Ubicaciones</h4>
                    </div>
                    <div class="card-body">
                        {{ $locationCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-secondary">
                    <i class="fas fa-gas-pump"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Terminales Combustible</h4>
                    </div>
                    <div class="card-body">
                        {{ $fuelTerminalsCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Listas de Precios Activas</h4>
                    </div>
                    <div class="card-body">
                        {{ $activePriceListsCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Amenidades</h4>
                    </div>
                    <div class="card-body">
                        {{ $amenitiesCount }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila: Usuarios y actividad -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Administradores</h4>
                    </div>
                    <div class="card-body">
                        {{ $adminCount }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Usuarios</h4>
                    </div>
                    <div class="card-body">
                        {{ $usersCount }}
                    </div>
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
                        <h4>Acceso a Precios</h4>
                    </div>
                    <div class="card-body">
                        {{ $usersWithPriceAccess }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Publicaciones Blog</h4>
                    </div>
                    <div class="card-body">
                        {{ $blogsCount }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Actividad Reciente -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line mr-2"></i>Actividad de los Últimos 7 Días</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="card card-statistic-2">
                                <div class="card-stats">
                                    <div class="card-stats-title">Resumen Semanal</div>
                                    <div class="card-stats-items">
                                        <div class="card-stats-item">
                                            <div class="card-stats-item-count">{{ $sessionsLastWeek }}</div>
                                            <div class="card-stats-item-label">Sesiones</div>
                                        </div>
                                        <div class="card-stats-item">
                                            <div class="card-stats-item-count">{{ $pageVisitsLastWeek }}</div>
                                            <div class="card-stats-item-label">Visitas</div>
                                        </div>
                                        <div class="card-stats-item">
                                            <div class="card-stats-item-count">{{ $newUsersLastWeek }}</div>
                                            <div class="card-stats-item-label">Nuevos Usuarios</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-icon shadow-primary bg-primary">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <canvas id="activityChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de listas y tablas -->
    <div class="row">
        <!-- Últimos Usuarios -->
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-plus mr-2"></i>Últimos Usuarios Registrados</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.role-user.index') }}" class="btn btn-primary btn-sm">
                            Ver todos <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($latestUsers as $user)
                                <tr>
                                    <td>{{ Str::limit($user->name, 20) }}</td>
                                    <td>{{ Str::limit($user->email, 25) }}</td>
                                    <td><span class="badge badge-light">{{ $user->created_at->format('d/m/Y') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay usuarios registrados</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Proveedores -->
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-building mr-2"></i>Últimos Proveedores</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.listing.index') }}" class="btn btn-primary btn-sm">
                            Ver todos <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Ubicación</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($latestListings as $listing)
                                <tr>
                                    <td>{{ Str::limit($listing->title, 20) }}</td>
                                    <td><span class="badge badge-info">{{ $listing->category->name ?? 'N/A' }}</span></td>
                                    <td>{{ Str::limit($listing->location->name ?? 'N/A', 15) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay proveedores registrados</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proveedores por Categoría -->
    <div class="row">
        <div class="col-lg-6 col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-pie mr-2"></i>Proveedores por Categoría</h4>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-6 col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-bolt mr-2"></i>Accesos Rápidos</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.listing.create') }}" class="btn btn-outline-primary btn-block py-3">
                                <i class="fas fa-plus-circle d-block mb-2" style="font-size: 24px;"></i>
                                Nuevo Proveedor
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.user-price.create') }}" class="btn btn-outline-success btn-block py-3">
                                <i class="fas fa-dollar-sign d-block mb-2" style="font-size: 24px;"></i>
                                Nueva Lista Precios
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.role-user.create') }}" class="btn btn-outline-info btn-block py-3">
                                <i class="fas fa-user-plus d-block mb-2" style="font-size: 24px;"></i>
                                Nuevo Usuario
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.category.index') }}" class="btn btn-outline-warning btn-block py-3">
                                <i class="fas fa-tags d-block mb-2" style="font-size: 24px;"></i>
                                Categorías
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.statistics.index') }}" class="btn btn-outline-danger btn-block py-3">
                                <i class="fas fa-chart-bar d-block mb-2" style="font-size: 24px;"></i>
                                Estadísticas
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4 mb-4">
                            <a href="{{ route('admin.fuel-terminal.index') }}" class="btn btn-outline-secondary btn-block py-3">
                                <i class="fas fa-gas-pump d-block mb-2" style="font-size: 24px;"></i>
                                Terminales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Actividad
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityData = @json($activityData);
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityData.map(item => item.date),
                datasets: [{
                    label: 'Visitas',
                    data: activityData.map(item => item.visits),
                    borderColor: '#6777ef',
                    backgroundColor: 'rgba(103, 119, 239, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Sesiones',
                    data: activityData.map(item => item.sessions),
                    borderColor: '#fc544b',
                    backgroundColor: 'rgba(252, 84, 75, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // Gráfico de Categorías
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = @json($listingsByCategory);
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.listings_count),
                    backgroundColor: [
                        '#6777ef',
                        '#fc544b',
                        '#ffa426',
                        '#47c363',
                        '#3abaf4'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
@endpush
