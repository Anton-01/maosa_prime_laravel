<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión &mdash; {{ config('settings.site_name', 'MAOSA Prime') }}</title>
    <link rel="icon" type="image/png" href="{{ config('settings.favicon') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('frontend/css/all.min.css') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: {{ config('settings.site_default_color', '#f66542') }};
            --primary-dark: #c94e30;
            --primary-light: #ff8566;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --text-light: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #334155;
            --input-bg: #0f172a;
            --success: #22c55e;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            overflow-x: hidden;
        }

        /* ===== LEFT PANEL ===== */
        .login-left {
            flex: 1;
            display: none;
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .login-left { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px; }
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(246, 101, 66, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(246, 101, 66, 0.12) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Floating circles decoration */
        .circle-deco {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(246, 101, 66, 0.12);
        }
        .circle-deco:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; }
        .circle-deco:nth-child(2) { width: 300px; height: 300px; bottom: -80px; right: -80px; }
        .circle-deco:nth-child(3) { width: 200px; height: 200px; top: 40%; left: 40%; border-color: rgba(246,101,66,0.08); }

        .left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 440px;
        }

        .left-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            font-size: 36px;
            color: white;
            box-shadow: 0 20px 60px rgba(246, 101, 66, 0.35);
        }

        .left-content h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .left-content h1 span {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-content p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .feature-list {
            list-style: none;
            text-align: left;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 10px 0;
            border-bottom: 1px solid rgba(51,65,85,0.5);
        }

        .feature-list li:last-child { border-bottom: none; }

        .feature-list li .icon {
            width: 32px;
            height: 32px;
            background: rgba(246, 101, 66, 0.12);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
            font-size: 13px;
        }

        /* ===== RIGHT PANEL ===== */
        .login-right {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 24px;
            background-color: var(--bg-dark);
        }

        @media (min-width: 1024px) {
            .login-right { width: 480px; flex-shrink: 0; padding: 60px 48px; }
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        /* Mobile logo */
        .mobile-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        @media (min-width: 1024px) { .mobile-logo { display: none; } }

        .mobile-logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .mobile-logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-light);
        }

        /* Form header */
        .form-header { margin-bottom: 32px; }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.25);
            color: #86efac;
        }

        /* Form groups */
        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 14px 13px 42px;
            color: var(--text-light);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
        }

        .input-wrapper input::placeholder { color: #475569; }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(246, 101, 66, 0.12);
        }

        .input-wrapper input:focus + .input-focus-line {
            transform: scaleX(1);
        }

        /* Password toggle */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 14px;
            background: none;
            border: none;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: var(--text-light); }

        /* Checkbox & forgot */
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            display: none;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--border);
            border-radius: 5px;
            background: var(--input-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .checkbox-label input:checked + .checkbox-custom {
            background: var(--primary);
            border-color: var(--primary);
        }

        .checkbox-label input:checked + .checkbox-custom::after {
            content: '';
            width: 10px;
            height: 6px;
            border-left: 2px solid white;
            border-bottom: 2px solid white;
            transform: rotate(-45deg) translate(1px, -1px);
            display: block;
        }

        .checkbox-text {
            font-size: 0.85rem;
            color: var(--text-muted);
            user-select: none;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: var(--primary-light); }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(246, 101, 66, 0.3);
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(246, 101, 66, 0.45);
        }

        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        /* Back link */
        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            margin-top: 28px;
            transition: color 0.2s;
            justify-content: center;
        }

        .back-link:hover { color: var(--text-light); }

        /* Footer */
        .login-footer {
            margin-top: 32px;
            text-align: center;
            color: #475569;
            font-size: 0.8rem;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    </style>
</head>
<body>

    <!-- LEFT PANEL (Visible en desktop) -->
    <div class="login-left">
        <div class="circle-deco"></div>
        <div class="circle-deco"></div>
        <div class="circle-deco"></div>

        <div class="left-content">
            <div class="left-logo">
                <i class="fas fa-layer-group"></i>
            </div>

            <h1>Tu directorio B2B <span>de confianza</span></h1>
            <p>Conectamos proveedores con compradores. Gestiona tu presencia, tus precios y tu catálogo desde un solo lugar.</p>

            <ul class="feature-list">
                <li>
                    <span class="icon"><i class="fas fa-store"></i></span>
                    Directorio completo de proveedores verificados
                </li>
                <li>
                    <span class="icon"><i class="fas fa-tags"></i></span>
                    Listas de precios personalizadas y exportación PDF
                </li>
                <li>
                    <span class="icon"><i class="fas fa-shield-alt"></i></span>
                    Acceso seguro con control de roles y permisos
                </li>
                <li>
                    <span class="icon"><i class="fas fa-chart-line"></i></span>
                    Analíticas y seguimiento de actividad en tiempo real
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="login-right">
        <div class="login-card">

            <!-- Mobile logo -->
            <div class="mobile-logo">
                <div class="mobile-logo-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="mobile-logo-text">MAOSA Prime</span>
            </div>

            <!-- Form header -->
            <div class="form-header">
                <h2>Bienvenido de nuevo</h2>
                <p>Ingresa tus credenciales para acceder a tu cuenta</p>
            </div>

            <!-- Alerts -->
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="margin-top:1px;flex-shrink:0;"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle" style="margin-top:1px;flex-shrink:0;"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Login form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Honeypot -->
                <div style="display:none;"><input type="text" name="honeypot" id="honeypot" value=""></div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="correo@empresa.com"
                            required
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()" id="toggleBtn" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="form-footer">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Back to home -->
            <a href="{{ route('start') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>

            <!-- Footer -->
            <div class="login-footer">
                &copy; {{ date('Y') }} MAOSA Prime. Todos los derechos reservados.
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Button loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            btn.disabled = true;
            btn.style.opacity = '0.8';
        });
    </script>

</body>
</html>
