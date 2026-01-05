<div class="dashboard_sidebar">
    <span class="close_icon">
        <i class="far fa-times"></i>
    </span>
    <a href="dsahboard.html" class="dash_logo">
        <img src="{{ asset(auth()->user()->avatar) }}" alt="logo" class="img-fluid">
    </a>
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
