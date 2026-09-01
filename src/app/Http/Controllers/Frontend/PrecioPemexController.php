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
use Illuminate\Support\Facades\Log;

class PrecioPemexController extends Controller
{
    public function __construct(private readonly PrecioPemexApiService $api, private readonly UserTrackingService $tracking) {}


    public function index(): InertiaResponse
    {

        Log::info('Carga de Index PrecioPemex', [
            'user_id' => auth()->id(),
            'cantidad_estaciones_front'
        ]);

        return Inertia::render('User/PreciosPemex', [
            'stations' => $this->stationsForUser(),
            'endpoints' => [
                'html' => route('user.precio-pemex.html'),
                'excel' => route('user.precio-pemex.excel'),
                'pdf' => route('user.precio-pemex.pdf'),
                'imagen' => route('user.precio-pemex.imagen'),
            ],
            'dates' => [
                'min' => Carbon::yesterday()->toDateString(),
                'today' => Carbon::today()->toDateString(),
                'max' => Carbon::tomorrow()->toDateString(),
            ],
            'maxStations' => PrecioPemexLayoutRequest::MAX_ESTACIONES,
            'stylesheet' => asset('frontend/css/maosa/price_national_layout.css'),
        ]);
    }

    public function html(PrecioPemexLayoutRequest $request): JsonResponse
    {
        $idBranches = $request->estaciones();
        $effectiveDate = $request->fechaVigencia();

        Log::info('Petición HTML recibida', [
            'estaciones_solicitadas' => $idBranches,
            'fecha_vigencia' => $effectiveDate
        ]);

        $responses = $this->api->layouts($idBranches, PrecioPemexApiService::FORMAT_HTML, $effectiveDate);
        $names = $this->stationNames($idBranches);

        $layouts = [];
        $failed = [];

        foreach ($idBranches as $idBranch) {
            $response = $responses[$idBranch] ?? null;
            $successful = $response instanceof ApiResponse && $response->successful();
            $estado = $response instanceof ApiResponse ? $response->status() : 503;

            if (! $successful) {
                Log::warning("Fallo al obtener HTML de la API para la estación {$idBranch}", [
                    'estado_http' => $estado,
                    'tiene_respuesta' => $response instanceof ApiResponse
                ]);
                $failed[] = $idBranch;
            }

            $layouts[] = [
                'id_estacion' => $idBranch,
                'estacion' => $names[$idBranch] ?? "Estación {$idBranch}",
                'ok' => $successful,
                'estado_http' => $estado,
                'html' => $successful ? $response->body() : $this->htmlMessage($estado),
            ];
        }

        $this->logActivity(
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_CONSULTA,
            'Consultó los precios PEMEX de ' . count($idBranches) . ' estación(es)',
            $idBranches, PrecioPemexApiService::FORMAT_HTML, $effectiveDate, ['estaciones_fallidas' => $failed],
        );

        Log::info('Respuesta HTML a enviar', [
            'total_layouts' => count($layouts),
            'total_fallidas' => count($failed)
        ]);

        return response()->json(['fecha_vigencia' => $effectiveDate, 'layouts' => $layouts, 'estaciones_fallidas' => $failed,]);
    }

    public function excel(PrecioPemexLayoutRequest $request): Response
    {
        return $this->downloadByChunk(
            $request, PrecioPemexApiService::FORMAT_EXCEL, 'xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_EXCEL,
            'Descargó el Excel de precios PEMEX',
        );
    }

    public function pdf(PrecioPemexLayoutRequest $request): Response
    {
        return $this->downloadByChunk(
            $request,
            PrecioPemexApiService::FORMAT_PDF, 'pdf',
            'application/pdf',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_PDF,
            'Descargó el PDF de precios PEMEX',
        );
    }

    public function imagen(PrecioPemexLayoutRequest $request): Response
    {
        return $this->downloadByChunk($request,
            PrecioPemexApiService::FORMAT_IMAGE, 'png', 'image/png',
            UserTrackingService::ACTIVITY_PRECIOS_PEMEX_IMAGEN,
            'Descargó la imagen de precios PEMEX',
        );
    }
    private function downloadByChunk(PrecioPemexLayoutRequest $request, string $format, string $defaultExtension, string $contentTypeDefault, string $activityType, string $activityDescription,): Response {
        $idBranches = $request->estaciones();
        $effectiveDate = $request->fechaVigencia();

        Log::info("Iniciando descarga por chunk - Formato: {$format}", [
            'estaciones' => $idBranches,
            'fecha' => $effectiveDate
        ]);

        $responses = $this->api->layouts($idBranches, $format, $effectiveDate);

        $files = [];
        $failed = [];

        foreach ($idBranches as $idBranch) {
            $response = $responses[$idBranch] ?? null;

            if (! $response instanceof ApiResponse || ! $response->successful()) {
                Log::warning("Descarga fallida para estación {$idBranch}", ['estado_http' => $status]);
                $failed[] = $idBranch;
                continue;
            }

            $extension = $this->extensionDe($response, $defaultExtension);

            $files[] = [
                'id_estacion' => $idBranch,
                'nombre' => "precios-pemex-estacion-{$idBranch}-{$effectiveDate}.{$extension}",
                'contenido' => $response->body(),
                'content_type' => $response->header('Content-Type') ?: $contentTypeDefault,
            ];
        }

        // It is recorded after the set is completed so that failed attempts are not counted.
        $this->logActivity($activityType, $activityDescription . ' de ' . count($files) . ' estación(es)',
            $idBranches, $format, $effectiveDate, ['estaciones_fallidas' => $failed],
        );

        if ($files === []) {
            Log::error("Abortando 502: Ningún archivo se pudo procesar. Total fallidas: " . count($failed));
            abort(502, 'No fue posible obtener los archivos de precios. Intente más tarde.');
        }

        $headers = $failed === [] ? [] : ['X-Estaciones-Fallidas' => implode(',', $failed)];

        if (count($files) === 1) {
            return $this->archivo($files[0]['contenido'], $files[0]['nombre'], $files[0]['content_type'], $headers,);
        }
        Log::info("Comprimiendo " . count($files) . " archivos en ZIP");
        return $this->archivo($this->comprimir($files), "precios-pemex-{$format}-{$effectiveDate}.zip", 'application/zip', $headers);
    }

    /**
     * Empaqueta las respuestas del lote en un único zip en memoria.
     *
     * @param  array<int, array{nombre: string, contenido: string}>  $files
     */
    private function comprimir(array $files): string
    {
        $ruta = tempnam(sys_get_temp_dir(), 'precios_pemex_');

        if ($ruta === false) {
            Log::error('comprimir(): tempnam falló al crear archivo temporal');
            abort(500, 'No fue posible preparar la descarga.');
        }

        $zip = new ZipArchive();

        if ($zip->open($ruta, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            Log::error('comprimir(): ZipArchive falló al abrir la ruta', ['ruta' => $ruta]);
            @unlink($ruta);
            abort(500, 'No fue posible preparar la descarga.');
        }

        try {
            foreach ($files as $archivo) {
                $zip->addFromString($archivo['nombre'], $archivo['contenido']);
            }

            $zip->close();

            return (string) file_get_contents($ruta);
        } finally {
            @unlink($ruta);
        }
    }

    /**
     * @param  array<string, string>  $headersExtra
     */
    private function archivo(string $body, string $name, string $contentType, array $headersExtra = []): Response
    {
        return response($body, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$name}\"",
            'Content-Length' => (string) strlen($body),
            ...$headersExtra,
        ]);
    }

    private function extensionDe(ApiResponse $response, string $default): string
    {
        $disposition = $response->header('Content-Disposition');

        if (preg_match('/filename\*?=(?:UTF-8\'\')?"?([^";]+)"?/i', $disposition, $matches)) {
            $extension = pathinfo(trim($matches[1]), PATHINFO_EXTENSION);

            if ($extension !== '' && preg_match('/^[A-Za-z0-9]{1,5}$/', $extension)) {
                return strtolower($extension);
            }
        }

        $contentType = strtolower(trim(explode(';', $response->header('Content-Type'))[0]));

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
            default => $default,
        };
    }

    private function logActivity(string $type, string $description, array $idBranches, string $format, string $effectiveDate, array $extra = []): void {
        $this->tracking->logActivity($type, $description, [
            'modulo' => 'precios_pemex',
            'estaciones' => $idBranches,
            'total_estaciones' => count($idBranches),
            'fecha_vigencia' => $effectiveDate,
            'formato' => $format,
            ...$extra,
        ]);
    }

    private function stationsForUser(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $ids = $user->estacionesAsignadasIds();

        if ($ids === []) {
            Log::warning('stationsForUser(): El usuario NO tiene IDs de estaciones asignadas', ['user_id' => $user->id]);
            return [];
        }
        Log::info('stationsForUser(): IDs asignados obtenidos', ['ids' => $ids]);

        $names = $this->stationNames($ids);

        return collect($ids)->map(fn (int $id) => ['id_estacion' => $id, 'estacion' => $names[$id] ?? "Estación {$id}",])
            ->sortBy('estacion', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();
    }

    private function stationNames(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return EstacionNacional::activasPorIds($ids)->mapWithKeys(fn (EstacionNacional $branch) => [(int) $branch->id_estacion => (string) ($branch->estacion ?: "Estación {$branch->id_estacion}"),])->all();
    }

    private function htmlMessage(int $status): string
    {
        $message = match (true) {
            $status === 404 => 'Sin precios disponibles para esta estación en la fecha seleccionada.',
            $status === 401 || $status === 403 => 'Error de autenticación con la API de precios.',
            default => 'Error al obtener los precios. Intente más tarde.',
        };

        return '<p style="text-align:center;padding:32px;margin:0;color:#64748b">' . e($message) . '</p>';
    }
}
