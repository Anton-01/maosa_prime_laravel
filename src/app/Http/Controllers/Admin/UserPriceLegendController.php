<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPriceLegend;
use App\Models\DefaultPriceLegend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPriceLegendController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:price legends index'])->only(['index']);
        $this->middleware(['permission:price legends create'])->only(['store']);
        $this->middleware(['permission:price legends update'])->only(['update']);
        $this->middleware(['permission:price legends delete'])->only(['destroy']);
    }

    /**
     * Display legends for a specific user.
     */
    public function index(Request $request): View
    {
        $userId = $request->query('user_id');
        $user = null;
        $legends = collect();

        if ($userId) {
            $user = User::findOrFail($userId);
            $legends = UserPriceLegend::where('user_id', $userId)
                ->orderBy('sort_order')
                ->get();
        }

        $users = User::where('user_type', 'user')
            ->where('can_view_price_table', true)
            ->orderBy('name')
            ->get();

        return view('admin.user-legend.index', compact('users', 'user', 'legends'));
    }

    /**
     * Store a new legend for a user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'legend_text' => 'required|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        UserPriceLegend::create([
            'user_id' => $request->user_id,
            'legend_text' => $request->legend_text,
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Leyenda creada correctamente');
    }

    /**
     * Update the specified legend.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'legend_text' => 'required|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $legend = UserPriceLegend::findOrFail($id);
        $legend->update([
            'legend_text' => $request->legend_text,
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Leyenda actualizada correctamente');
    }

    /**
     * Remove the specified legend.
     */
    public function destroy(string $id)
    {
        $legend = UserPriceLegend::findOrFail($id);
        $legend->delete();

        return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
    }

    /**
     * Copy default legends to a user.
     */
    public function copyDefaults(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // Delete existing legends for this user
        UserPriceLegend::where('user_id', $userId)->delete();

        // Copy defaults
        $defaults = DefaultPriceLegend::active()->orderBy('sort_order')->get();

        foreach ($defaults as $default) {
            UserPriceLegend::create([
                'user_id' => $userId,
                'legend_text' => $default->legend_text,
                'sort_order' => $default->sort_order,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Leyendas por defecto copiadas correctamente');
    }
}
