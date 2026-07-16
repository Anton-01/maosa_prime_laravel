<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImportService
{
    private const PASSWORD_LENGTH = 12;

    /**
     * Process the uploaded Excel file, creating one user per valid row.
     *
     * @return array{results: array<int, array<string, mixed>>, successCount: int, errorCount: int}
     */
    public function import(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        // Skip header row
        $dataRows = array_slice($rows, 1);

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

            if (empty($name)) {
                $results[] = $this->errorRow($rowNumber, $name, $email, 'El nombre es requerido');
                $errorCount++;

                continue;
            }

            if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results[] = $this->errorRow($rowNumber, $name, $email, 'El email no es válido');
                $errorCount++;

                continue;
            }

            if (User::where('email', $email)->exists()) {
                $results[] = $this->errorRow($rowNumber, $name, $email, 'El email ya está registrado');
                $errorCount++;

                continue;
            }

            $password = $this->generateSecurePassword(self::PASSWORD_LENGTH);

            try {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'user_type' => 'user',
                    'is_approved' => true,
                ]);

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
                $results[] = $this->errorRow($rowNumber, $name, $email, 'Error al crear usuario: ' . $e->getMessage());
                $errorCount++;
            }
        }

        return [
            'results' => $results,
            'successCount' => $successCount,
            'errorCount' => $errorCount,
        ];
    }

    /**
     * Plain-text report with the outcome of every processed row,
     * including the generated passwords.
     *
     * @param array<int, array<string, mixed>> $results
     */
    public function generateResultFile(array $results, int $successCount, int $errorCount): string
    {
        $content = "========================================\n";
        $content .= "  RESULTADO DE IMPORTACIÓN DE USUARIOS\n";
        $content .= "========================================\n";
        $content .= 'Fecha: ' . date('Y-m-d H:i:s') . "\n";
        $content .= 'Total procesados: ' . count($results) . "\n";
        $content .= "Exitosos: {$successCount}\n";
        $content .= "Errores: {$errorCount}\n";
        $content .= "========================================\n\n";

        $successResults = array_filter($results, fn ($result) => $result['status'] === 'success');

        if (! empty($successResults)) {
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

        $errorResults = array_filter($results, fn ($result) => $result['status'] === 'error');

        if (! empty($errorResults)) {
            $content .= "--- REGISTROS CON ERROR ---\n\n";

            foreach ($errorResults as $result) {
                $content .= "Fila: {$result['row']}\n";
                $content .= 'Nombre: ' . ($result['name'] ?: '(vacío)') . "\n";
                $content .= 'Email: ' . ($result['email'] ?: '(vacío)') . "\n";
                $content .= "Error: {$result['message']}\n";
                $content .= "----------------------------\n\n";
            }
        }

        $content .= "========================================\n";
        $content .= "  FIN DEL REPORTE\n";
        $content .= "========================================\n";

        return $content;
    }

    /**
     * @return array<string, mixed>
     */
    private function errorRow(int $rowNumber, string $name, string $email, string $message): array
    {
        return [
            'row' => $rowNumber,
            'name' => $name,
            'email' => $email,
            'password' => null,
            'status' => 'error',
            'message' => $message,
        ];
    }

    /**
     * Generate a secure password with uppercase, lowercase, numbers and symbols.
     */
    private function generateSecurePassword(int $length): string
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

        $allChars = $uppercase . $lowercase . $numbers . $symbols;

        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        $passwordArray = str_split($password);
        shuffle($passwordArray);

        return implode('', $passwordArray);
    }
}
