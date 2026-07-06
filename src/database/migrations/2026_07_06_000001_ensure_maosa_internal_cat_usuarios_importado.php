<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Garantiza la existencia del catálogo maosa_internal.cat_usuarios_importado,
 * consumido por el select de estaciones en la gestión de usuarios.
 *
 * En Producción el schema y la tabla ya existen (los mantiene el proceso de
 * importación), por lo que todas las sentencias usan IF NOT EXISTS y la
 * migración es idempotente: allí solo agrega el índice de apoyo si falta.
 * En Desarrollo crea el schema y la tabla replicando la estructura real
 * (id_estacion, estacion, id_socio, socio, activo, fecha_creacion,
 * fecha_actualizacion).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE SCHEMA IF NOT EXISTS maosa_internal');

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS maosa_internal.cat_usuarios_importado (
                id_estacion integer,
                estacion character varying(255),
                id_socio integer,
                socio character varying(255),
                activo boolean DEFAULT true NOT NULL,
                fecha_creacion timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion timestamp with time zone
            )
        SQL);

        // Índice para el filtro "estaciones activas" del select de usuarios
        DB::statement(<<<'SQL'
            CREATE INDEX IF NOT EXISTS idx_cat_usuarios_importado_activo_estacion
                ON maosa_internal.cat_usuarios_importado (activo, id_estacion)
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // No se elimina la tabla ni el schema: en Producción contienen datos
        // administrados por procesos externos. Solo se revierte el índice creado aquí.
        DB::statement('DROP INDEX IF EXISTS maosa_internal.idx_cat_usuarios_importado_activo_estacion');
    }
};
