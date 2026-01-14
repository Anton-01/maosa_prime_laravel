<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserBranchDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserBranchController extends Controller
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
    public function index(UserBranchDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.user-branch.index');
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

        return view('admin.user-branch.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        UserBranch::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return to_route('admin.user-branch.index')
            ->with('success', 'Sucursal creada correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $branch = UserBranch::with('user')->findOrFail($id);
        $users = User::where('user_type', 'user')
            ->where('can_view_price_table', true)
            ->orderBy('name')
            ->get();

        return view('admin.user-branch.edit', compact('branch', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $branch = UserBranch::findOrFail($id);
        $branch->update([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return to_route('admin.user-branch.index')
            ->with('success', 'Sucursal actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = UserBranch::findOrFail($id);
        $branch->delete();

        return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
    }

    /**
     * Get branches for a specific user (AJAX).
     */
    public function getBranchesForUser(Request $request)
    {
        $userId = $request->get('user_id');

        if (!$userId) {
            return response()->json([]);
        }

        $branches = UserBranch::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($branches);
    }
}
