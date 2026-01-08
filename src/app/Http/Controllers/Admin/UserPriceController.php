<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserPriceListDataTable;
use App\Http\Controllers\Controller;
use App\Models\DefaultPriceLegend;
use App\Models\FuelTerminal;
use App\Models\User;
use App\Models\UserPriceItem;
use App\Models\UserPriceLegend;
use App\Models\UserPriceList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user prices index'])->only(['index', 'show']);
        $this->middleware(['permission:user prices create'])->only(['create', 'store']);
        $this->middleware(['permission:user prices update'])->only(['edit', 'update']);
        $this->middleware(['permission:user prices delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserPriceListDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.user-price.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::where('user_type', 'user')
            ->where('can_view_price_table', true)
            ->orderBy('name')
            ->get();
        
        $terminals = FuelTerminal::active()->orderBy('name')->get();

        return view('admin.user-price.create', compact('users', 'terminals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'price_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.terminal_name' => 'required|string|max:255',
            'items.*.magna_price' => 'nullable|numeric|min:0',
            'items.*.premium_price' => 'nullable|numeric|min:0',
            'items.*.diesel_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Deactivate previous price lists for this user
            UserPriceList::where('user_id', $request->user_id)
                ->update(['is_active' => false]);

            // Create new price list
            $priceList = UserPriceList::create([
                'user_id' => $request->user_id,
                'price_date' => $request->price_date,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // Create price items
            foreach ($request->items as $index => $item) {
                if (!empty($item['terminal_name'])) {
                    UserPriceItem::create([
                        'user_price_list_id' => $priceList->id,
                        'fuel_terminal_id' => $item['fuel_terminal_id'] ?? null,
                        'terminal_name' => $item['terminal_name'],
                        'magna_price' => $item['magna_price'] ?? null,
                        'premium_price' => $item['premium_price'] ?? null,
                        'diesel_price' => $item['diesel_price'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Ensure user has legends (copy from defaults if not)
            $this->ensureUserHasLegends($request->user_id);
        });

        return to_route('admin.user-price.index')
            ->with('success', 'Lista de precios creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $priceList = UserPriceList::with(['user', 'items', 'createdBy'])->findOrFail($id);
        $legends = UserPriceLegend::forUser($priceList->user_id)->active()->get();

        return view('admin.user-price.show', compact('priceList', 'legends'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $priceList = UserPriceList::with(['user', 'items'])->findOrFail($id);
        $users = User::where('user_type', 'user')
            ->where('can_view_price_table', true)
            ->orderBy('name')
            ->get();
        $terminals = FuelTerminal::active()->orderBy('name')->get();

        return view('admin.user-price.edit', compact('priceList', 'users', 'terminals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'price_date' => 'required|date',
            'is_active' => 'required|boolean',
            'items' => 'required|array|min:1',
            'items.*.terminal_name' => 'required|string|max:255',
            'items.*.magna_price' => 'nullable|numeric|min:0',
            'items.*.premium_price' => 'nullable|numeric|min:0',
            'items.*.diesel_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $priceList = UserPriceList::findOrFail($id);

            // If activating this list, deactivate others for the same user
            if ($request->is_active) {
                UserPriceList::where('user_id', $request->user_id)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);
            }

            $priceList->update([
                'user_id' => $request->user_id,
                'price_date' => $request->price_date,
                'is_active' => $request->is_active,
            ]);

            // Delete old items and create new ones
            $priceList->items()->delete();

            foreach ($request->items as $index => $item) {
                if (!empty($item['terminal_name'])) {
                    UserPriceItem::create([
                        'user_price_list_id' => $priceList->id,
                        'fuel_terminal_id' => $item['fuel_terminal_id'] ?? null,
                        'terminal_name' => $item['terminal_name'],
                        'magna_price' => $item['magna_price'] ?? null,
                        'premium_price' => $item['premium_price'] ?? null,
                        'diesel_price' => $item['diesel_price'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        return to_route('admin.user-price.index')
            ->with('success', 'Lista de precios actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $priceList = UserPriceList::findOrFail($id);
        $priceList->delete();

        return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
    }

    /**
     * Ensure user has legends, copy from defaults if not.
     */
    private function ensureUserHasLegends(int $userId): void
    {
        $existingLegends = UserPriceLegend::where('user_id', $userId)->count();

        if ($existingLegends === 0) {
            $defaults = DefaultPriceLegend::active()->orderBy('sort_order')->get();

            foreach ($defaults as $default) {
                UserPriceLegend::create([
                    'user_id' => $userId,
                    'legend_text' => $default->legend_text,
                    'sort_order' => $default->sort_order,
                    'is_active' => true,
                ]);
            }
        }
    }
}
