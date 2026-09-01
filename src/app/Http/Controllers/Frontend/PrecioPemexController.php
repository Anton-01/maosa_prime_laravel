<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrecioPemexLayoutRequest;
use App\Models\EstacionNacional;
use App\Models\User;
use App\Services\PrecioPemexApiService;
use App\Services\UserTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Client\Response as ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use ZipArchive;

/**
 * Submódulo "Precios PEMEX" (REQ-04 a REQ-07).
 *
 * Los cuatro endpoints de layout (HTML, Excel, pdf e imagen) son un proxy de
 * la API de Layout de Precios PEMEX y trabajan por lote: el navegador manda
 * **una sola** petición con las estaciones seleccionadas y la fecha de
 * vigencia, y aquí dentro se resuelven todas en paralelo y se devuelve el
 * resultado ya armado.
 *
 * Así el token nunca llega al navegador y en cada llamada se valida —vía
 * PrecioPemexLayoutRequest— que las estaciones estén asignadas al usuario y
 * que la fecha sea ayer, hoy o mañana.
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
                'html' => route('user.precio-pemex.html'),
                'excel' => route('user.precio-pemex.excel'),
                'pdf' => route('user.precio-pemex.pdf'),
                'imagen' => route('user.precio-pemex.imagen'),
            ],
            // Las tres únicas fechas seleccionables; el backend valida lo mismo.
            'dates' => [
                'min' => Carbon::yesterday()->toDateString(),
                'today' => Carbon::today()->toDateString(),
                'max' => Carbon::tomorrow()->toDateString(),
            ],
            'maxStations' => PrecioPemexLayoutRequest::MAX_ESTACIONES,
            'stylesheet' => asset('frontend/css/maosa/price_national_layout.css'),
        ]);
    }

    /**
     * REQ-05: layouts HTML de todas las estaciones seleccionadas en una sola
     * respuesta, cada fragmento con su estación para poder renderizarlos ya
     * agrupados en el cliente.
     */
    public function html(PrecioPemexLayoutRequest $request): JsonResponse
    {
        $idEstaciones = $request->estaciones();
        $fechaVigencia = $request->fechaVigencia();

        $respuestas = $this->api->layouts($idEstaciones, PrecioPemexApiService::FORMATO_HTML, $fechaVigencia);
        $nombres = $this->stationNames($idEstaciones);

        $layouts = [];
        $fallidas = [];

        foreach ($idEstaciones as $idEstacion) {
            $respuesta = $respuestas[$idEstacion] ?? null;
            $exitosa = $respuesta instanceof ApiResponse && $respuesta->successful();
            $estado = $respuesta instanceof ApiResponse ? $respuesta->status() : 503;

            if (! $exitosa) {
                $fallidas[] = $idEstacion;
            }

            $layouts[] = [
                'id_estacion' => $idEstacion,
                'estacion' => $nombres[$idEstacion] ?? "Estación {$idEstacion}",
                'ok' => $exitosa,
                'estado_http' => $estado,
                'html' => $exitosa ? $respuesta->body() : $this->htmlMessage($estado),
            ];
        }

        $this->logActivity(
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_CONSULTA,
            'Consultó los precios PEMEX de ' . count($idEstaciones) . ' estación(es)',
            $idEstaciones,
            PrecioPemexApiService::FORMATO_HTML,
            $fechaVigencia,
            ['estaciones_fallidas' => $fallidas],
        );

        return response()->json([
            'fecha_vigencia' => $fechaVigencia,
            'layouts' => $layouts,
            'estaciones_fallidas' => $fallidas,
        ]);
    }

    /**
     * REQ-07: descarga del layout en Excel (.xlsx). Con varias estaciones se
     * devuelve un único .zip con un archivo por estación.
     */
    public function excel(PrecioPemexLayoutRequest $request): Response
    {
        return $this->descargaPorLote(
            $request,
            PrecioPemexApiService::FORMATO_EXCEL,
            'xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_EXCEL,
            'Descargó el Excel de precios PEMEX',
        );
    }

    /**
     * REQ-07: descarga del layout en PDF (zip con varias estaciones).
     */
    public function pdf(PrecioPemexLayoutRequest $request): Response
    {
        return $this->descargaPorLote(
            $request,
            PrecioPemexApiService::FORMATO_PDF,
            'pdf',
            'application/pdf',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_PDF,
            'Descargó el PDF de precios PEMEX',
        );
    }

    /**
     * Descarga del layout como imagen. La API responde con una imagen o con un
     * zip cuando el layout se compone de varias piezas, así que la extensión
     * se toma de la propia respuesta.
     */
    public function imagen(PrecioPemexLayoutRequest $request): Response
    {
        return $this->descargaPorLote(
            $request,
            PrecioPemexApiService::FORMATO_IMAGEN,
            'png',
            'image/png',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_IMAGEN,
            'Descargó la imagen de precios PEMEX',
        );
    }

    /**
     * Resuelve el lote completo contra la API y arma la descarga:
     *   - una estación  -> el archivo tal cual lo devolvió la API;
     *   - varias        -> un .zip con un archivo por estación.
     *
     * Las estaciones que fallan no tumban la descarga: se omiten y se anuncian
     * en la cabecera `X-Estaciones-Fallidas` para que el cliente avise.
     */
    private function descargaPorLote(
        PrecioPemexLayoutRequest $request,
        string $formato,
        string $extensionPorDefecto,
        string $contentTypePorDefecto,
        string $tipoActividad,
        string $descripcionActividad,
    ): Response {
        $idEstaciones = $request->estaciones();
        $fechaVigencia = $request->fechaVigencia();

        $respuestas = $this->api->layouts($idEstaciones, $formato, $fechaVigencia);

        $archivos = [];
        $fallidas = [];

        foreach ($idEstaciones as $idEstacion) {
            $respuesta = $respuestas[$idEstacion] ?? null;

            if (! $respuesta instanceof ApiResponse || ! $respuesta->successful()) {
                $fallidas[] = $idEstacion;
                continue;
            }

            $extension = $this->extensionDe($respuesta, $extensionPorDefecto);

            $archivos[] = [
                'id_estacion' => $idEstacion,
                'nombre' => "precios-pemex-estacion-{$idEstacion}-{$fechaVigencia}.{$extension}",
                'contenido' => $respuesta->body(),
                'content_type' => $respuesta->header('Content-Type') ?: $contentTypePorDefecto,
            ];
        }

        // Se registra después de resolver el lote para no contar los intentos fallidos.
        $this->logActivity(
            $tipoActividad,
            $descripcionActividad . ' de ' . count($archivos) . ' estación(es)',
            $idEstaciones,
            $formato,
            $fechaVigencia,
            ['estaciones_fallidas' => $fallidas],
        );

        if ($archivos === []) {
            abort(502, 'No fue posible obtener los archivos de precios. Intente más tarde.');
        }

        $cabeceras = $fallidas === []
            ? []
            : ['X-Estaciones-Fallidas' => implode(',', $fallidas)];

        if (count($archivos) === 1) {
            return $this->archivo(
                $archivos[0]['contenido'],
                $archivos[0]['nombre'],
                $archivos[0]['content_type'],
                $cabeceras,
            );
        }

        return $this->archivo(
            $this->comprimir($archivos),
            "precios-pemex-{$formato}-{$fechaVigencia}.zip",
            'application/zip',
            $cabeceras,
        );
    }

    /**
     * Empaqueta las respuestas del lote en un único zip en memoria.
     *
     * @param  array<int, array{nombre: string, contenido: string}>  $archivos
     */
    private function comprimir(array $archivos): string
    {
        $ruta = tempnam(sys_get_temp_dir(), 'precios_pemex_');

        if ($ruta === false) {
            abort(500, 'No fue posible preparar la descarga.');
        }

        $zip = new ZipArchive();

        if ($zip->open($ruta, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            @unlink($ruta);
            abort(500, 'No fue posible preparar la descarga.');
        }

        try {
            foreach ($archivos as $archivo) {
                $zip->addFromString($archivo['nombre'], $archivo['contenido']);
            }

            $zip->close();

            return (string) file_get_contents($ruta);
        } finally {
            @unlink($ruta);
        }
    }

    /**
     * @param  array<string, string>  $cabecerasExtra
     */
    private function archivo(string $contenido, string $nombre, string $contentType, array $cabecerasExtra = []): Response
    {
        return response($contenido, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
            'Content-Length' => (string) strlen($contenido),
            ...$cabecerasExtra,
        ]);
    }

    /**
     * Extensión real del archivo devuelto por la API: primero la que venga en
     * el `Content-Disposition`, si no la que corresponda al `Content-Type`
     * (el endpoint de imagen puede responder png/jpg o un zip).
     */
    private function extensionDe(ApiResponse $respuesta, string $porDefecto): string
    {
        $disposition = (string) $respuesta->header('Content-Disposition');

        if (preg_match('/filename\*?=(?:UTF-8\'\')?"?([^";]+)"?/i', $disposition, $coincidencias)) {
            $extension = pathinfo(trim($coincidencias[1]), PATHINFO_EXTENSION);

            if ($extension !== '' && preg_match('/^[A-Za-z0-9]{1,5}$/', $extension)) {
                return strtolower($extension);
            }
        }

        $contentType = strtolower(trim(explode(';', (string) $respuesta->header('Content-Type'))[0]));

        return match ($contentType) {
            'image/png' => 'png',
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'application/zip', 'application/x-zip-compressed', 'application/octet-stream' => 'zip',
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel' => 'xls',
            default => $porDefecto,
        };
    }

    /**
     * Deja traza de lo que el usuario hace dentro del módulo (consultas y
     * descargas por lote). La actividad se ata a la última visita de página
     * registrada, así queda dentro de la sesión de navegación.
     *
     * @param  array<int, int>  $idEstaciones
     * @param  array<string, mixed>  $extra
     */
    private function logActivity(
        string $tipo,
        string $descripcion,
        array $idEstaciones,
        string $formato,
        string $fechaVigencia,
        array $extra = [],
    ): void {
        $this->tracking->logActivity($tipo, $descripcion, [
            'modulo' => 'precios_pemex',
            'estaciones' => $idEstaciones,
            'total_estaciones' => count($idEstaciones),
            'fecha_vigencia' => $fechaVigencia,
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

        $nombres = $this->stationNames($ids);

        return collect($ids)
            ->map(fn (int $id) => [
                'id_estacion' => $id,
                'estacion' => $nombres[$id] ?? "Estación {$id}",
            ])
            ->sortBy('estacion', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * Nombre de cada estación del catálogo nacional, indexado por id.
     *
     * @param  array<int, int>  $ids
     * @return array<int, string>
     */
    private function stationNames(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return EstacionNacional::activasPorIds($ids)
            ->mapWithKeys(fn (EstacionNacional $estacion) => [
                (int) $estacion->id_estacion => (string) ($estacion->estacion ?: "Estación {$estacion->id_estacion}"),
            ])
            ->all();
    }

    /**
     * Mensaje HTML de reemplazo cuando la API no devuelve el layout, para que
     * la tarjeta de esa estación muestre algo legible en lugar de vaciarse.
     */
    private function htmlMessage(int $status): string
    {
        $mensaje = match (true) {
            $status === 404 => 'Sin precios disponibles para esta estación en la fecha seleccionada.',
            $status === 401 || $status === 403 => 'Error de autenticación con la API de precios.',
            default => 'Error al obtener los precios. Intente más tarde.',
        };

        return '<p style="text-align:center;padding:32px;margin:0;color:#64748b">' . e($mensaje) . '</p>';
    }
}
