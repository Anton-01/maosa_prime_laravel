<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Cliente de la API de Layout de Precios PEMEX (REQ-05 / REQ-07).
 *
 * Expone los tres formatos del layout nacional por estación:
 *   GET /api/precio_pemex/layout/estacion/{id_estacion}/HTML
 *   GET /api/precio_pemex/layout/estacion/{id_estacion}/Excel
 *   GET /api/precio_pemex/layout/estacion/{id_estacion}/pdf
 *
 * El token vive sólo del lado del servidor: el navegador nunca habla
 * directamente con la API, siempre pasa por el proxy de la aplicación.
 */
class PrecioPemexApiService
{
    private const PATH = '/api/precio_pemex/layout/estacion';

    /** Segundos de espera para las exportaciones (Excel/PDF son más lentas). */
    private const EXPORT_TIMEOUT = 60;

    private string $baseUrl;

    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.maosa_api.pemex_base_url') ?: config('services.maosa_api.base_url', ''),
            '/'
        );
        $this->token = (string) config('services.maosa_api.token', '');
    }

    /**
     * Layout en HTML listo para inyectarse en el contenedor del módulo.
     */
    public function layoutHtml(int $idEstacion): Response
    {
        return Http::withHeaders($this->headers('text/html'))
            ->timeout(30)
            ->get($this->url($idEstacion, 'HTML'));
    }

    /**
     * Layout en Excel (.xlsx).
     */
    public function layoutExcel(int $idEstacion): Response
    {
        return Http::withHeaders($this->headers('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
            ->timeout(self::EXPORT_TIMEOUT)
            ->get($this->url($idEstacion, 'Excel'));
    }

    /**
     * Layout en PDF.
     */
    public function layoutPdf(int $idEstacion): Response
    {
        return Http::withHeaders($this->headers('application/pdf'))
            ->timeout(self::EXPORT_TIMEOUT)
            ->get($this->url($idEstacion, 'pdf'));
    }

    private function url(int $idEstacion, string $formato): string
    {
        return "{$this->baseUrl}" . self::PATH . "/{$idEstacion}/{$formato}";
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
