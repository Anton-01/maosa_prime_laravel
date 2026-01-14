<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultPriceLegend;
use App\Models\FuelTerminal;
use App\Models\User;
use App\Models\UserBranch;
use App\Models\UserPriceItem;
use App\Models\UserPriceLegend;
use App\Models\UserPriceList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PriceImportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user prices import']);
    }

    /**
     * Show the import form.
     */
    public function index(): View
    {
        $users = User::where('user_type', 'user')
            ->where('can_view_price_table', true)
            ->orderBy('name')
            ->get();

        return view('admin.price-import.index', compact('users'));
    }

    /**
     * Process the imported Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_branch_id' => 'nullable|exists:user_branches,id',
            'price_date' => 'required|date',
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('excel_file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            if (empty($dataRows)) {
                return back()->with('error', 'El archivo esta vacio o no contiene datos validos');
            }

            DB::transaction(function () use ($request, $dataRows) {
                // Deactivate previous price lists for this user (and branch if specified)
                $query = UserPriceList::where('user_id', $request->user_id);
                if ($request->user_branch_id) {
                    $query->where('user_branch_id', $request->user_branch_id);
                } else {
                    $query->whereNull('user_branch_id');
                }
                $query->update(['is_active' => false]);

                // Create new price list
                $priceList = UserPriceList::create([
                    'user_id' => $request->user_id,
                    'user_branch_id' => $request->user_branch_id ?: null,
                    'price_date' => $request->price_date,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]);

                // Process rows - Format: TERMINAL, FLETE, MAGNA, PREMIUM, DIESEL
                foreach ($dataRows as $index => $row) {
                    $terminalName = trim($row[0] ?? '');

                    if (empty($terminalName)) {
                        continue;
                    }

                    // Try to find terminal by name
                    $terminal = FuelTerminal::where('name', 'LIKE', "%{$terminalName}%")->first();

                    UserPriceItem::create([
                        'user_price_list_id' => $priceList->id,
                        'fuel_terminal_id' => $terminal?->id,
                        'terminal_name' => $terminalName,
                        'shipping_price' => $this->parsePrice($row[1] ?? null) ?? 0,
                        'magna_price' => $this->parsePrice($row[2] ?? null),
                        'premium_price' => $this->parsePrice($row[3] ?? null),
                        'diesel_price' => $this->parsePrice($row[4] ?? null),
                        'sort_order' => $index,
                    ]);
                }

                // Ensure user has legends
                $this->ensureUserHasLegends($request->user_id);
            });

            return to_route('admin.user-price.index')
                ->with('success', 'Precios importados correctamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel layout.
     */
    public function downloadLayout()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Precios');

        // Headers - Now includes FLETE between TERMINAL and MAGNA
        $headers = ['TERMINAL', 'FLETE', 'MAGNA', 'PREMIUM', 'DIESEL'];
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
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Flete column header color (purple)
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('5C6BC0');

        // Sample data - Now includes FLETE
        $sampleData = [
            ['Valero Veracruz (RDP)', 0.50, 19.9943, 21.1387, 21.9464],
            ['Valero Puebla (RDP)', 0.45, 20.5295, 21.4457, 22.5617],
            ['Valero Tizayuca (RDP)', 0.35, 20.6943, 21.8537, 22.6817],
            ['Valero Tizayuca (ZM)', 0.35, 20.6027, 21.7937, 22.6817],
            ['Glencore Dos Bocas (RDP)', 0.60, 20.6425, 21.1124, 22.5389],
            ['Glencore Monterra (ZM)', 0.40, 19.3037, 19.6736, 20.1874],
            ['Altamira (RDP)', 0.55, 19.8867, 20.8386, 21.8722],
            ['Altamira (ZM)', 0.55, 19.9367, 20.8886, 21.8722],
            ['Nvo. Laredo 8%', 0.70, 18.5408, 19.3142, 20.5343],
            ['Nvo. Laredo 16%', 0.70, 19.8437, 20.6648, 21.9924],
            ['Valero Silos Tysa (RDP)', 0.30, 20.9110, 22.0165, 22.4805],
            ['Valero Silos Tysa (ZM)', 0.30, 21.0881, 22.5465, 22.5037],
            ['Shell MAOSA', 0, 19.4625, 19.2873, 21.0592],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Style data cells
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $lastRow = count($sampleData) + 1;
        $sheet->getStyle("A2:E{$lastRow}")->applyFromArray($dataStyle);

        // Format price columns
        $sheet->getStyle("B2:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.0000');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instrucciones');
        $instructionSheet->setCellValue('A1', 'INSTRUCCIONES DE USO');
        $instructionSheet->setCellValue('A3', '1. En la hoja "Precios", ingrese los datos de las terminales');
        $instructionSheet->setCellValue('A4', '2. La columna TERMINAL es obligatoria');
        $instructionSheet->setCellValue('A5', '3. La columna FLETE es el costo de envio (puede ser 0 si no aplica)');
        $instructionSheet->setCellValue('A6', '4. Los precios deben ser numericos con hasta 4 decimales');
        $instructionSheet->setCellValue('A7', '5. Puede dejar celdas de precios vacias si no aplica');
        $instructionSheet->setCellValue('A8', '6. No modifique la fila de encabezados');
        $instructionSheet->setCellValue('A9', '7. Puede agregar hasta 25 terminales');

        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Create response
        $writer = new Xlsx($spreadsheet);
        $filename = 'layout_precios_' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Parse price value from Excel.
     */
    private function parsePrice($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove currency symbols and spaces
        $value = preg_replace('/[^\d.,]/', '', $value);

        // Handle comma as decimal separator
        if (strpos($value, ',') !== false && strpos($value, '.') === false) {
            $value = str_replace(',', '.', $value);
        }

        // Remove thousands separator
        $value = str_replace(',', '', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Ensure user has legends.
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
