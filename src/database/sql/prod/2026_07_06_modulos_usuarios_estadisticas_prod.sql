-- ============================================================================
-- Script PROD — Módulos: Gestión de Accesos, Estadísticas y Usuarios Inactivos
-- Base de datos: PostgreSQL
--
-- Notas:
--   * En Producción la tabla maosa_internal.cat_usuarios_importado YA existe
--     (la mantiene el proceso de importación). Este script NO la crea ni la
--     modifica; solo agrega índices de apoyo si faltan.
--   * Todas las sentencias son idempotentes (IF NOT EXISTS).
--   * No se insertan datos.
-- ============================================================================

BEGIN;

-- ----------------------------------------------------------------------------
-- 1. Índice para el filtro "estaciones activas" del select en Gestión de
--    Accesos > Usuarios. Se omite silenciosamente si la tabla no existe.
-- ----------------------------------------------------------------------------
DO $$
BEGIN
    IF to_regclass('maosa_internal.cat_usuarios_importado') IS NOT NULL THEN
        CREATE INDEX IF NOT EXISTS idx_cat_usuarios_importado_estatus_estacion
            ON maosa_internal.cat_usuarios_importado (estatus, id_estacion);
    ELSE
        RAISE NOTICE 'maosa_internal.cat_usuarios_importado no existe; revisar el proceso de importación.';
    END IF;
END
$$;

-- ----------------------------------------------------------------------------
-- 2. Índices de apoyo para el DataTable "Usuarios más activos" y la
--    detección/eliminación de usuarios inactivos (última sesión por usuario).
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_index
    ON public.user_sessions (user_id, created_at DESC);

CREATE INDEX IF NOT EXISTS page_visits_user_id_created_at_index
    ON public.page_visits (user_id, created_at);

CREATE INDEX IF NOT EXISTS users_id_estacion_index
    ON public.users (id_estacion);

COMMIT;

-- Verificación posterior (opcional):
-- SELECT indexname FROM pg_indexes
--  WHERE indexname IN (
--    'idx_cat_usuarios_importado_estatus_estacion',
--    'user_sessions_user_id_created_at_index',
--    'page_visits_user_id_created_at_index',
--    'users_id_estacion_index'
--  );
