<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MaosaPriceApiService;
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
        $dateString = $effectiveDate->toDateString();

        if ($user->id_socio) {
            $stations = $this->resolveStations($user);
            $stationIds = array_column($stations, 'id_estacion');
        } else {
            $stationIds = [$user->id_estacion];
        }

        if (empty($stationIds)) {
            abort(404, 'No tiene estaciones asignadas.');
        }

        $pdfContents = [];
        foreach ($stationIds as $stationId) {
            $apiResponse = $this->apiService->getPricePdf((int) $stationId, $dateString);

            if ($apiResponse->successful()) {
                $pdfContents[] = $apiResponse->body();
            }
        }

        if (empty($pdfContents)) {
            abort(502, 'No fue posible obtener los PDF de precios. Intente más tarde.');
        }

        $filename = 'maosa-prime-precios-combustible-' . $effectiveDate->format('Y-m-d') . '.pdf';

        if (count($pdfContents) === 1) {
            return response($pdfContents[0], 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $mergedPdf = $this->mergePdfs($pdfContents);

        return response($mergedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function mergePdfs(array $pdfContents): string
    {
        $tempDir = sys_get_temp_dir();
        $tempFiles = [];

        try {
            foreach ($pdfContents as $i => $content) {
                $path = $tempDir . '/maosa_pdf_' . uniqid() . "_{$i}.pdf";
                file_put_contents($path, $content);
                $tempFiles[] = $path;
            }

            $outputPath = $tempDir . '/maosa_pdf_merged_' . uniqid() . '.pdf';
            $inputFiles = implode(' ', array_map('escapeshellarg', $tempFiles));

            $command = sprintf(
                'gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=%s %s 2>&1',
                escapeshellarg($outputPath),
                $inputFiles
            );

            exec($command, $output, $exitCode);

            if ($exitCode !== 0 || !is_file($outputPath)) {
                abort(502, 'Error al fusionar los PDF.');
            }

            $merged = file_get_contents($outputPath);
            $tempFiles[] = $outputPath;

            return $merged;
        } finally {
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
        }
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
