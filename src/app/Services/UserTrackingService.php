<?php

namespace App\Services;

use App\Models\PageVisit;
use App\Models\UserActivity;
use App\Models\UserNavigationFlow;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class UserTrackingService
{
    /** Consulta del layout de precios internacionales de una estación. */
    public const ACTIVITY_PRECIOS_INTERNACIONALES_CONSULTA = 'precios_internacionales.consulta';

    /** Descarga del PDF de precios internacionales. */
    public const ACTIVITY_PRECIOS_INTERNACIONALES_PDF = 'precios_internacionales.descarga_pdf';

    /** Consulta del layout de precios PEMEX de una estación. */
    public const ACTIVITY_PRECIOS_PEMEX_CONSULTA = 'precios_pemex.consulta';

    /** Descarga del Excel de precios PEMEX. */
    public const ACTIVITY_PRECIOS_PEMEX_EXCEL = 'precios_pemex.descarga_excel';

    /** Descarga del PDF de precios PEMEX. */
    public const ACTIVITY_PRECIOS_PEMEX_PDF = 'precios_pemex.descarga_pdf';

    /** Descarga de la imagen (o zip de imágenes) de precios PEMEX. */
    public const ACTIVITY_PRECIOS_PEMEX_IMAGEN = 'precios_pemex.descarga_imagen';

    protected ?Agent $agent = null;

    /**
     * Get or create user session.
     */
    public function getOrCreateSession(Request $request): UserSession
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        // Try to find existing active session
        $userSession = UserSession::where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$userSession) {
            $userSession = $this->createSession($request, $sessionId, $userId);
        } elseif ($userId && !$userSession->user_id) {
            // User logged in during session
            $userSession->update(['user_id' => $userId]);
        }

        // Store session ID in Laravel session
        session(['user_session_id' => $userSession->id]);

        return $userSession;
    }

    /**
     * Create a new user session.
     */
    protected function createSession(Request $request, string $sessionId, ?int $userId): UserSession
    {
        $this->agent = new Agent();
        $this->agent->setUserAgent($request->userAgent());

        $geoData = $this->getGeoLocation($request->ip());

        return UserSession::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->getDeviceType(),
            'browser' => $this->agent->browser(),
            'browser_version' => $this->agent->version($this->agent->browser()),
            'platform' => $this->agent->platform(),
            'platform_version' => $this->agent->version($this->agent->platform()),
            'country' => $geoData['country'] ?? null,
            'city' => $geoData['city'] ?? null,
            'region' => $geoData['region'] ?? null,
            'latitude' => $geoData['latitude'] ?? null,
            'longitude' => $geoData['longitude'] ?? null,
            'started_at' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * Get device type.
     */
    protected function getDeviceType(): string
    {
        if ($this->agent->isTablet()) {
            return 'tablet';
        }
        if ($this->agent->isMobile()) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Get geo location from IP, cached in Redis for 24 hours.
     */
    protected function getGeoLocation(string $ip): array
    {
        // Skip for local IPs
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return [];
        }

        return Cache::remember("geo_location:{$ip}", now()->addHours(24), function () use ($ip) {
            try {
                // Using ip-api.com free service (limit 45 requests per minute)
                $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");

                if ($response) {
                    $data = json_decode($response, true);

                    if ($data && $data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'region' => $data['regionName'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['lat'] ?? null,
                            'longitude' => $data['lon'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Silently fail
            }
            return [];
        });
    }

    /**
     * Track a page visit.
     */
    public function trackPageVisit(Request $request, ?int $userSessionId = null): PageVisit
    {
        $userId = auth()->id();
        $sessionId = $userSessionId ?? session('user_session_id');

        // Update previous page visit's left_at and time_on_page
        $this->updatePreviousPageVisit($sessionId);

        // Create new page visit
        $pageVisit = PageVisit::create([
            'user_id' => $userId,
            'user_session_id' => $sessionId,
            'url' => $request->fullUrl(),
            'route_name' => $request->route()?->getName(),
            'page_title' => $this->resolvePageTitle($request),
            'referrer' => $request->header('referer'),
            'ip_address' => $request->ip(),
            'visited_at' => now(),
        ]);

        // Track navigation flow
        $this->trackNavigationFlow($request, $pageVisit, $sessionId);

        // Store current page visit ID in session
        session(['current_page_visit_id' => $pageVisit->id]);

        return $pageVisit;
    }

    /**
     * Nombre legible de la página a partir del nombre de la ruta
     * (config/tracking.php). Permite agrupar las visitas por módulo en las
     * estadísticas en vez de por URL suelta.
     */
    protected function resolvePageTitle(Request $request): ?string
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return null;
        }

        return config("tracking.page_titles.{$routeName}");
    }

    /**
     * Update previous page visit.
     */
    protected function updatePreviousPageVisit(?int $sessionId): void
    {
        $previousVisitId = session('current_page_visit_id');

        if ($previousVisitId) {
            $previousVisit = PageVisit::find($previousVisitId);

            if ($previousVisit && !$previousVisit->left_at) {
                $timeOnPage = (int) now()->diffInSeconds($previousVisit->visited_at);
                $previousVisit->update([
                    'left_at' => now(),
                    'time_on_page' => $timeOnPage,
                ]);
            }
        }
    }

    /**
     * Track navigation flow.
     */
    protected function trackNavigationFlow(Request $request, PageVisit $toPageVisit, ?int $sessionId): void
    {
        $previousVisitId = session('current_page_visit_id');
        $previousUrl = $request->header('referer');

        if ($previousVisitId || $previousUrl) {
            UserNavigationFlow::create([
                'user_id' => auth()->id(),
                'user_session_id' => $sessionId,
                'from_page_visit_id' => $previousVisitId,
                'to_page_visit_id' => $toPageVisit->id,
                'from_url' => $previousUrl,
                'to_url' => $request->fullUrl(),
                'transition_time' => $previousVisitId ? now()->diffInMilliseconds(PageVisit::find($previousVisitId)?->visited_at) : null,
            ]);
        }
    }

    /**
     * Log an activity.
     */
    public function logActivity(
        string $type,
        ?string $description = null,
        ?array $metadata = null
    ): ?UserActivity {
        try {
            return UserActivity::create([
                'user_id' => auth()->id(),
                'user_session_id' => session('user_session_id'),
                'page_visit_id' => session('current_page_visit_id'),
                'activity_type' => $type,
                'activity_description' => $description,
                'url' => request()->fullUrl(),
                'metadata' => $metadata,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // La trazabilidad nunca debe tumbar la acción del usuario
            // (una descarga, por ejemplo): se registra el fallo y se sigue.
            logger()->warning('No fue posible registrar la actividad del usuario', [
                'activity_type' => $type,
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * End a session.
     */
    public function endSession(): void
    {
        $sessionId = session('user_session_id');

        if ($sessionId) {
            $session = UserSession::find($sessionId);

            if ($session) {
                $session->update([
                    'ended_at' => now(),
                    'is_active' => false,
                ]);
            }

            // Update last page visit
            $this->updatePreviousPageVisit($sessionId);
        }
    }
}
