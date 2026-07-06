<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Índices de apoyo para el panel de estadísticas (DataTable de usuarios más
 * activos) y la detección de usuarios inactivos (última sesión por usuario).
 * Idempotente: seguro de ejecutar en Dev y Prod.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_index
                ON public.user_sessions (user_id, created_at DESC)
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX IF NOT EXISTS page_visits_user_id_created_at_index
                ON public.page_visits (user_id, created_at)
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX IF NOT EXISTS users_id_estacion_index
                ON public.users (id_estacion)
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS public.user_sessions_user_id_created_at_index');
        DB::statement('DROP INDEX IF EXISTS public.page_visits_user_id_created_at_index');
        DB::statement('DROP INDEX IF EXISTS public.users_id_estacion_index');
    }
};
