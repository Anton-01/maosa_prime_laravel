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
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BulkPriceImportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user prices import']);
    }

    /**
     * Show the bulk import form.
     */
    public function index(): View
    {
        return view('admin.bulk-price-import.index');
    }

    /**
     * Process the bulk imported Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'price_date' => 'required|date',
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('excel_file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $results = [];
            $currentUserEmail = null;
            $currentBranchName = null;
            $currentItems = [];
            $lineNumber = 0;

            foreach ($rows as $row) {
                $lineNumber++;
                $firstCell = trim($row[0] ?? '');

                // Skip empty rows
                if (empty($firstCell) && empty(trim($row[1] ?? ''))) {
                    continue;
                }

                // Check if it's a user marker
                if (stripos($firstCell, '[USUARIO:') !== false) {
                    // Save previous user's data if exists
                    if ($currentUserEmail && !empty($currentItems)) {
                        $result = $this->processUserPriceList(
                            $currentUserEmail,
                            $currentBranchName,
                            $currentItems,
                            $request->price_date
                        );
                        $results[] = $result;
                    }

                    // Extract email
                    preg_match('/\[USUARIO:\s*(.+?)\s*\]/', $firstCell, $matches);
                    $currentUserEmail = $matches[1] ?? null;
                    $currentBranchName = null;
                    $currentItems = [];
                    continue;
                }

                // Check if it's a branch marker
                if (stripos($firstCell, '[SUCURSAL:') !== false) {
                    // Save previous branch's data if exists
                    if ($currentUserEmail && !empty($currentItems)) {
                        $result = $this->processUserPriceList(
                            $currentUserEmail,
                            $currentBranchName,
                            $currentItems,
                            $request->price_date
                        );
                        $results[] = $result;
                    }

                    // Extract branch name
                    preg_match('/\[SUCURSAL:\s*(.+?)\s*\]/', $firstCell, $matches);
                    $currentBranchName = $matches[1] ?? null;
                    $currentItems = [];
                    continue;
                }

                // Skip header row (TERMINAL, FLETE, MAGNA, PREMIUM, DIESEL)
                if (strtoupper($firstCell) === 'TERMINAL') {
                    continue;
                }

                // It's a data row
                if ($currentUserEmail && !empty($firstCell)) {
                    $currentItems[] = [
                        'terminal_name' => $firstCell,
                        'shipping_price' => $this->parsePrice($row[1] ?? null) ?? 0,
                        'magna_price' => $this->parsePrice($row[2] ?? null),
                        'premium_price' => $this->parsePrice($row[3] ?? null),
                        'diesel_price' => $this->parsePrice($row[4] ?? null),
                    ];
                }
            }

            // Process last user/branch
            if ($currentUserEmail && !empty($currentItems)) {
                $result = $this->processUserPriceList(
                    $currentUserEmail,
                    $currentBranchName,
                    $currentItems,
                    $request->price_date
                );
                $results[] = $result;
            }

            // Store results in session
            Session::put('bulk_import_results', $results);
            Session::put('bulk_import_date', now()->format('d/m/Y H:i'));

            return to_route('admin.bulk-price-import.result');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Process a single user's price list.
     */
    private function processUserPriceList(string $email, ?string $branchName, array $items, string $priceDate): array
    {
        $result = [
            'email' => $email,
            'branch' => $branchName,
            'items_count' => count($items),
            'status' => 'error',
            'message' => '',
        ];

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $result['message'] = 'Usuario no encontrado';
            return $result;
        }

        // Check if user can view price table
        if (!$user->can_view_price_table) {
            $result['message'] = 'Usuario no tiene acceso a tabla de precios';
            return $result;
        }

        $branchId = null;

        // If branch is specified, find it
        if ($branchName) {
            $branch = UserBranch::where('user_id', $user->id)
                ->where('name', $branchName)
                ->first();

            if (!$branch) {
                // Create the branch
                $branch = UserBranch::create([
                    'user_id' => $user->id,
                    'name' => $branchName,
                    'is_active' => true,
                ]);
                $result['branch_created'] = true;
            }

            $branchId = $branch->id;
        }

        try {
            DB::transaction(function () use ($user, $branchId, $items, $priceDate) {
                // Deactivate previous price lists for this user/branch combination
                $query = UserPriceList::where('user_id', $user->id);
                if ($branchId) {
                    $query->where('user_branch_id', $branchId);
                } else {
                    $query->whereNull('user_branch_id');
                }
                $query->update(['is_active' => false]);

                // Create new price list
                $priceList = UserPriceList::create([
                    'user_id' => $user->id,
                    'user_branch_id' => $branchId,
                    'price_date' => $priceDate,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]);

                // Create price items
                foreach ($items as $index => $item) {
                    // Try to find terminal by name
                    $terminal = FuelTerminal::where('name', 'LIKE', "%{$item['terminal_name']}%")->first();

                    UserPriceItem::create([
                        'user_price_list_id' => $priceList->id,
                        'fuel_terminal_id' => $terminal?->id,
                        'terminal_name' => $item['terminal_name'],
                        'shipping_price' => $item['shipping_price'],
                        'magna_price' => $item['magna_price'],
                        'premium_price' => $item['premium_price'],
                        'diesel_price' => $item['diesel_price'],
                        'sort_order' => $index,
                    ]);
                }

                // Ensure user has legends
                $this->ensureUserHasLegends($user->id);
            });

            $result['status'] = 'success';
            $result['message'] = 'Importado correctamente';
            $result['user_name'] = $user->name;

        } catch (\Exception $e) {
            $result['message'] = 'Error: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Show import results.
     */
    public function result(): View
    {
        $results = Session::get('bulk_import_results', []);
        $importDate = Session::get('bulk_import_date', '');

        return view('admin.bulk-price-import.result', compact('results', 'importDate'));
    }

    /**
     * Download import results as Excel.
     */
    public function downloadResult()
    {
        $results = Session::get('bulk_import_results', []);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resultados');

        // Headers
        $headers = ['Email', 'Usuario', 'Sucursal', 'Terminales', 'Estado', 'Mensaje'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($results as $result) {
            $sheet->setCellValue('A' . $row, $result['email']);
            $sheet->setCellValue('B' . $row, $result['user_name'] ?? '-');
            $sheet->setCellValue('C' . $row, $result['branch'] ?? 'Sin sucursal');
            $sheet->setCellValue('D' . $row, $result['items_count']);
            $sheet->setCellValue('E' . $row, $result['status'] == 'success' ? 'Exitoso' : 'Error');
            $sheet->setCellValue('F' . $row, $result['message']);

            // Color based on status
            if ($result['status'] == 'success') {
                $sheet->getStyle('E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C8E6C9');
            } else {
                $sheet->getStyle('E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCDD2');
            }

            $row++;
        }

        // Style data
        $lastRow = $row - 1;
        $sheet->getStyle("A2:F{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(40);

        $writer = new Xlsx($spreadsheet);
        $filename = 'resultado_carga_masiva_' . date('Y-m-d_His') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download sample Excel layout for bulk import.
     */
    public function downloadLayout()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Carga Masiva');

        // Example data
        $data = [
            ['[USUARIO: cliente1@ejemplo.com]', '', '', '', ''],
            ['[SUCURSAL: Sucursal Norte]', '', '', '', ''],
            ['TERMINAL', 'FLETE', 'MAGNA', 'PREMIUM', 'DIESEL'],
            ['Valero Veracruz (RDP)', 0.50, 19.9943, 21.1387, 21.9464],
            ['Valero Puebla (RDP)', 0.45, 20.5295, 21.4457, 22.5617],
            ['Altamira (RDP)', 0.55, 19.8867, 20.8386, 21.8722],
            ['', '', '', '', ''],
            ['[SUCURSAL: Sucursal Sur]', '', '', '', ''],
            ['TERMINAL', 'FLETE', 'MAGNA', 'PREMIUM', 'DIESEL'],
            ['Glencore Dos Bocas (RDP)', 0.60, 20.6425, 21.1124, 22.5389],
            ['Shell MAOSA', 0, 19.4625, 19.2873, 21.0592],
            ['', '', '', '', ''],
            ['[USUARIO: cliente2@ejemplo.com]', '', '', '', ''],
            ['TERMINAL', 'FLETE', 'MAGNA', 'PREMIUM', 'DIESEL'],
            ['Valero Tizayuca (RDP)', 0.35, 20.6943, 21.8537, 22.6817],
            ['Nvo. Laredo 8%', 0.70, 18.5408, 19.3142, 20.5343],
            ['Nvo. Laredo 16%', 0.70, 19.8437, 20.6648, 21.9924],
        ];

        $sheet->fromArray($data, null, 'A1');

        // Style user markers
        $userRows = [1, 13];
        foreach ($userRows as $rowNum) {
            $sheet->getStyle("A{$rowNum}:E{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            ]);
        }

        // Style branch markers
        $branchRows = [2, 8];
        foreach ($branchRows as $rowNum) {
            $sheet->getStyle("A{$rowNum}:E{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7B1FA2']],
            ]);
        }

        // Style header rows
        $headerRows = [3, 9, 14];
        foreach ($headerRows as $rowNum) {
            $sheet->getStyle("A{$rowNum}:E{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            // Flete column purple
            $sheet->getStyle("B{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('5C6BC0');
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);

        // Format number columns
        $sheet->getStyle('B4:E6')->getNumberFormat()->setFormatCode('#,##0.0000');
        $sheet->getStyle('B10:E11')->getNumberFormat()->setFormatCode('#,##0.0000');
        $sheet->getStyle('B15:E17')->getNumberFormat()->setFormatCode('#,##0.0000');

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instrucciones');

        $instructions = [
            ['INSTRUCCIONES DE USO - CARGA MASIVA DE PRECIOS'],
            [''],
            ['FORMATO DEL ARCHIVO:'],
            [''],
            ['1. [USUARIO: email@ejemplo.com] - Indica el inicio de precios para un usuario (identificado por email)'],
            ['2. [SUCURSAL: Nombre Sucursal] - (OPCIONAL) Indica la sucursal para los precios siguientes'],
            ['3. Encabezados: TERMINAL, FLETE, MAGNA, PREMIUM, DIESEL'],
            ['4. Filas de datos con los precios por terminal'],
            [''],
            ['REGLAS:'],
            [''],
            ['- El usuario DEBE existir en el sistema (se busca por email)'],
            ['- El usuario DEBE tener acceso a tabla de precios activado'],
            ['- Si se especifica una sucursal que no existe, se creara automaticamente'],
            ['- Si no se especifica sucursal, los precios se asignan sin sucursal'],
            ['- Un mismo usuario puede tener multiples sucursales'],
            ['- Cada sucursal tiene su propia lista de precios'],
            ['- Al importar se desactivan las listas anteriores del usuario/sucursal'],
            [''],
            ['COLUMNAS:'],
            [''],
            ['- TERMINAL: Nombre de la terminal (obligatorio)'],
            ['- FLETE: Costo de envio por terminal (0 si no aplica)'],
            ['- MAGNA: Precio de gasolina Magna'],
            ['- PREMIUM: Precio de gasolina Premium'],
            ['- DIESEL: Precio de Diesel'],
        ];

        foreach ($instructions as $index => $row) {
            $instructionSheet->setCellValue('A' . ($index + 1), $row[0] ?? '');
        }

        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getStyle('A3')->getFont()->setBold(true);
        $instructionSheet->getStyle('A10')->getFont()->setBold(true);
        $instructionSheet->getStyle('A19')->getFont()->setBold(true);
        $instructionSheet->getColumnDimension('A')->setWidth(80);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'layout_carga_masiva_' . date('Y-m-d') . '.xlsx';

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

        $value = preg_replace('/[^\d.,]/', '', $value);

        if (strpos($value, ',') !== false && strpos($value, '.') === false) {
            $value = str_replace(',', '.', $value);
        }

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
