<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserPriceLegend;
use App\Models\UserPriceList;
use App\Services\MaosaPriceApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserPriceTableController extends Controller
{
    public function __construct(private MaosaPriceApiService $apiService) {}

    /**
     * Display the price table for the authenticated user.
     */
    public function index(): View
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        $estaciones = $this->resolveEstaciones($user);

        return view('frontend.price-table.index', compact('user', 'estaciones'));
    }

    /**
     * Proxy endpoint: obtiene el HTML de precios desde la API externa.
     */
    public function loadPriceHtml(Request $request): Response
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403);
        }

        $idEstacion = (int) $request->get('estacion_id');
        $fechaVigencia = $request->get('fecha_vigencia');

        if (!$idEstacion) {
            return response('<p class="text-center text-muted py-4">Seleccione una estación.</p>', 200)
                ->header('Content-Type', 'text/html');
        }

        // Validar que la fecha solo sea ayer, hoy o mañana
        if ($fechaVigencia) {
            $fecha = \Carbon\Carbon::parse($fechaVigencia)->startOfDay();
            $hoy = \Carbon\Carbon::today();
            $diff = $hoy->diffInDays($fecha, false);
            if ($diff < -1 || $diff > 1) {
                return response('<p class="text-center text-danger py-4">Fecha no permitida.</p>', 200)
                    ->header('Content-Type', 'text/html');
            }
        }

        // Verificar que la estación pertenece al usuario
        if (!$this->userOwnsEstacion($user, $idEstacion)) {
            abort(403, 'No tiene acceso a esta estación');
        }

        $apiResponse = $this->apiService->getPrecioHtml($idEstacion, $fechaVigencia ?: null);

        if ($apiResponse->status() === 404) {
            return response(
                '<p class="text-center text-muted py-4">Sin precios disponibles para la fecha seleccionada.</p>',
                200
            )->header('Content-Type', 'text/html');
        }

        if ($apiResponse->status() === 401) {
            return response(
                '<p class="text-center text-danger py-4">Error de autenticación con la API de precios.</p>',
                200
            )->header('Content-Type', 'text/html');
        }

        if (!$apiResponse->successful()) {
            return response(
                '<p class="text-center text-danger py-4">Error al obtener precios. Intente más tarde.</p>',
                200
            )->header('Content-Type', 'text/html');
        }

        return response($apiResponse->body(), 200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Export the price table to PDF.
     */
    public function exportPdf(): Response
    {
        $user = auth()->user();

        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        $priceLists = UserPriceList::with(['items', 'branch'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('user_branch_id')
            ->orderByDesc('price_date')
            ->get();

        $legends = UserPriceLegend::getForUser($user->id);

        $hasBranches = $priceLists->whereNotNull('user_branch_id')->isNotEmpty();

        $pdf = Pdf::loadView('frontend.price-table.pdf', compact('user', 'priceLists', 'legends', 'hasBranches'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('precios_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Resuelve la lista de estaciones del usuario según su tipo (socio o estación directa).
     */
    private function resolveEstaciones($user): array
    {
        if ($user->id_socio) {
            return $this->apiService->getEstacionesPorSocio($user->id_socio);
        }

        if ($user->id_estacion) {
            $estacion = $this->apiService->getEstacion($user->id_estacion);
            return [$estacion];
        }

        return [];
    }

    /**
     * Verifica que la estación solicitada pertenezca al usuario.
     */
    private function userOwnsEstacion($user, int $idEstacion): bool
    {
        if ($user->id_estacion) {
            return $user->id_estacion === $idEstacion;
        }

        if ($user->id_socio) {
            $estaciones = $this->apiService->getEstacionesPorSocio($user->id_socio);
            $ids = array_column($estaciones, 'id');
            return in_array($idEstacion, $ids);
        }

        return false;
    }
}
