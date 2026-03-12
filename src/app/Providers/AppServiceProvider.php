<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        config()->set('app.timezone', env('APP_TIMEZONE'));
        // set default pagination design
        Paginator::useBootstrap();

        // Configurar rate limiters con Redis como backend de caché
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiters backed by Redis.
     *
     * Al tener CACHE_DRIVER=redis, el RateLimiter usa Redis automáticamente.
     */
    protected function configureRateLimiting(): void
    {
        // API: 60 peticiones por minuto por IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Formulario de contacto: 5 envíos por hora por IP
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Login: 10 intentos por minuto por IP para frenar fuerza bruta
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
