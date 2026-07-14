-- ============================================================================
-- AJUSTES DE BASE DE DATOS (PostgreSQL) — Dev y Prod
-- Módulos: Gestión de Accesos, Estadísticas y Usuarios Inactivos
--
-- Ejecución directa desde consola:
--   psql -h <host> -U <usuario> -d <base_de_datos> -f ajustes_bd_modulos_usuarios_estadisticas.sql
--
-- IMPORTANTE:
--   * NO crea ni modifica maosa_internal.cat_usuarios_importado: la tabla ya
--     existe y la administra el proceso de importación. La aplicación solo la
--     consulta (calificada con el schema) para poblar el select de estaciones
--     activas (columnas usadas: id_estacion, estacion, activo).
--   * NO se requieren nuevas columnas en tablas existentes: la "fecha última
--     sesión" y la "ubicación última sesión" se calculan en tiempo real desde
--     public.user_sessions (created_at, city, region, country), por lo que no
--     hay sentencias ALTER TABLE que aplicar.
--   * Los únicos ajustes necesarios son índices de apoyo para rendimiento.
--     Todas las sentencias son idempotentes (IF NOT EXISTS): el script puede
--     ejecutarse varias veces sin error y sin tocar datos.
-- ============================================================================

BEGIN;

-- ----------------------------------------------------------------------------
-- 1. Índice para el filtro de estaciones activas del select en
--    Gestión de Accesos > Usuarios (crear/editar).
--    Consulta que optimiza:
--      SELECT DISTINCT id_estacion, estacion
--        FROM maosa_internal.cat_usuarios_importado
--       WHERE id_estacion IS NOT NULL AND activo = true
--       ORDER BY estacion;
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS idx_cat_usuarios_importado_activo_estacion
    ON maosa_internal.cat_usuarios_importado (activo, id_estacion);

-- ----------------------------------------------------------------------------
-- 2. Índice para obtener la última sesión por usuario (DataTable "Usuarios
--    más activos" del Panel General y detección de usuarios inactivos).
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_index
    ON public.user_sessions (user_id, created_at DESC);

-- ----------------------------------------------------------------------------
-- 3. Índice para los conteos de visitas por usuario y rango de fechas
--    (columnas VISITAS del DataTable del Panel General).
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS page_visits_user_id_created_at_index
    ON public.page_visits (user_id, created_at);

-- ----------------------------------------------------------------------------
-- 4. Índice sobre la estación asignada al usuario (toggle de tabla de precios).
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS users_id_estacion_index
    ON public.users (id_estacion);

COMMIT;

-- ----------------------------------------------------------------------------
-- Verificación posterior (opcional):
-- ----------------------------------------------------------------------------
-- SELECT schemaname, indexname
--   FROM pg_indexes
--  WHERE indexname IN (
--        'idx_cat_usuarios_importado_activo_estacion',
--        'user_sessions_user_id_created_at_index',
--        'page_visits_user_id_created_at_index',
--        'users_id_estacion_index'
--  );
