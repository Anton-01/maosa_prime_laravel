@extends('auth.layouts.auth')

@section('auth-content')

    <div class="auth-form-header">
        <p class="auth-greeting">Recuperación de acceso</p>
        <h2>¿Olvidó su<br>contraseña?</h2>
        <p class="auth-subtitle" style="margin-top:10px;">
            No se preocupe. Ingrese su correo electrónico y le enviaremos un enlace para restablecer su contraseña.
        </p>
    </div>

    @if(session()->has('status'))
        <div class="auth-alerts">
            <div class="auth-alert auth-alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="auth-alerts">
            @foreach($errors->all() as $error)
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="auth-btn">
            Enviar enlace de recuperación
        </button>

        <p class="auth-secondary-link">
            ¿Recordó su contraseña?
            <a href="{{ route('login') }}">Iniciar Sesión</a>
        </p>

    </form>

@endsection
