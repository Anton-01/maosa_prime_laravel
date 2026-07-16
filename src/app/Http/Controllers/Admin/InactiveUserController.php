<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InactiveUserDestroyRequest;
use App\Services\Admin\InactiveUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InactiveUserController extends Controller
{
    public function __construct(private readonly InactiveUserService $inactiveUserService)
    {
        $this->middleware(['permission:access management users index'])->only(['index', 'export']);
        $this->middleware(['permission:access management users delete'])->only(['destroy']);
    }

    /**
     * Listing of inactive users (no session activity since N days).
     */
    public function index(Request $request): InertiaResponse
    {
        $days = max(1, (int) $request->get('days', 30));

        return Inertia::render('Admin/InactiveUsers/Index', [
            'days' => $days,
            'users' => $this->inactiveUserService->list($days),
            'urls' => [
                'base' => route('admin.inactive-users.index'),
                'export' => route('admin.inactive-users.export'),
                'destroy' => route('admin.inactive-users.destroy'),
            ],
        ]);
    }

    /**
     * Permanently delete the selected inactive users.
     * Requires the admin's current password (validated in the FormRequest).
     */
    public function destroy(InactiveUserDestroyRequest $request): RedirectResponse
    {
        $result = $this->inactiveUserService->destroy($request->validated()['user_ids']);

        $message = "Se eliminaron {$result['deleted']} usuario(s) correctamente.";

        if (! empty($result['skipped'])) {
            $message .= ' Omitidos (protegidos): ' . implode(', ', $result['skipped']) . '.';
        }

        return back()->with('success', $message);
    }

    /**
     * Export the inactive users listing to Excel.
     */
    public function export(Request $request)
    {
        $days = max(1, (int) $request->get('days', 30));
        $users = $this->inactiveUserService->query($days)->orderBy('users.name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios Inactivos');

        $headers = [
            'ID', 'Nombre', 'Email', 'Rol', 'Fecha Última Sesión',
            'Ubicación Última Sesión', 'Días Inactivo', 'Fecha Registro',
        ];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($users as $user) {
            $lastSession = $user->last_session_at ? Carbon::parse($user->last_session_at) : null;
            $reference = $lastSession ?? $user->created_at;

            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->getRoleNames()->implode(', ') ?: '-');
            $sheet->setCellValue('E' . $row, $lastSession ? $lastSession->format('d/m/Y H:i') : 'Nunca ha iniciado sesión');
            $sheet->setCellValue('F' . $row, $user->last_session_location ?: 'Desconocida');
            $sheet->setCellValue('G' . $row, $reference->diffInDays(now()));
            $sheet->setCellValue('H' . $row, $user->created_at->format('d/m/Y H:i'));
            $row++;
        }

        $lastRow = max($row - 1, 1);
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        foreach (['A' => 8, 'B' => 25, 'C' => 30, 'D' => 15, 'E' => 20, 'F' => 35, 'G' => 14, 'H' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'usuarios_inactivos_' . $days . 'dias_' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
