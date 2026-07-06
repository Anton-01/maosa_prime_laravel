<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Catálogo de socios/estaciones importado (schema maosa_internal).
 * Tabla de solo lectura administrada por procesos externos de importación.
 */
class CatUsuarioImportado extends Model
{
    protected $table = 'maosa_internal.cat_usuarios_importado';

    public $timestamps = false;

    /**
     * Estaciones con estatus activo, únicas y ordenadas por nombre,
     * para poblar el select de "Mostrar tabla de precios".
     */
    public static function estacionesActivas(): Collection
    {
        try {
            return static::query()
                ->select('id_estacion', 'estacion')
                ->whereNotNull('id_estacion')
                ->where('estatus', true)
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
                ->where('estatus', true)
                ->exists();
        } catch (\Throwable $e) {
            logger($e);
            // Si el catálogo no está disponible no bloqueamos el guardado
            return true;
        }
    }
}
