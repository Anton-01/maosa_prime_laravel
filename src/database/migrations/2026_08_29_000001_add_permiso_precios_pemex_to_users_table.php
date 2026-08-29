<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REQ-02: bandera de acceso al submódulo "Precios PEMEX".
 *
 * La tabla pivote `usuario_estacion` (REQ-01) se creó por script fuera de las
 * migraciones, por lo que aquí sólo se agrega la columna en `users`. La
 * migración es idempotente para entornos donde la columna ya se haya agregado
 * manualmente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'permiso_precios_pemex')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('permiso_precios_pemex')->default(false)->after('can_view_price_table');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'permiso_precios_pemex')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permiso_precios_pemex');
        });
    }
};
