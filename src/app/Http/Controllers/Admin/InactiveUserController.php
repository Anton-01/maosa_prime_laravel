<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\InactiveUserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InactiveUserDestroyRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InactiveUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:access management users index'])->only(['index', 'export']);
        $this->middleware(['permission:access management users delete'])->only(['destroy']);
    }

    /**
     * Listing of inactive users (no session activity since N days).
     */
    public function index(InactiveUserDataTable $dataTable, Request $request): View | JsonResponse
    {
        $days = max(1, (int) $request->get('days', 30));

        return $dataTable->render('admin.role-permission.role-user.inactive', compact('days'));
    }

    /**
     * Export the inactive users listing to Excel.
     */
    public function export(Request $request)
    {
        $days = max(1, (int) $request->get('days', 30));
        $users = $this->inactiveUsersQuery($days)->orderBy('users.name')->get();

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

    /**
     * Permanently delete the selected inactive users.
     * Requires the admin's current password (validated in the FormRequest).
     */
    public function destroy(InactiveUserDestroyRequest $request): JsonResponse
    {
        $users = User::whereIn('id', $request->input('user_ids'))->get();

        $deleted = 0;
        $skipped = [];

        foreach ($users as $user) {
            if ($user->id === auth()->id() || $user->hasRole('Super Admin')) {
                $skipped[] = $user->name;
                continue;
            }

            $user->delete();
            $deleted++;
        }

        $message = "Se eliminaron {$deleted} usuario(s) correctamente.";
        if (!empty($skipped)) {
            $message .= ' Omitidos (protegidos): ' . implode(', ', $skipped) . '.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'deleted' => $deleted,
            'skipped' => $skipped,
        ]);
    }

    /**
     * Base query for inactive users, shared with the export.
     */
    private function inactiveUsersQuery(int $days): Builder
    {
        $cutoff = now()->subDays($days);

        return User::query()
            ->select('users.*')
            ->selectRaw('last_session.last_session_at')
            ->selectRaw("NULLIF(CONCAT_WS(', ', last_session.city, last_session.region, last_session.country), '') as last_session_location")
            ->withLastSession()
            ->inactiveSince($cutoff)
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'Super Admin'))
            ->where('users.id', '!=', auth()->id());
    }
}
