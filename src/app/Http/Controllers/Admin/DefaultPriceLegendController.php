<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DefaultPriceLegendDataTable;
use App\Http\Controllers\Controller;
use App\Models\DefaultPriceLegend;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DefaultPriceLegendController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:price legends index'])->only(['index']);
        $this->middleware(['permission:price legends create'])->only(['create', 'store']);
        $this->middleware(['permission:price legends update'])->only(['edit', 'update']);
        $this->middleware(['permission:price legends delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DefaultPriceLegendDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.default-legend.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.default-legend.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'legend_text' => 'required|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        DefaultPriceLegend::create([
            'legend_text' => $request->legend_text,
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ]);

        return to_route('admin.default-legend.index')
            ->with('success', 'Leyenda creada correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $legend = DefaultPriceLegend::findOrFail($id);
        return view('admin.default-legend.edit', compact('legend'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'legend_text' => 'required|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $legend = DefaultPriceLegend::findOrFail($id);
        $legend->update([
            'legend_text' => $request->legend_text,
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ]);

        return to_route('admin.default-legend.index')
            ->with('success', 'Leyenda actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $legend = DefaultPriceLegend::findOrFail($id);
        $legend->delete();

        return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
    }
}
