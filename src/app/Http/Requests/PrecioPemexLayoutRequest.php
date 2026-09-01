<?php

namespace App\Http\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación de los endpoints de layout de Precios PEMEX.
 *
 * Dos reglas que el front no puede relajar:
 *
 *  - `fecha_vigencia` sólo admite ayer, hoy o mañana (misma regla que Precios
 *    Internacionales). Cualquier otra fecha corta la petición con un 422,
 *    aunque se inyecte a mano en la URL.
 *  - `estaciones` es una lista de ids sin repetidos y limitada a las
 *    estaciones asignadas al usuario en `usuario_estacion`.
 */
class PrecioPemexLayoutRequest extends FormRequest
{
    /** Tope defensivo de estaciones por petición. */
    public const MAX_ESTACIONES = 50;

    /** @var array<int, int>|null */
    private ?array $estacionesAsignadas = null;

    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * Normaliza la entrada antes de validarla: ids enteros, sin vacíos y sin
     * repetidos, y fecha por omisión = hoy. La deduplicación ocurre aquí para
     * que el ciclo del backend nunca pueda pedir dos veces la misma estación.
     */
    protected function prepareForValidation(): void
    {
        $crudo = $this->input('estaciones', []);

        if (is_string($crudo)) {
            $crudo = explode(',', $crudo);
        }

        if (! is_array($crudo)) {
            $crudo = [$crudo];
        }

        $ids = collect($crudo)
            ->map(fn ($valor) => is_numeric($valor) ? (int) $valor : null)
            ->filter(fn (?int $id) => $id !== null && $id > 0)
            ->unique()
            ->values()
            ->all();

        $fecha = trim((string) $this->input('fecha_vigencia', ''));

        $this->merge([
            'estaciones' => $ids,
            'fecha_vigencia' => $fecha !== '' ? $fecha : Carbon::today()->toDateString(),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'estaciones' => ['required', 'array', 'min:1', 'max:' . self::MAX_ESTACIONES],
            'estaciones.*' => ['required', 'integer', 'distinct', Rule::in($this->estacionesAsignadas())],
            'fecha_vigencia' => ['required', 'string', 'date_format:Y-m-d', Rule::in(self::fechasPermitidas())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'estaciones.required' => 'Seleccione al menos una estación.',
            'estaciones.min' => 'Seleccione al menos una estación.',
            'estaciones.max' => 'No es posible consultar más de ' . self::MAX_ESTACIONES . ' estaciones a la vez.',
            'estaciones.*.in' => 'No tiene acceso a alguna de las estaciones seleccionadas.',
            'fecha_vigencia.in' => 'La fecha de vigencia sólo puede ser ayer, hoy o mañana.',
            'fecha_vigencia.date_format' => 'La fecha de vigencia sólo puede ser ayer, hoy o mañana.',
        ];
    }

    /**
     * Ids de estación validados, únicos y ordenados como llegaron.
     *
     * @return array<int, int>
     */
    public function estaciones(): array
    {
        return array_values(array_unique(array_map('intval', $this->validated('estaciones'))));
    }

    public function fechaVigencia(): string
    {
        return (string) $this->validated('fecha_vigencia');
    }

    /**
     * Únicas tres fechas admitidas: ayer, hoy y mañana en la zona horaria de
     * la aplicación.
     *
     * @return array<int, string>
     */
    public static function fechasPermitidas(): array
    {
        return [
            Carbon::yesterday()->toDateString(),
            Carbon::today()->toDateString(),
            Carbon::tomorrow()->toDateString(),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function estacionesAsignadas(): array
    {
        /** @var User $user */
        $user = $this->user();

        return $this->estacionesAsignadas ??= $user->estacionesAsignadasIds();
    }
}
