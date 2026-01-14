<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RoleUserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleUserCreateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;

class RoleUserController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:access management index']);
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
        $user->is_approved = $request->is_approved;
        $user->save();

        $user->assignRole($request->role);

        return to_route('admin.role-user.index')->with('statusUsrC', true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = Role::all();
        $user = User::findOrFail($id);
        return view('admin.role-permission.role-user.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_approved = $request->is_approved;
        $request->filled('password') ?? $user->password = bcrypt($request->password);
        $user->user_type = 'admin';
        $user->save();

        $user->syncRoles($request->role);

        return to_route('admin.role-user.index')->with('statusUsrU', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            User::findOrFail($id)->delete();

            return response(['status' => 'success', 'message' => 'Eliminado correctamente']);
        }catch(\Exception $e){
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
        $users = User::with(['roles', 'branches'])
            ->orderBy('name')
            ->get();

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
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B5E20'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

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
            $sheet->setCellValue('F' . $row, $user->user_type == 'admin' ? 'Administrador' : 'Usuario');
            $sheet->setCellValue('G' . $row, $roles ?: '-');
            $sheet->setCellValue('H' . $row, $user->is_approved ? 'Si' : 'No');
            $sheet->setCellValue('I' . $row, $user->can_view_price_table ? 'Si' : 'No');
            $sheet->setCellValue('J' . $row, $branches ?: 'Sin sucursales');
            $sheet->setCellValue('K' . $row, $user->created_at->format('d/m/Y H:i'));
            $row++;
        }

        // Style data cells
        $lastRow = $row - 1;
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle("A2:K{$lastRow}")->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(18);

        // Create response
        $writer = new Xlsx($spreadsheet);
        $filename = 'usuarios_' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
