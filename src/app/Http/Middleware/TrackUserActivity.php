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
     */
    protected function shouldSkipTracking(Request $request): bool
    {
        // Skip AJAX requests (except specific ones)
        if ($request->ajax() && !$request->is('api/track/*')) {
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
