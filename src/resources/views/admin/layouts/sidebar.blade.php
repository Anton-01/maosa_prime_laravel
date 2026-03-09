<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                        class="fas fa-search"></i></a></li>
        </ul>

    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ auth()->user()->avatar }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('admin.profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Perfil
                </a>
                <a href="{{ route('admin.settings.index') }}" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Configuración
                </a>
                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                    this.closest('form').submit();" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}">{{ config('settings.site_name') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard.index') }}">{{ truncate(config('settings.site_name'), 2) }}</a>
        </div>
        <ul class="sidebar-menu">

            <li class="menu-header">Admin Panel</li>

            <li class="{{ setSidebarActive(['admin.dashboard.index']) }}"><a class="nav-link" href="{{ route('admin.dashboard.index') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>

            @can('access management sections content')
                <li class="dropdown {{ setSidebarActive([
                    'admin.hero.index',
                    'admin.hero-public.index',
                    'admin.our-features.index',
                    'admin.section-title.index'
                    ]) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-columns"></i> <span>Secciones</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.hero.index']) }}"><a class="nav-link" href="{{ route('admin.hero.index') }}">Banner</a></li>
                        <li class="{{ setSidebarActive(['admin.hero-public.index']) }}"><a class="nav-link" href="{{ route('admin.hero.public.index') }}">Banner público</a></li>
                        <li class="{{ setSidebarActive(['admin.our-features.index']) }}"><a class="nav-link" href="{{ route('admin.our-features.index') }}">Nuestras funciones</a></li>
                        <li class="{{ setSidebarActive(['admin.section-title.index']) }}"><a class="nav-link" href="{{ route('admin.section-title.index') }}">Títulos - Secciones</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management suppliers')
                <li class="dropdown {{
                    setSidebarActive([
                        'admin.category.*',
                        'admin.location.*',
                        'admin.amenity.*',
                        'admin.listing.*',
                    ])
                }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-list"></i> <span>Proveedores</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.category.*']) }}"><a class="nav-link" href="{{ route('admin.category.index') }}">Categorías</a></li>
                        <li class="{{ setSidebarActive(['admin.location.*']) }}"><a class="nav-link" href="{{ route('admin.location.index') }}">Ubicaciones</a></li>
                        <li class="{{ setSidebarActive(['admin.listing.*']) }}"><a class="nav-link" href="{{ route('admin.listing.index') }}">Todos los Proveedores</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management pages')
                <li class="dropdown {{ setSidebarActive(['admin.about-us.index','admin.contact.index','admin.privacy-policy.index','admin.terms-and-condition.index']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-alt"></i> <span>Páginas</span></a>
                    <ul class="dropdown-menu" {{ setSidebarActive(['admin.packages.*',]) }}>
                        <li class="{{ setSidebarActive(['admin.about-us.index']) }}"><a class="nav-link" href="{{ route('admin.about-us.index') }}">Sobre nosotros</a></li>
                        <li class="{{ setSidebarActive(['admin.contact.index']) }}"><a class="nav-link" href="{{ route('admin.contact.index') }}">Contacto</a></li>
                        <li class="{{ setSidebarActive(['admin.privacy-policy.index']) }}"><a class="nav-link" href="{{ route('admin.privacy-policy.index') }}">Política de privacidad</a></li>
                        <li class="{{ setSidebarActive(['admin.terms-and-condition.index']) }}"><a class="nav-link" href="{{ route('admin.terms-and-condition.index') }}">Términos y condiciones</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management footer')
                <li class="dropdown {{ setSidebarActive(['admin.footer-info.index', 'admin.social-link.*]']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-info"></i> <span>Gestionar Footer</span></a>

                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.footer-info.index']) }}"><a class="nav-link" href="{{ route('admin.footer-info.index') }}">Footer Info</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management users')
                <li class="dropdown {{ setSidebarActive(['admin.role-user.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-fingerprint"></i> <span>Gestión de accesos</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.role-user.*']) }}"><a class="nav-link" href="{{ route('admin.role-user.index') }}">Usuarios</a></li>

                        <li class="{{ setSidebarActive(['admin.role.*']) }}"><a class="nav-link" href="{{ route('admin.role.index') }}">Roles y permisos</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management gas price')
                <li class="dropdown {{ setSidebarActive([
                'admin.fuel-terminal.*',
                'admin.user-price.*',
                'admin.price-import.*',
                'admin.default-legend.*',
                'admin.user-legend.*'
                ]) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-gas-pump"></i> <span>Precios Combustibles</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.fuel-terminal.*']) }}"><a class="nav-link" href="{{ route('admin.fuel-terminal.index') }}">Terminales</a></li>
                        <li class="{{ setSidebarActive(['admin.user-price.*']) }}"><a class="nav-link" href="{{ route('admin.user-price.index') }}">Listas de Precios</a></li>
                        <li class="{{ setSidebarActive(['admin.price-import.*']) }}"><a class="nav-link" href="{{ route('admin.price-import.index') }}">Importar Excel</a></li>
                        <li class="{{ setSidebarActive(['admin.default-legend.*']) }}"><a class="nav-link" href="{{ route('admin.default-legend.index') }}">Leyendas por Defecto</a></li>
                        <li class="{{ setSidebarActive(['admin.user-legend.*']) }}"><a class="nav-link" href="{{ route('admin.user-legend.index') }}">Leyendas por Usuario</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management statics users')
                <li class="dropdown {{ setSidebarActive(['admin.statistics.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-chart-line"></i> <span>Estadísticas</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.statistics.index']) }}"><a class="nav-link" href="{{ route('admin.statistics.index') }}">Panel General</a></li>
                    </ul>
                </li>
            @endcan

            @can('access management menu builder')
                <li class="{{ setSidebarActive(['admin.menu-builder.index']) }}"><a class="nav-link" href="{{ route('admin.menu-builder.index') }}"><i class="fas fa-wrench"></i> <span>Menús</span></a></li>
            @endcan

            @can('access management settings maosa')
                <li class="{{ setSidebarActive(['admin.settings.index']) }}" ><a class="nav-link" href="{{ route('admin.settings.index') }}"><i class="fas fa-cogs"></i> <span>Ajustes</span></a></li>
            @endcan
        </ul>
    </aside>
</div>
