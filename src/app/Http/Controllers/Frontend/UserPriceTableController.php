<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserPriceLegend;
use App\Models\UserPriceList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserPriceTableController extends Controller
{
    /**
     * Display the price table for the authenticated user.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Check if user can view price table
        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        // Get active price list
        $priceList = UserPriceList::getActiveForUser($user->id);

        // Get legends
        $legends = UserPriceLegend::getForUser($user->id);

        return view('frontend.price-table.index', compact('user', 'priceList', 'legends'));
    }

    /**
     * Export the price table to PDF.
     */
    public function exportPdf(): Response
    {
        $user = auth()->user();

        // Check if user can view price table
        if (!$user->canViewPriceTable()) {
            abort(403, 'No tiene permiso para ver la tabla de precios');
        }

        // Get active price list
        $priceList = UserPriceList::getActiveForUser($user->id);

        // Get legends
        $legends = UserPriceLegend::getForUser($user->id);

        $pdf = Pdf::loadView('frontend.price-table.pdf', compact('user', 'priceList', 'legends'));

        $pdf->setPaper('letter', 'portrait');

        $filename = 'precios_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
