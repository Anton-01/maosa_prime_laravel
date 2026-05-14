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

    public function exportPdf(): void
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }
    }

    private function resolveStations($user): array
    {
        if ($user->id_socio) {
            return $this->apiService->getEstacionesPorSocio($user->id_socio);
        }

        if ($user->id_estacion) {
            $station = $this->apiService->getEstacion($user->id_estacion);
            return [$station];
        }

        return [];
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
