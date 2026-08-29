<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Catálogo nacional de estaciones (foreign table `origen.cat_estacion_nacional`).
 *
 * Vive fuera del search_path por defecto de la aplicación, por eso la tabla se
 * referencia calificada con el schema. Es de sólo lectura: la administran los
 * procesos de importación del origen, nunca la aplicación.
 *
 * Columnas usadas: id_estacion (PK lógica), estacion, activo (boolean).
 */
class EstacionNacional extends Model
{
    protected $table = 'origen.cat_estacion_nacional';

    protected $primaryKey = 'id_estacion';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'int';

    protected $casts = [
        'id_estacion' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Estaciones activas, únicas y ordenadas por nombre, para poblar los
     * selectores de asignación y del submódulo de Precios PEMEX.
     *
     * @return Collection<int, static>
     */
    public static function activas(): Collection
    {
        try {
            return static::query()
                ->select('id_estacion', 'estacion')
                ->whereNotNull('id_estacion')
                ->where('activo', true)
                ->distinct()
                ->orderBy('estacion')
                ->get();
        } catch (\Throwable $e) {
            // La foreign table puede no estar disponible en todos los entornos.
            logger()->warning('No fue posible leer origen.cat_estacion_nacional', ['exception' => $e]);

            return collect();
        }
    }

    /**
     * Estaciones activas filtradas por id, preservando el orden por nombre.
     *
     * @param  array<int, int>  $ids
     * @return Collection<int, static>
     */
    public static function activasPorIds(array $ids): Collection
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return collect();
        }

        try {
            return static::query()
                ->select('id_estacion', 'estacion')
                ->whereIn('id_estacion', $ids)
                ->where('activo', true)
                ->distinct()
                ->orderBy('estacion')
                ->get();
        } catch (\Throwable $e) {
            logger()->warning('No fue posible leer origen.cat_estacion_nacional', ['exception' => $e]);

            return collect();
        }
    }

    /**
     * Verifica que una estación exista y esté activa en el catálogo nacional.
     */
    public static function esActiva(int $idEstacion): bool
    {
        try {
            return static::query()
                ->where('id_estacion', $idEstacion)
                ->where('activo', true)
                ->exists();
        } catch (\Throwable $e) {
            logger()->warning('No fue posible validar la estación nacional', ['exception' => $e]);

            // Si el catálogo no está disponible no bloqueamos el guardado.
            return true;
        }
    }

    /**
     * Opciones `{ value, label }` listas para los selects de Ant Design.
     *
     * @param  Collection<int, static>|null  $estaciones
     * @return array<int, array{value: int, label: string}>
     */
    public static function comoOpciones(?Collection $estaciones = null): array
    {
        return ($estaciones ?? static::activas())
            ->map(fn (self $estacion) => [
                'value' => (int) $estacion->id_estacion,
                'label' => $estacion->estacion ?: "Estación {$estacion->id_estacion}",
            ])
            ->values()
            ->all();
    }
}
