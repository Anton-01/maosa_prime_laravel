<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrecioPemexApiService
{
    public const FORMAT_HTML = 'html';

    public const FORMAT_EXCEL = 'Excel';

    public const FORMAT_PDF = 'pdf';

    public const FORMAT_IMAGE = 'imagen';

    /** Segundos de espera del layout HTML. */
    private const HTML_TIMEOUT = 30;

    private const EXPORT_TIMEOUT = 60;

    private const MAX_ATTENDANCE = 7;

    private string $baseUrl;

    private string $path;

    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.maosa_api.pemex_base_url') ?: config('services.maosa_api.base_url', ''),
            '/'
        );
        $this->path = '/' . trim((string) config('services.maosa_api.pemex_layout_path', ''), '/');
        $this->token = (string) config('services.maosa_api.token', '');

        Log::info('PrecioPemexApiService inicializado', [
            'base_url' => $this->baseUrl,
            'path' => $this->path,
            'token_configurado' => !empty($this->token),
            'token_preview' => $this->token,
        ]);
    }

    /**
     * Layout de una estación en el formato indicado.
     */
    public function layout(int $idBranch, string $format, ?string $effectiveDate = null): Response
    {
        return $this->layouts([$idBranch], $format, $effectiveDate)[$idBranch];
    }

    /**
     * Layout de varias estaciones en un solo barrido concurrente.
     *
     * Devuelve las respuestas indexadas por id de estación y en el mismo orden
     * en que se pidieron. Un fallo de red no rompe el lote: esa posición trae
     * la excepción para que el llamador decida qué mostrar.
     *
     * @param  array<int, int>  $idBranches
     * @return array<int, Response|ConnectionException>
     */
    public function layouts(array $idBranches, string $format, ?string $effectiveDate = null): array
    {

        $idBranches = array_values(array_unique(array_map('intval', $idBranches)));

        if ($idBranches === []) {
            Log::warning('PrecioPemexApiService::layouts llamado con array de estaciones vacío');
            return [];
        }

        $timeout = $format === self::FORMAT_HTML ? self::HTML_TIMEOUT : self::EXPORT_TIMEOUT;
        $accept = $this->accept($format);
        $query = $effectiveDate ? ['fecha_vigencia' => $effectiveDate] : [];

        Log::info('Preparando peticiones a la API MAOSA', [
            'estaciones' => $idBranches,
            'formato' => $format,
            'timeout' => $timeout,
            'query' => $query
        ]);

        $responses = [];

        foreach (array_chunk($idBranches, self::MAX_ATTENDANCE) as $index => $chunk) {
            Log::info("Enviando Chunk #{$index}", [
                'estaciones_en_chunk' => $chunk,
                'ejemplo_url' => $this->url($chunk[0], $format) . ($query ? '?' . http_build_query($query) : '')
            ]);

            // Capturamos el pool para poder loguear posibles caídas graves
            try {
                $chunkResponses = Http::pool(fn (Pool $pool) => array_map(
                    fn (int $id) => $pool->as((string) $id)
                        ->withHeaders($this->headers($accept))->timeout($timeout)->get($this->url($id, $format), $query),
                    $chunk,
                ));

                // Analizamos qué nos devolvió el pool para este chunk
                foreach ($chunkResponses as $key => $response) {
                    if ($response instanceof Response) {
                        Log::info("Respuesta de API para estación {$key}", [
                            'status' => $response->status(),
                            'successful' => $response->successful()
                        ]);
                    } elseif ($response instanceof Throwable) {
                        Log::error("Fallo de red/conexión para estación {$key}", [
                            'mensaje_error' => $response->getMessage(),
                            'clase_error' => get_class($response)
                        ]);
                    }
                }

                $responses += $chunkResponses;

            } catch (Throwable $e) {
                // Por si el pool entero crashea
                Log::critical("Fallo crítico ejecutando Http::pool en el chunk #{$index}", [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine()
                ]);
            }
        }

        // `Http::pool()` devuelve las claves como string; se reindexa por id.
        return collect($idBranches)->mapWithKeys(fn (int $id) => [$id => $responses[(string) $id] ?? $responses[$id] ?? null])->all();
    }

    private function url(int $idBranch, string $format): string
    {
        return "{$this->baseUrl}{$this->path}/{$idBranch}/{$format}";
    }

    private function accept(string $format): string
    {
        return match ($format) {
            self::FORMAT_EXCEL => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::FORMAT_PDF => 'application/pdf',
            self::FORMAT_IMAGE => 'image/*, application/zip',
            default => 'text/html',
        };
    }

    private function headers(string $accept): array
    {
        return ['Authorization' => "Bearer {$this->token}", 'Accept' => $accept,];
    }
}
