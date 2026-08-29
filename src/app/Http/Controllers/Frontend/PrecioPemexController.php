<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\EstacionNacional;
use App\Models\User;
use App\Services\PrecioPemexApiService;
use App\Services\UserTrackingService;
use Illuminate\Http\Client\Response as ApiResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Submódulo "Precios PEMEX" (REQ-04 a REQ-07).
 *
 * Los tres endpoints de layout son un proxy de la API de Layout de Precios
 * PEMEX: conservan la ruta pública documentada
 * (`/api/precio_pemex/layout/estacion/{id_estacion}/{formato}`) pero se sirven
 * desde la aplicación para que el token nunca llegue al navegador y para
 * validar, en cada llamada, que la estación esté asignada al usuario.
 */
class PrecioPemexController extends Controller
{
    public function __construct(
        private readonly PrecioPemexApiService $api,
        private readonly UserTrackingService $tracking,
    ) {}

    /**
     * Vista del submódulo con las estaciones asignadas al usuario (REQ-06).
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('User/PreciosPemex', [
            'stations' => $this->stationsForUser(),
            'endpoints' => [
                // El cliente arma {layoutBase}/{id_estacion}/{HTML|Excel|pdf}.
                'layoutBase' => url('/api/precio_pemex/layout/estacion'),
            ],
            'stylesheet' => asset('frontend/css/maosa/price_national_layout.css'),
        ]);
    }

    /**
     * REQ-05: layout HTML de la estación, listo para inyectarse en la vista.
     */
    public function html(int $idEstacion): Response
    {
        $this->authorizeStation($idEstacion);

        $response = $this->api->layoutHtml($idEstacion);

        $this->logActivity(
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_CONSULTA,
            "Consultó los precios PEMEX de la estación {$idEstacion}",
            $idEstacion,
            'HTML',
            ['estado_http' => $response->status()],
        );

        if (! $response->successful()) {
            return response($this->htmlMessage($response->status()), 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return response($response->body(), 200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * REQ-07: descarga del layout en Excel (.xlsx).
     */
    public function excel(int $idEstacion): Response
    {
        $this->authorizeStation($idEstacion);

        $archivo = $this->download(
            $this->api->layoutExcel($idEstacion),
            "precios-pemex-estacion-{$idEstacion}-" . now()->format('Y-m-d') . '.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

        // Se registra después de la descarga para no contar los intentos fallidos.
        $this->logActivity(
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_EXCEL,
            "Descargó el Excel de precios PEMEX de la estación {$idEstacion}",
            $idEstacion,
            'Excel',
        );

        return $archivo;
    }

    /**
     * REQ-07: descarga del layout en PDF.
     */
    public function pdf(int $idEstacion): Response
    {
        $this->authorizeStation($idEstacion);

        $archivo = $this->download(
            $this->api->layoutPdf($idEstacion),
            "precios-pemex-estacion-{$idEstacion}-" . now()->format('Y-m-d') . '.pdf',
            'application/pdf',
        );

        $this->logActivity(
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_PDF,
            "Descargó el PDF de precios PEMEX de la estación {$idEstacion}",
            $idEstacion,
            'pdf',
        );

        return $archivo;
    }

    /**
     * Deja traza de lo que el usuario hace dentro del módulo (consultas y
     * descargas por estación). La actividad se ata a la última visita de
     * página registrada, así queda dentro de la sesión de navegación.
     */
    private function logActivity(
        string $tipo,
        string $descripcion,
        int $idEstacion,
        string $formato,
        array $extra = [],
    ): void {
        $this->tracking->logActivity($tipo, $descripcion, [
            'modulo' => 'precios_pemex',
            'id_estacion' => $idEstacion,
            'formato' => $formato,
            ...$extra,
        ]);
    }

    /**
     * Estaciones asignadas al usuario, resueltas contra el catálogo nacional.
     * Si la foreign table no responde se conservan los ids de la pivote para
     * que el módulo siga siendo utilizable.
     *
     * @return array<int, array{id_estacion: int, estacion: string}>
     */
    private function stationsForUser(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $ids = $user->estacionesAsignadasIds();

        if ($ids === []) {
            return [];
        }

        $catalogo = EstacionNacional::activasPorIds($ids)
            ->keyBy(fn (EstacionNacional $estacion) => (int) $estacion->id_estacion);

        return collect($ids)
            ->map(fn (int $id) => [
                'id_estacion' => $id,
                'estacion' => $catalogo->get($id)?->estacion ?: "Estación {$id}",
            ])
            ->sortBy('estacion', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * Sólo se consulta lo que el usuario tiene asignado en `usuario_estacion`.
     */
    private function authorizeStation(int $idEstacion): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->tieneEstacionAsignada($idEstacion)) {
            abort(403, 'No tiene acceso a esta estación.');
        }
    }

    /**
     * Reenvía el binario de la API como descarga, propagando los errores
     * como códigos HTTP para que el cliente pueda avisar al usuario.
     */
    private function download(ApiResponse $response, string $filename, string $contentType): Response
    {
        if ($response->status() === 404) {
            abort(404, 'No hay layout de precios disponible para esta estación.');
        }

        if (! $response->successful()) {
            abort(502, 'No fue posible obtener el archivo de precios. Intente más tarde.');
        }

        return response($response->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length' => (string) strlen($response->body()),
        ]);
    }

    /**
     * Mensaje HTML de reemplazo cuando la API no devuelve el layout, para que
     * el contenedor del módulo muestre algo legible en lugar de vaciarse.
     */
    private function htmlMessage(int $status): string
    {
        $mensaje = match (true) {
            $status === 404 => 'Sin precios disponibles para esta estación.',
            $status === 401 || $status === 403 => 'Error de autenticación con la API de precios.',
            default => 'Error al obtener los precios. Intente más tarde.',
        };

        return '<p style="text-align:center;padding:32px;margin:0;color:#64748b">' . e($mensaje) . '</p>';
    }
}
