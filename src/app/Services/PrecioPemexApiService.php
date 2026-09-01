<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Cliente de la API de Layout de Precios PEMEX (REQ-05 / REQ-07).
 *
 * Los cuatro formatos del layout nacional cuelgan del mismo path base
 * (`services.maosa_api.pemex_layout_path`), lo único que cambia es el último
 * segmento y el `Accept`:
 *
 *   GET {base}{path}/{id_estacion}/HTML?fecha_vigencia=YYYY-MM-DD
 *   GET {base}{path}/{id_estacion}/Excel?fecha_vigencia=YYYY-MM-DD
 *   GET {base}{path}/{id_estacion}/pdf?fecha_vigencia=YYYY-MM-DD
 *   GET {base}{path}/{id_estacion}/imagen?fecha_vigencia=YYYY-MM-DD  (imagen o zip)
 *
 * El token vive sólo del lado del servidor: el navegador nunca habla
 * directamente con la API, siempre pasa por el proxy de la aplicación.
 *
 * Cuando se piden varias estaciones las peticiones salen en paralelo con
 * `Http::pool()` en lotes de `MAX_CONCURRENCIA`, así N estaciones no cuestan
 * N tiempos de espera encadenados ni saturan la API de un golpe.
 */
class PrecioPemexApiService
{
    public const FORMATO_HTML = 'HTML';

    public const FORMATO_EXCEL = 'Excel';

    public const FORMATO_PDF = 'pdf';

    public const FORMATO_IMAGEN = 'imagen';

    /** Segundos de espera del layout HTML. */
    private const HTML_TIMEOUT = 30;

    /** Segundos de espera para las exportaciones (Excel/PDF/imagen son más lentas). */
    private const EXPORT_TIMEOUT = 60;

    /** Peticiones simultáneas por lote contra la API externa. */
    private const MAX_CONCURRENCIA = 5;

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
    }

    /**
     * Layout de una estación en el formato indicado.
     */
    public function layout(int $idEstacion, string $formato, ?string $fechaVigencia = null): Response
    {
        return $this->layouts([$idEstacion], $formato, $fechaVigencia)[$idEstacion];
    }

    /**
     * Layout de varias estaciones en un solo barrido concurrente.
     *
     * Devuelve las respuestas indexadas por id de estación y en el mismo orden
     * en que se pidieron. Un fallo de red no rompe el lote: esa posición trae
     * la excepción para que el llamador decida qué mostrar.
     *
     * @param  array<int, int>  $idEstaciones
     * @return array<int, Response|\Illuminate\Http\Client\ConnectionException>
     */
    public function layouts(array $idEstaciones, string $formato, ?string $fechaVigencia = null): array
    {
        // Nunca se consulta dos veces la misma estación, aunque llegue repetida.
        $idEstaciones = array_values(array_unique(array_map('intval', $idEstaciones)));

        if ($idEstaciones === []) {
            return [];
        }

        $timeout = $formato === self::FORMATO_HTML ? self::HTML_TIMEOUT : self::EXPORT_TIMEOUT;
        $accept = $this->accept($formato);
        $query = $fechaVigencia ? ['fecha_vigencia' => $fechaVigencia] : [];

        $respuestas = [];

        foreach (array_chunk($idEstaciones, self::MAX_CONCURRENCIA) as $lote) {
            $respuestas += Http::pool(fn (Pool $pool) => array_map(
                fn (int $id) => $pool->as((string) $id)
                    ->withHeaders($this->headers($accept))
                    ->timeout($timeout)
                    ->get($this->url($id, $formato), $query),
                $lote,
            ));
        }

        // `Http::pool()` devuelve las claves como string; se reindexa por id.
        return collect($idEstaciones)
            ->mapWithKeys(fn (int $id) => [$id => $respuestas[(string) $id] ?? $respuestas[$id] ?? null])
            ->all();
    }

    private function url(int $idEstacion, string $formato): string
    {
        return "{$this->baseUrl}{$this->path}/{$idEstacion}/{$formato}";
    }

    private function accept(string $formato): string
    {
        return match ($formato) {
            self::FORMATO_EXCEL => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::FORMATO_PDF => 'application/pdf',
            // El endpoint de imagen responde con una imagen o con un zip cuando
            // el layout se compone de varias piezas.
            self::FORMATO_IMAGEN => 'image/*, application/zip',
            default => 'text/html',
        };
    }

    /**
     * @return array<string, string>
     */
    private function headers(string $accept): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => $accept,
        ];
    }
}
