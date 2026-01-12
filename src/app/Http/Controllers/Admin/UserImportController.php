<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserImportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:access management index']);
    }

    /**
     * Show the import form.
     */
    public function index(): View
    {
        return view('admin.role-permission.role-user.import');
    }

    /**
     * Process the imported Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('excel_file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            if (empty($dataRows)) {
                return back()->with('error', 'El archivo está vacío o no contiene datos válidos');
            }

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($dataRows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we skip header and arrays are 0-indexed
                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');

                // Skip empty rows
                if (empty($name) && empty($email)) {
                    continue;
                }

                // Validate name
                if (empty($name)) {
                    $results[] = [
                        'row' => $rowNumber,
                        'name' => $name,
                        'email' => $email,
                        'password' => null,
                        'status' => 'error',
                        'message' => 'El nombre es requerido',
                    ];
                    $errorCount++;
                    continue;
                }

                // Validate email format
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $results[] = [
                        'row' => $rowNumber,
                        'name' => $name,
                        'email' => $email,
                        'password' => null,
                        'status' => 'error',
                        'message' => 'El email no es válido',
                    ];
                    $errorCount++;
                    continue;
                }

                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $results[] = [
                        'row' => $rowNumber,
                        'name' => $name,
                        'email' => $email,
                        'password' => null,
                        'status' => 'error',
                        'message' => 'El email ya está registrado',
                    ];
                    $errorCount++;
                    continue;
                }

                // Generate secure password
                $password = $this->generateSecurePassword(12);

                try {
                    // Create user
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'user_type' => 'user',
                        'is_approved' => true,
                    ]);

                    // Assign 'User' role
                    $user->assignRole('User');

                    $results[] = [
                        'row' => $rowNumber,
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'status' => 'success',
                        'message' => 'Usuario creado correctamente',
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    $results[] = [
                        'row' => $rowNumber,
                        'name' => $name,
                        'email' => $email,
                        'password' => null,
                        'status' => 'error',
                        'message' => 'Error al crear usuario: ' . $e->getMessage(),
                    ];
                    $errorCount++;
                }
            }

            // Generate result file
            $resultContent = $this->generateResultFile($results, $successCount, $errorCount);
            $filename = 'resultado_importacion_usuarios_' . date('Y-m-d_His') . '.txt';

            // Store result in session for download
            session()->put('import_result', [
                'content' => $resultContent,
                'filename' => $filename,
            ]);

            return redirect()->route('admin.user-import.result')
                ->with('success', "Importación completada. Registros exitosos: {$successCount}, Errores: {$errorCount}");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Show import result and allow download.
     */
    public function result(): View
    {
        $importResult = session()->get('import_result');

        if (!$importResult) {
            return redirect()->route('admin.user-import.index')
                ->with('error', 'No hay resultados de importación disponibles');
        }

        return view('admin.role-permission.role-user.import-result', [
            'content' => $importResult['content'],
            'filename' => $importResult['filename'],
        ]);
    }

    /**
     * Download the result file.
     */
    public function downloadResult()
    {
        $importResult = session()->get('import_result');

        if (!$importResult) {
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

    /**
     * Generate a secure password with uppercase, lowercase, numbers, and symbols.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Ensure at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest with random characters from all categories
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password to randomize character positions
        $passwordArray = str_split($password);
        shuffle($passwordArray);

        return implode('', $passwordArray);
    }

    /**
     * Generate the result file content.
     */
    private function generateResultFile(array $results, int $successCount, int $errorCount): string
    {
        $content = "========================================\n";
        $content .= "  RESULTADO DE IMPORTACIÓN DE USUARIOS\n";
        $content .= "========================================\n";
        $content .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Total procesados: " . count($results) . "\n";
        $content .= "Exitosos: {$successCount}\n";
        $content .= "Errores: {$errorCount}\n";
        $content .= "========================================\n\n";

        // Successful registrations
        $successResults = array_filter($results, fn($r) => $r['status'] === 'success');
        if (!empty($successResults)) {
            $content .= "--- USUARIOS REGISTRADOS ---\n\n";
            foreach ($successResults as $result) {
                $content .= "Fila: {$result['row']}\n";
                $content .= "Nombre: {$result['name']}\n";
                $content .= "Email: {$result['email']}\n";
                $content .= "Contraseña: {$result['password']}\n";
                $content .= "Estado: EXITOSO\n";
                $content .= "----------------------------\n\n";
            }
        }

        // Failed registrations
        $errorResults = array_filter($results, fn($r) => $r['status'] === 'error');
        if (!empty($errorResults)) {
            $content .= "--- REGISTROS CON ERROR ---\n\n";
            foreach ($errorResults as $result) {
                $content .= "Fila: {$result['row']}\n";
                $content .= "Nombre: " . ($result['name'] ?: '(vacío)') . "\n";
                $content .= "Email: " . ($result['email'] ?: '(vacío)') . "\n";
                $content .= "Error: {$result['message']}\n";
                $content .= "----------------------------\n\n";
            }
        }

        $content .= "========================================\n";
        $content .= "  FIN DEL REPORTE\n";
        $content .= "========================================\n";

        return $content;
    }
}
