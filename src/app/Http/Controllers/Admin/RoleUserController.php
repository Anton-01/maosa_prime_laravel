<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RoleUserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleUserCreateRequest;
use App\Http\Requests\Admin\RoleUserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\PermissionRegistrar;

class RoleUserController extends Controller
{
    function __construct(){
        $this->middleware(['permission:access management users index'])->only(['index']);
        $this->middleware(['permission:access management users create'])->only(['create', 'store']);
        $this->middleware(['permission:access management users update'])->only(['edit', 'update', 'toggleApproval', 'togglePriceTable']);
        $this->middleware(['permission:access management users delete'])->only(['destroy']);
        $this->middleware(['permission:access management users index'])->only(['show', 'exportExcel']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(RoleUserDataTable $dataTable) : View | JsonResponse
    {
        return $dataTable->render('admin.role-permission.role-user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        $roles = Role::all();
        return view('admin.role-permission.role-user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleUserCreateRequest $request) : RedirectResponse
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->user_type = 'admin';
        $user->is_approved = $request->is_approved ?? 0;
        $user->save();

        $user->syncRoles($request->role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return to_route('admin.role-user.index')->with('statusUsrC', true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $user = User::with('roles.permissions')->findOrFail($id);

        // Safely load branches — table may not exist in all environments
        try {
            $user->load('branches');
        } catch (\Throwable $e) {
            $user->setRelation('branches', collect());
        }

        $directPermissions = $user->getDirectPermissions();
        $rolePermissions   = $user->getPermissionsViaRoles();
        $allPermissions    = $user->getAllPermissions()->groupBy('group_name');

        return view('admin.role-permission.role-user.show', compact(
            'user', 'directPermissions', 'rolePermissions', 'allPermissions'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $roles = Role::all();
        $user = User::findOrFail($id);
        return view('admin.role-permission.role-user.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUserUpdateRequest $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        // Block editing Super Admin users via this form
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'No tienes permiso para editar un Super Admin.');
        }

        $user->name  = $request->name;

        $user->email = $request->email;
        $user->is_approved = $request->is_approved ?? 0;
        $user->user_type = 'admin';

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->syncRoles($request->role);
        // Clear Spatie permission cache so the role change is immediately visible
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return to_route('admin.role-user.index')->with('statusUsrU', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->hasRole('Super Admin')) {
                return response(['status' => 'error', 'message' => 'No se puede eliminar el Super Admin.']);
            }
            $user->delete();
            return response(['status' => 'success', 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Toggle user's approval status.
     */
    public function toggleApproval(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_approved = !$user->is_approved;
            $user->save();

            $status = $user->is_approved ? 'aprobado' : 'desaprobado';

            return response([
                'status'      => 'success',
                'message'     => "Usuario {$status} correctamente",
                'is_approved' => $user->is_approved,
            ]);
        } catch (\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Toggle user's access to price table.
     */
    public function togglePriceTable(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->can_view_price_table = !$user->can_view_price_table;
            $user->save();

            $status = $user->can_view_price_table ? 'activado' : 'desactivado';

            return response([
                'status' => 'success',
                'message' => "Acceso a tabla de precios {$status} correctamente",
                'can_view_price_table' => $user->can_view_price_table
            ]);
        } catch(\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Export all users to Excel.
     */
    public function exportExcel()
    {
        $users = User::with('roles')->orderBy('name')->get();

        // Safely load branches
        try {
            $users->load('branches');
        } catch (\Throwable $e) {
            $users->each(fn($u) => $u->setRelation('branches', collect()));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios');

        // Headers
        $headers = [
            'ID', 'Nombre', 'Email', 'Telefono', 'Empresa',
            'Tipo Usuario', 'Rol', 'Aprobado', 'Tabla Precios',
            'Sucursales', 'Fecha Registro'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($users as $user) {
            $branches = $user->branches->pluck('name')->implode(', ');
            $roles = $user->roles->pluck('name')->implode(', ');

            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->phone ?? '-');
            $sheet->setCellValue('E' . $row, $user->company ?? '-');
            $sheet->setCellValue('F' . $row, $user->user_type === 'admin' ? 'Administrador' : 'Usuario');
            $sheet->setCellValue('G' . $row, $roles ?: '-');
            $sheet->setCellValue('H' . $row, $user->is_approved ? 'Sí' : 'No');
            $sheet->setCellValue('I' . $row, $user->can_view_price_table ? 'Sí' : 'No');
            $sheet->setCellValue('J' . $row, $branches ?: 'Sin sucursales');
            $sheet->setCellValue('K' . $row, $user->created_at->format('d/m/Y H:i'));
            $row++;
        }

        // Style data cells
        $lastRow = $row - 1;
        $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        foreach (['A' => 8, 'B' => 25, 'C' => 30, 'D' => 15, 'E' => 20,
                     'F' => 15, 'G' => 15, 'H' => 10, 'I' => 12, 'J' => 30, 'K' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'users_maosa' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
