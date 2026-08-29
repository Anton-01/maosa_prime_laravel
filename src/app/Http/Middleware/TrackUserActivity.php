<?php

namespace App\Http\Middleware;

use App\Services\UserTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected UserTrackingService $trackingService;

    public function __construct(UserTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for certain paths
        if ($this->shouldSkipTracking($request)) {
            return $next($request);
        }

        // Get or create session
        $session = $this->trackingService->getOrCreateSession($request);

        // Track page visit
        $this->trackingService->trackPageVisit($request, $session->id);

        return $next($request);
    }

    /**
     * Determine if tracking should be skipped.
     *
     * Sólo se registran navegaciones reales: peticiones GET que devuelven una
     * pantalla. Las visitas de Inertia viajan como XHR (axios manda
     * `X-Requested-With: XMLHttpRequest`), así que no pueden descartarse por
     * ser AJAX o se perdería toda la navegación dentro del SPA; se reconocen
     * por la cabecera `X-Inertia`.
     */
    protected function shouldSkipTracking(Request $request): bool
    {
        // Sólo las lecturas de página cuentan como visita.
        if (!$request->isMethod('GET')) {
            return true;
        }

        $isInertiaVisit = $request->hasHeader('X-Inertia');

        // Recarga parcial de props: el usuario sigue en la misma página.
        if ($isInertiaVisit && $request->hasHeader('X-Inertia-Partial-Data')) {
            return true;
        }

        // Endpoints de datos (JSON, fragmentos, descargas): son acciones, no páginas.
        if (!$isInertiaVisit && ($request->ajax() || $request->expectsJson()) && !$request->is('api/track/*')) {
            return true;
        }

        // Skip asset requests
        $skipPaths = [
            '_debugbar/*',
            'livewire/*',
            'horizon/*',
            'telescope/*',
            'storage/*',
            'css/*',
            'js/*',
            'images/*',
            'fonts/*',
            'favicon.ico',
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        return false;
    }
}
