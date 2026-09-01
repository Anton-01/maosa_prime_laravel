<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Catálogo de socios/estaciones importado (schema maosa_internal, fuera del
 * search_path por defecto de la aplicación, por eso la tabla se referencia
 * calificada con el schema).
 *
 * Estructura real: id_estacion, estacion, id_socio, socio, activo (boolean),
 * fecha_creacion, fecha_actualizacion. Tabla de solo lectura administrada
 * por procesos externos de importación.
 */
class CatUsuarioImportado extends Model
{
    protected $table = 'maosa_internal.cat_usuarios_importado';

    public $timestamps = false;

    /**
     * Estaciones activas (activo = true), únicas y ordenadas por nombre,
     * para poblar el select de "Precios Internacionales".
     */
    public static function estacionesActivas(): Collection
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
            // La tabla vive en otro schema y puede no existir en todos los entornos
            logger($e);
            return collect();
        }
    }

    /**
     * Verifica que una estación exista y esté activa en el catálogo.
     */
    public static function esEstacionActiva(int $idEstacion): bool
    {
        try {
            return static::query()
                ->where('id_estacion', $idEstacion)
                ->where('activo', true)
                ->exists();
        } catch (\Throwable $e) {
            logger($e);
            return true;
        }
    }
}
