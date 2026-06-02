<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MaosaPriceApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserPriceTableController extends Controller
{
    public function __construct(private MaosaPriceApiService $apiService) {}

    public function index(): View
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        return view('frontend.price-table.index', compact('user'));
    }

    public function loadStations(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403);
        }

        $stations = $this->resolveStations($user);

        return response()->json($stations);
    }

    public function loadPriceHtml(Request $request): Response
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403);
        }

        $idStations = (int) $request->get('estacion_id');
        $effectiveDate = $request->get('fecha_vigencia');

        if (!$idStations) {
            return response('<p class="text-center text-muted py-4">Seleccione una estación.</p>', 200)
                ->header('Content-Type', 'text/html');
        }


        if ($effectiveDate) {
            $date = Carbon::parse($effectiveDate)->startOfDay();
            $today = Carbon::today();
            $diff = $today->diffInDays($date, false);
            if ($diff < -1 || $diff > 1) {
                return response('<p class="text-center text-danger py-4">Fecha no permitida.</p>', 200)
                    ->header('Content-Type', 'text/html');
            }
        }

        if (!$this->userOwnsStation($user, $idStations)) {
            abort(403, 'No tiene acceso a esta estación');
        }

        $apiResponse = $this->apiService->getPriceHtml($idStations, $effectiveDate ?: null);

        if ($apiResponse->status() === 404) {
            return response('<p class="text-center text-muted py-4">Sin precios disponibles para la fecha seleccionada.</p>', 200)->header('Content-Type', 'text/html');
        }

        if ($apiResponse->status() === 401) {
            return response('<p class="text-center text-danger py-4">Error de autenticación con la API de precios.</p>', 200)->header('Content-Type', 'text/html');
        }

        if (!$apiResponse->successful()) {
            return response('<p class="text-center text-danger py-4">Error al obtener precios. Intente más tarde.</p>', 200)->header('Content-Type', 'text/html');
        }

        return response($apiResponse->body(), 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function exportPdf(Request $request): Response
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        $effectiveDate = $this->resolveEffectiveDate($request->get('fecha_vigencia'));
        $stations = $this->resolveStations($user);
        $allowedStationIds = array_column($stations, 'id_estacion');
        $priceTables = [];

        // 1. Inicializamos la variable para la marca de agua fuera del bucle
        $watermarkUrl = null;

        foreach ($stations as $station) {
            $stationId = (int) ($station['id_estacion'] ?? $station['id'] ?? 0);

            if (!$stationId || !in_array($stationId, $allowedStationIds, true)) {
                continue;
            }

            $apiResponse = $this->apiService->getPriceHtml($stationId, $effectiveDate->toDateString());

            $cleanHtml = null;
            if ($apiResponse->successful()) {
                // 2. Recibimos el arreglo de nuestra nueva función
                $processedData = $this->cleanHtmlAndExtractWatermark($apiResponse->body());

                // 3. Asignamos SOLO EL STRING del html limpio a nuestra variable
                $cleanHtml = $processedData['html'];

                // 4. Capturamos la URL de la marca de agua (basta con hacerlo una vez)
                if (empty($watermarkUrl) && !empty($processedData['watermark'])) {
                    $watermarkUrl = $processedData['watermark'];
                }
            }

            $priceTables[] = [
                'station_name' => $this->stationName($station, $stationId),
                'html' => $cleanHtml, // Ahora sí es un string, Blade no fallará
                'status' => $apiResponse->status(),
            ];
        }

        $downloadedAt = now();
        $filename = 'maosa-prime-precios-combustible-' . $effectiveDate->format('Y-m-d') . '.pdf';
        $tablePricesCss = $this->tablePricesCss();

        // 5. Agregamos 'watermarkUrl' al compact() para que la vista pueda usarla
        $pdf = Pdf::loadView('frontend.price-table.pdf', compact(
            'user',
            'effectiveDate',
            'downloadedAt',
            'priceTables',
            'tablePricesCss',
            'watermarkUrl'
        ))->setPaper('letter', 'portrait')
            ->setOption('enable_remote', true);

        $pdf->setEncryption('', $this->pdfOwnerPassword($user, $downloadedAt), ['print']);

        return $pdf->download($filename);
    }

    private function cleanHtmlAndExtractWatermark(string $html): array
    {
        if (empty($html)) {
            return ['html' => '', 'watermark' => null];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);

        $watermarkUrl = null;

        $wmNodes = $xpath->query("//div[contains(@class, 'wm')]//img");
        if ($wmNodes->length > 0) {
            $watermarkUrl = $wmNodes->item(0)->getAttribute('src');

            $wmContainer = $xpath->query("//div[contains(@class, 'wm')]")->item(0);
            if ($wmContainer) {
                $wmContainer->parentNode->removeChild($wmContainer);
            }
        }

        $nodesWithStyle = $xpath->query("//*[@style]");
        foreach ($nodesWithStyle as $node) {
            // EXCLUSIÓN: Si el elemento es un encabezado de tabla, conservamos sus colores
            if (strtolower($node->nodeName) === 'th') {
                continue;
            }

            $style = $node->getAttribute('style');
            $style = preg_replace('/background(-color)?\s*:\s*#[a-fA-F0-9]{3,6}\s*;?/i', '', $style);
            $node->setAttribute('style', $style);
        }

        $cleanHtml = $dom->saveHTML();
        libxml_clear_errors();

        return [
            'html' => $cleanHtml,
            'watermark' => $watermarkUrl
        ];
    }
    private function resolveStations($user): array
    {
        if ($user->id_socio) {
            return array_map(
                fn (array $station) => $this->normalizeStation($station),
                $this->apiService->getEstacionesPorSocio($user->id_socio)
            );
        }

        if ($user->id_estacion) {
            $station = $this->apiService->getEstacion($user->id_estacion);
            return [$this->normalizeStation($station, $user->id_estacion)];
        }

        return [];
    }

    private function normalizeStation(array $station, ?int $fallbackId = null): array
    {
        $station['id_estacion'] = (int) ($station['id_estacion'] ?? $station['id'] ?? $fallbackId);

        return $station;
    }

    private function resolveEffectiveDate(?string $effectiveDate): Carbon
    {
        $date = $effectiveDate
            ? Carbon::parse($effectiveDate)->startOfDay()
            : Carbon::today();

        $today = Carbon::today();
        $diff = $today->diffInDays($date, false);

        if ($diff < -1 || $diff > 1) {
            abort(422, 'Fecha no permitida.');
        }

        return $date;
    }

    private function stationName(array $station, int $stationId): string
    {
        return $station['estacion']
            ?? $station['nombre']
            ?? $station['name']
            ?? "Estación {$stationId}";
    }

    private function pdfOwnerPassword($user, Carbon $downloadedAt): string
    {
        return hash('sha256', implode('|', [
            config('app.key'),
            $user->id,
            $downloadedAt->timestamp,
        ]));
    }

    private function tablePricesCss(): string
    {
        $path = public_path('frontend/css/maosa/table-prices-pdf.css');

        if (!is_file($path)) {
            return '';
        }

        return file_get_contents($path) ?: '';
    }

    private function userOwnsStation($user, int $idStations): bool
    {
        if ($user->id_estacion) {
            return $user->id_estacion === $idStations;
        }

        if ($user->id_socio) {
            $stations = $this->apiService->getEstacionesPorSocio($user->id_socio);
            $ids = array_column($stations, 'id_estacion');
            return in_array($idStations, $ids);
        }

        return false;
    }
}
