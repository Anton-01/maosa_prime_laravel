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

            {{-- ============================================================
                 TABS: agrupan las métricas para evitar scroll excesivo
                 ============================================================ --}}
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="stats-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-general-link" data-toggle="tab" href="#tab-general"
                               role="tab" aria-controls="tab-general" aria-selected="true">
                                <i class="fas fa-chart-pie"></i> Métricas Generales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-geo-link" data-toggle="tab" href="#tab-geo"
                               role="tab" aria-controls="tab-geo" aria-selected="false">
                                <i class="fas fa-globe-americas"></i> Geografía y Tecnología
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pages-link" data-toggle="tab" href="#tab-pages"
                               role="tab" aria-controls="tab-pages" aria-selected="false">
                                <i class="fas fa-file-alt"></i> Páginas
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="stats-tabs-content">

                        {{-- Tab 1: Métricas Generales (cards) --}}
                        <div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="tab-general-link">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                                        <div class="card-wrap">
                                            <div class="card-header"><h4>Usuarios Únicos</h4></div>
                                            <div class="card-body">{{ number_format($uniqueUsers) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-success"><i class="fas fa-sign-in-alt"></i></div>
                                        <div class="card-wrap">
                                            <div class="card-header"><h4>Sesiones</h4></div>
                                            <div class="card-body">{{ number_format($totalSessions) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-warning"><i class="fas fa-eye"></i></div>
                                        <div class="card-wrap">
                                            <div class="card-header"><h4>Páginas Vistas</h4></div>
                                            <div class="card-body">{{ number_format($totalPageViews) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-info"><i class="fas fa-mouse-pointer"></i></div>
                                        <div class="card-wrap">
                                            <div class="card-header"><h4>Actividades</h4></div>
                                            <div class="card-body">{{ number_format($totalActivities) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Geografía y Tecnología (Dispositivos, Navegadores, Países) --}}
                        <div class="tab-pane fade" id="tab-geo" role="tabpanel" aria-labelledby="tab-geo-link">
                            <div class="row">
                                <div class="col-lg-4">
                                    @include('admin.statistics.partials.export-table-card', [
                                        'title'   => 'Dispositivos',
                                        'table'   => $devicesTable,
                                        'tableId' => 'devices-stats-table',
                                        'type'    => 'devices',
                                    ])
                                </div>
                                <div class="col-lg-4">
                                    @include('admin.statistics.partials.export-table-card', [
                                        'title'   => 'Navegadores',
                                        'table'   => $browsersTable,
                                        'tableId' => 'browsers-stats-table',
                                        'type'    => 'browsers',
                                    ])
                                </div>
                                <div class="col-lg-4">
                                    @include('admin.statistics.partials.export-table-card', [
                                        'title'   => 'Países',
                                        'table'   => $countriesTable,
                                        'tableId' => 'countries-stats-table',
                                        'type'    => 'countries',
                                    ])
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Páginas más visitadas --}}
                        <div class="tab-pane fade" id="tab-pages" role="tabpanel" aria-labelledby="tab-pages-link">
                            @include('admin.statistics.partials.export-table-card', [
                                'title'   => 'Páginas más Visitadas',
                                'table'   => $pagesTable,
                                'tableId' => 'pages-stats-table',
                                'type'    => 'pages',
                            ])
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usuarios más activos: INDEPENDIENTE, debajo del componente de Tabs --}}
            <div class="row">
                <div class="col-12">
                    @include('admin.statistics.partials.export-table-card', [
                        'title'   => 'Usuarios más Activos',
                        'table'   => $dataTable,
                        'tableId' => 'active-users-table',
                        'type'    => 'active-users',
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{ $devicesTable->scripts(attributes: ['type' => 'module']) }}
    {{ $browsersTable->scripts(attributes: ['type' => 'module']) }}
    {{ $countriesTable->scripts(attributes: ['type' => 'module']) }}
    {{ $pagesTable->scripts(attributes: ['type' => 'module']) }}
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $(function () {
            // Selecciones persistentes por tabla (a través de paginación): id -> Set de claves
            const statSelections = {};
            const selectionFor = (tableId) => statSelections[tableId] || (statSelections[tableId] = new Set());

            function syncSelectAll($table) {
                const rows = $table.find('.stat-row-check');
                const checked = rows.filter(':checked').length;
                $table.find('.stat-check-all').prop('checked', rows.length > 0 && rows.length === checked);
            }

            $(document)
                // Marcar/desmarcar una fila
                .on('change', '.stat-row-check', function () {
                    const $table = $(this).closest('table');
                    const set = selectionFor($table.attr('id'));
                    this.checked ? set.add(this.value) : set.delete(this.value);
                    syncSelectAll($table);
                })
                // Marcar/desmarcar todas las filas visibles
                .on('change', '.stat-check-all', function () {
                    const $table = $(this).closest('table');
                    const set = selectionFor($table.attr('id'));
                    const check = this.checked;
                    $table.find('.stat-row-check').each(function () {
                        this.checked = check;
                        check ? set.add(this.value) : set.delete(this.value);
                    });
                })
                // Reaplicar selección al redibujar (paginación / búsqueda / orden)
                .on('draw.dt', 'table', function () {
                    const set = statSelections[$(this).attr('id')];
                    if (!set) return;
                    $(this).find('.stat-row-check').each(function () {
                        this.checked = set.has(this.value);
                    });
                    syncSelectAll($(this));
                });

            // Envía una descarga vía formulario (GET) con fechas + claves seleccionadas
            function submitExport(url, keys) {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = url;
                const add = (name, value) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                };
                add('date_from', document.querySelector('input[name=date_from]').value);
                add('date_to', document.querySelector('input[name=date_to]').value);
                (keys || []).forEach((k) => add('keys[]', k));
                document.body.appendChild(form);
                form.submit();
                form.remove();
            }

            $(document)
                .on('click', '.stat-export-all', function () {
                    submitExport($(this).data('url'), null);
                })
                .on('click', '.stat-export-selected', function () {
                    const set = statSelections[$(this).data('table')];
                    const keys = set ? Array.from(set) : [];
                    if (!keys.length) {
                        alert('Seleccione al menos una fila para exportar.');
                        return;
                    }
                    submitExport($(this).data('url'), keys);
                });

            // Las tablas dentro de tabs ocultos calculan mal el ancho: reajustar al mostrarse
            $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
                if ($.fn.dataTable) {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                }
            });
        });
    </script>
@endpush
