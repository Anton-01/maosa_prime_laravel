<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>{{ config('settings.site_name') }}</title>
    <link rel="icon" type="image/png" href="{{ config('settings.favicon') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    <style>
        :root { --colorPrimary: {{ config('settings.site_default_color') }}; }
        html, body { height: 100%; overflow: hidden; margin: 0; padding: 0; }
        .gh-shell { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        .gh-nav   { flex: 0 0 auto; }
        .gh-main  { flex: 1 1 0; overflow: hidden; display: flex; min-height: 0; }
    </style>
    @stack('styles')
</head>
<body>

<div class="gh-shell">
    <div class="gh-nav">
        @include('frontend.layouts.navbar')
    </div>
    <div class="gh-main">
        @yield('contents')
    </div>
</div>

<script src="{{ asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('frontend/js/Font-Awesome.js') }}"></script>
<script src="{{ asset('frontend/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
