@extends('auth.layouts.auth')

@section('auth-content')

    <div class="auth-form-header">
        <p class="auth-greeting">Bienvenido de nuevo</p>
        <h2>Inicia Sesión</h2>
        <p class="auth-subtitle">Accede con tus credenciales para continuar</p>
    </div>

    @if(session()->has('status') || $errors->any())
        <div class="auth-alerts">
            @if(session()->has('status'))
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif
            @foreach($errors->all() as $error)
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        {{-- Honeypot --}}
        <div class="auth-honeypot">
            <input type="text" name="honeypot" id="honeypot" value="" tabindex="-1" autocomplete="off">
        </div>

        {{-- Email --}}
        <div class="auth-form-group">
            <label class="auth-label" for="auth-email">Correo Electrónico</label>
            <div class="auth-input-wrapper">
                <input
                    id="auth-email"
                    class="auth-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="correo@empresa.com"
                    required
                    autofocus>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>
        </div>

        {{-- Password --}}
        <div class="auth-form-group">
            <label class="auth-label" for="auth-password">Contraseña</label>
            <div class="auth-input-wrapper">
                <input
                    id="auth-password"
                    class="auth-input"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="auth-options-row">
            <label class="auth-remember">
                <input type="checkbox" name="remember" id="auth-remember">
                Recordarme
            </label>
            <a href="{{ route('password.request') }}" class="auth-forgot-link">
                ¿Olvidó su contraseña?
            </a>
        </div>

        <button type="submit" class="auth-btn">
            Iniciar Sesión
        </button>

    </form>

@endsection
