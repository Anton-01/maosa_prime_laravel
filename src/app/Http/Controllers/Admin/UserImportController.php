<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserImportRequest;
use App\Services\Admin\UserImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserImportController extends Controller
{
    public function __construct(private readonly UserImportService $userImportService)
    {
        $this->middleware(['permission:access management index']);
    }

    /**
     * Show the import form.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/Users/Import', [
            'urls' => [
                'import' => route('admin.user-import.store'),
                'layout' => route('admin.user-import.layout'),
                'users' => route('admin.role-user.index'),
            ],
        ]);
    }

    /**
     * Process the imported Excel file.
     */
    public function import(UserImportRequest $request): RedirectResponse
    {
        try {
            $outcome = $this->userImportService->import($request->file('excel_file'));

            if (empty($outcome['results'])) {
                return back()->with('error', 'El archivo está vacío o no contiene datos válidos');
            }

            $resultContent = $this->userImportService->generateResultFile(
                $outcome['results'],
                $outcome['successCount'],
                $outcome['errorCount'],
            );

            // Store result in session for the result screen and download
            session()->put('import_result', [
                'content' => $resultContent,
                'filename' => 'resultado_importacion_usuarios_' . date('Y-m-d_His') . '.txt',
            ]);

            return redirect()->route('admin.user-import.result')->with(
                'success',
                "Importación completada. Registros exitosos: {$outcome['successCount']}, Errores: {$outcome['errorCount']}",
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Show import result and allow download.
     */
    public function result(): InertiaResponse|RedirectResponse
    {
        $importResult = session()->get('import_result');

        if (! $importResult) {
            return redirect()->route('admin.user-import.index')
                ->with('error', 'No hay resultados de importación disponibles');
        }

        return Inertia::render('Admin/Users/ImportResult', [
            'content' => $importResult['content'],
            'filename' => $importResult['filename'],
            'urls' => [
                'download' => route('admin.user-import.download'),
                'import' => route('admin.user-import.index'),
                'users' => route('admin.role-user.index'),
            ],
        ]);
    }

    /**
     * Download the result file.
     */
    public function downloadResult()
    {
        $importResult = session()->get('import_result');

        if (! $importResult) {
            return redirect()->route('admin.user-import.index')
                ->with('error', 'No hay resultados de importación disponibles');
        }

        // Clear the session
        session()->forget('import_result');

        return Response::make($importResult['content'], 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $importResult['filename'] . '"',
        ]);
    }

    /**
     * Download sample Excel layout.
     */
    public function downloadLayout()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios');

        // Headers
        $headers = ['NOMBRE', 'EMAIL'];
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
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Juan Pérez', 'juan.perez@example.com'],
            ['María García', 'maria.garcia@example.com'],
            ['Carlos López', 'carlos.lopez@example.com'],
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
        $sheet->getStyle("A2:B{$lastRow}")->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(40);

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instrucciones');
        $instructionSheet->setCellValue('A1', 'INSTRUCCIONES DE USO');
        $instructionSheet->setCellValue('A3', '1. En la hoja "Usuarios", ingrese los datos de los usuarios a importar');
        $instructionSheet->setCellValue('A4', '2. La columna NOMBRE es obligatoria');
        $instructionSheet->setCellValue('A5', '3. La columna EMAIL es obligatoria y debe ser único');
        $instructionSheet->setCellValue('A6', '4. No modifique la fila de encabezados');
        $instructionSheet->setCellValue('A7', '5. Los usuarios importados tendrán:');
        $instructionSheet->setCellValue('A8', '   - Rol: User');
        $instructionSheet->setCellValue('A9', '   - Estado: Aprobado');
        $instructionSheet->setCellValue('A10', '   - Contraseña: Generada automáticamente (12 caracteres)');
        $instructionSheet->setCellValue('A11', '6. Al finalizar la importación, se descargará un archivo TXT con los resultados');
        $instructionSheet->setCellValue('A12', '   incluyendo las contraseñas asignadas a cada usuario');

        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getColumnDimension('A')->setWidth(70);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Create response
        $writer = new Xlsx($spreadsheet);
        $filename = 'layout_usuarios_' . date('Y-m-d') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
