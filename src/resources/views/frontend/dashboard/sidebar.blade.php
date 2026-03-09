<div class="dashboard_sidebar">
    <span class="close_icon">
        <i class="far fa-times"></i>
    </span>
    <a href="{{ route('user.profile.index') }}" class="dash_logo">
        <img src="{{ asset(auth()->user()->avatar) }}" alt="logo" class="img-fluid">
    </a>
    <div class="container-user-simple-info">
        <span style="margin-bottom: 5px;"> <i class="fas fa-envelope"></i> {{auth()->user()->email}}</span>
        <span style="margin-bottom: 5px;"> <i class="fas fa-user-circle"></i> {{auth()->user()->name}}</span>
        <span style="font-size: 13px;"> <i class="fas fa-user-check"></i> {{auth()->user()->user_type}}</span>
    </div>
    <ul class="dashboard_link">
        @if (auth()->user()->user_type === 'admin')
            <li>
                <a class="" href="{{ route('admin.dashboard.index') }}">
                    <i class="fas fa-tachometer"></i>
                    Admin Dashboard
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('user.profile.index') }}">
                <i class="far fa-user"></i>
                Mi perfil
            </a>
        </li>
        @if (auth()->user()->canViewPriceTable())
            <li style="background-color: rgb(199, 149, 44, 0.8)">
                <a href="{{ route('user.price-table.index') }}">
                    <i class="fas fa-gas-pump"></i>
                    Tabla de Precios
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('listings') }}">
                <i class="fas fa-building"></i>
                Ver Proveedores
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="far fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </form>
        </li>
    </ul>
</div>
