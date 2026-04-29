<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MaosaPriceApiService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.maosa_api.base_url'), '/');
        $this->token = config('services.maosa_api.token', '');
    }

    /**
     * Obtiene las estaciones de un socio.
     * Retorna array de estaciones: [['id' => int, 'nombre' => string], ...]
     */
    public function getEstacionesPorSocio(int $idSocio): array
    {
        $response = Http::withHeaders($this->jsonHeaders())
            ->get("{$this->baseUrl}/api/precio_importado/catalogos/socios/{$idSocio}");

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();

        return $data['estaciones'] ?? [];
    }

    /**
     * Obtiene el detalle de una estación específica.
     * Retorna array con los datos de la estación.
     */
    public function getEstacion(int $idEstacion): array
    {
        $response = Http::withHeaders($this->jsonHeaders())
            ->get("{$this->baseUrl}/api/precio_importado/catalogos/estaciones/{$idEstacion}");

        if (!$response->successful()) {
            return ['id' => $idEstacion, 'nombre' => "Estación {$idEstacion}"];
        }

        return $response->json() ?? ['id' => $idEstacion, 'nombre' => "Estación {$idEstacion}"];
    }

    /**
     * Obtiene el layout HTML de precios para una estación.
     */
    public function getPrecioHtml(int $idEstacion, ?string $fechaVigencia = null): Response
    {
        $url = "{$this->baseUrl}/api/precio_importado/layout/estacion/{$idEstacion}/html";

        $query = [];
        if ($fechaVigencia) {
            $query['fecha_vigencia'] = $fechaVigencia;
        }

        return Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'text/html',
        ])->get($url, $query);
    }

    private function jsonHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json',
        ];
    }
}
