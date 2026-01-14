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

        // Get all active price lists for the user (grouped by branch)
        $priceLists = UserPriceList::with(['items', 'branch'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('user_branch_id')
            ->orderByDesc('price_date')
            ->get();

        // Get legends
        $legends = UserPriceLegend::getForUser($user->id);

        // Check if user has branches
        $hasBranches = $priceLists->whereNotNull('user_branch_id')->isNotEmpty();

        return view('frontend.price-table.index', compact('user', 'priceLists', 'legends', 'hasBranches'));
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

        // Get all active price lists for the user (grouped by branch)
        $priceLists = UserPriceList::with(['items', 'branch'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('user_branch_id')
            ->orderByDesc('price_date')
            ->get();

        // Get legends
        $legends = UserPriceLegend::getForUser($user->id);

        // Check if user has branches
        $hasBranches = $priceLists->whereNotNull('user_branch_id')->isNotEmpty();

        $pdf = Pdf::loadView('frontend.price-table.pdf', compact('user', 'priceLists', 'legends', 'hasBranches'));

        $pdf->setPaper('letter', 'portrait');

        $filename = 'precios_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
