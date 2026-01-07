<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\FuelTerminalDataTable;
use App\Http\Controllers\Controller;
use App\Models\FuelTerminal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FuelTerminalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:fuel terminals index'])->only(['index']);
        $this->middleware(['permission:fuel terminals create'])->only(['create', 'store']);
        $this->middleware(['permission:fuel terminals update'])->only(['edit', 'update']);
        $this->middleware(['permission:fuel terminals delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FuelTerminalDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.fuel-terminal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.fuel-terminal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'required|boolean',
        ]);

        FuelTerminal::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return to_route('admin.fuel-terminal.index')
            ->with('success', 'Terminal creada correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $terminal = FuelTerminal::findOrFail($id);
        return view('admin.fuel-terminal.edit', compact('terminal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'required|boolean',
        ]);

        $terminal = FuelTerminal::findOrFail($id);
        $terminal->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return to_route('admin.fuel-terminal.index')
            ->with('success', 'Terminal actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $terminal = FuelTerminal::findOrFail($id);
        $terminal->delete();

        return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
    }
}
