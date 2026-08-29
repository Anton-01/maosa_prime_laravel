<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleUserCreateRequest;
use App\Http\Requests\Admin\RoleUserUpdateRequest;
use App\Models\CatUsuarioImportado;
use App\Models\EstacionNacional;
use App\Models\User;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;

class RoleUserController extends Controller
{
    public function __construct(private readonly UserManagementService $userManagementService)
    {
        $this->middleware(['permission:access management users index'])->only(['index', 'data']);
        $this->middleware(['permission:access management users create'])->only(['create', 'store']);
        $this->middleware(['permission:access management users update'])->only(['edit', 'update', 'toggleApproval']);
        $this->middleware(['permission:access management users delete'])->only(['destroy']);
        $this->middleware(['permission:access management users index'])->only(['show', 'exportExcel']);
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/Users/Index', [
            'stations' => $this->stationOptions(),
            'urls' => [
                'base' => route('admin.role-user.index'),
                'data' => route('admin.role-user.data'),
                'create' => route('admin.role-user.create'),
                'export' => route('admin.role-user.export'),
                'import' => route('admin.user-import.index'),
                'permissionsBase' => url('admin/user-permissions'),
                'statisticsBase' => url('admin/statistics/user'),
            ],
        ]);
    }

    /**
     * Server-side JSON data source for the users table.
     */
    public function data(Request $request): JsonResponse
    {
        return response()->json($this->userManagementService->tableData($request));
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
            'roles' => $this->roleOptions(),
            'stations' => $this->stationOptions(),
            'nationalStations' => $this->nationalStationOptions(),
            'urls' => [
                'base' => route('admin.role-user.index'),
            ],
        ]);
    }

    public function store(RoleUserCreateRequest $request): RedirectResponse
    {
        $this->userManagementService->create($request, $request->validated());

        return to_route('admin.role-user.index')->with('success', '¡Usuario creado correctamente!');
    }

    public function show(string $id): InertiaResponse
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $this->userManagementService->getForShow((int) $id),
            'urls' => [
                'base' => route('admin.role-user.index'),
                'permissionsBase' => url('admin/user-permissions'),
            ],
        ]);
    }

    public function edit(string $id): InertiaResponse
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => $this->userManagementService->getForEdit((int) $id),
            'roles' => $this->roleOptions(),
            'stations' => $this->stationOptions(),
            'nationalStations' => $this->nationalStationOptions(),
            'urls' => [
                'base' => route('admin.role-user.index'),
            ],
        ]);
    }

    public function update(RoleUserUpdateRequest $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Block editing Super Admin users via this form
        if ($user->hasRole('Super Admin') && ! auth()->user()->hasRole('Super Admin')) {
            abort(403, 'No tienes permiso para editar un Super Admin.');
        }

        $this->userManagementService->update($request, (int) $id, $request->validated());

        return to_route('admin.role-user.index')->with('success', '¡Usuario actualizado correctamente!');
    }

    public function destroy(string $id): RedirectResponse
    {
        if (! $this->userManagementService->delete((int) $id)) {
            return back()->with('error', 'No se puede eliminar el Super Admin.');
        }

        return back()->with('success', '¡Usuario eliminado correctamente!');
    }

    /**
     * Toggle user's approval status.
     */
    public function toggleApproval(Request $request, string $id): RedirectResponse
    {
        $isApproved = $this->userManagementService->toggleApproval((int) $id);

        $status = $isApproved ? 'aprobado' : 'desaprobado';

        return back()->with('success', "Usuario {$status} correctamente");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function roleOptions(): array
    {
        return Role::orderBy('name')
            ->get()
            ->map(fn (Role $role) => ['value' => $role->name, 'label' => $role->name])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function stationOptions(): array
    {
        return CatUsuarioImportado::estacionesActivas()
            ->map(fn ($station) => ['value' => $station->id_estacion, 'label' => $station->estacion])
            ->values()
            ->all();
    }

    /**
     * Estaciones activas del catálogo nacional (origen.cat_estacion_nacional)
     * para el multiselect de "PRECIOS PEMEX" (REQ-02 / REQ-03).
     *
     * @return array<int, array{value: int, label: string}>
     */
    private function nationalStationOptions(): array
    {
        return EstacionNacional::comoOpciones();
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
            $users->each(fn ($u) => $u->setRelation('branches', collect()));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios');

        // Headers
        $headers = [
            'ID', 'Nombre', 'Email', 'Telefono', 'Empresa',
            'Tipo Usuario', 'Rol', 'Aprobado', 'Precios Internacionales',
            'Sucursales', 'Fecha Registro',
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
            'F' => 15, 'G' => 15, 'H' => 10, 'I' => 22, 'J' => 30, 'K' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'users_maosa' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
