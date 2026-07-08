<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ActiveUsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Exportación a Excel de las tablas del panel de estadísticas.
 *
 * Usa PhpSpreadsheet (ya presente en el proyecto), por lo que NO se requiere
 * instalar maatwebsite/excel. Cada endpoint soporta dos modalidades:
 *   - Exportar Todos:        sin parámetro `keys`  → todo el query filtrado.
 *   - Exportar Seleccionados: `keys[]` con las filas marcadas en el DataTable.
 */
class StatisticsExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user statistics index']);
    }

    public function export(Request $request, string $type)
    {
        return match ($type) {
            'browsers'     => $this->exportBreakdown($request, UserSession::class, 'browser', 'Navegadores', 'Navegador'),
            'countries'    => $this->exportBreakdown($request, UserSession::class, 'country', 'Paises', 'País'),
            'devices'      => $this->exportBreakdown($request, UserSession::class, 'device_type', 'Dispositivos', 'Dispositivo'),
            'pages'        => $this->exportBreakdown($request, PageVisit::class, 'url', 'Paginas', 'Página (URL)'),
            'active-users' => $this->exportActiveUsers($request),
            default        => abort(404),
        };
    }

    /**
     * Export a two-column breakdown (label + total) grouped by $column.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    private function exportBreakdown(Request $request, string $modelClass, string $column, string $sheetTitle, string $labelTitle)
    {
        [$from, $to] = $this->range($request);

        // $column es un literal fijo (browser/country/device_type/url), no entrada del usuario.
        $query = $modelClass::query()
            ->selectRaw("{$column} as label, COUNT(*) as total")
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total');

        $keys = $this->selectedKeys($request);
        if (!empty($keys)) {
            $query->whereIn($column, $keys);
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);

        $sheet->fromArray([$labelTitle, 'Total'], null, 'A1');
        $this->styleHeader($sheet, 'A1:B1');

        $row = 2;
        foreach ($rows as $item) {
            $sheet->setCellValue('A' . $row, $item->label);
            $sheet->setCellValue('B' . $row, (int) $item->total);
            $row++;
        }

        $this->borderRange($sheet, "A2:B" . max($row - 1, 1));
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(14);

        return $this->download($spreadsheet, strtolower($sheetTitle));
    }

    /**
     * Export the "Usuarios más activos" table.
     */
    private function exportActiveUsers(Request $request)
    {
        [$from, $to] = $this->range($request);

        $query = ActiveUsersDataTable::baseQuery($from, $to)->orderByDesc('page_visits_count');

        $keys = $this->selectedKeys($request);
        if (!empty($keys)) {
            $query->whereIn('users.id', $keys);
        }

        $users = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios mas activos');

        $headers = ['Usuario', 'Email', 'Visitas', 'Sesiones', 'Fecha última sesión', 'Ubicación última sesión'];
        $sheet->fromArray($headers, null, 'A1');
        $this->styleHeader($sheet, 'A1:F1');

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->name);
            $sheet->setCellValue('B' . $row, $user->email);
            $sheet->setCellValue('C' . $row, (int) $user->page_visits_count);
            $sheet->setCellValue('D' . $row, (int) $user->sessions_count);
            $sheet->setCellValue('E' . $row, $user->last_session_at
                ? Carbon::parse($user->last_session_at)->format('d/m/Y H:i')
                : '—');
            $sheet->setCellValue('F' . $row, $user->last_session_location ?: 'Desconocida');
            $row++;
        }

        $this->borderRange($sheet, "A2:F" . max($row - 1, 1));
        foreach (['A' => 25, 'B' => 30, 'C' => 12, 'D' => 12, 'E' => 20, 'F' => 35] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return $this->download($spreadsheet, 'usuarios_mas_activos');
    }

    /**
     * Selected row keys sent by the "Exportar Seleccionados" action.
     */
    private function selectedKeys(Request $request): array
    {
        return array_values(array_filter(
            (array) $request->input('keys', []),
            fn ($k) => $k !== '' && $k !== null
        ));
    }

    /**
     * Raw [from, to] dates from the request, defaulting to the last 7 days.
     */
    private function range(Request $request): array
    {
        return [
            $request->input('date_from', now()->subDays(7)->format('Y-m-d')),
            $request->input('date_to', now()->format('Y-m-d')),
        ];
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    private function borderRange($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    private function download(Spreadsheet $spreadsheet, string $slug)
    {
        $writer = new Xlsx($spreadsheet);
        $filename = 'estadisticas_' . $slug . '_' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
