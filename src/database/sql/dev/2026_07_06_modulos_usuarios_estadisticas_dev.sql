-- ============================================================================
-- Script DEV — Módulos: Gestión de Accesos, Estadísticas y Usuarios Inactivos
-- Base de datos: PostgreSQL
-- Equivalente raw SQL de las migraciones:
--   2026_07_06_000001_ensure_maosa_internal_cat_usuarios_importado
--   2026_07_06_000002_add_activity_indexes_for_user_tracking
-- Idempotente: puede ejecutarse varias veces sin error.
-- ============================================================================

BEGIN;

-- ----------------------------------------------------------------------------
-- 1. Catálogo de socios/estaciones (fuente del select de estaciones activas)
--    Vive en el schema maosa_internal (fuera del search_path por defecto).
--    En DEV normalmente no existe; se crea replicando la estructura real:
--    id_estacion, estacion, id_socio, socio, activo, fecha_creacion,
--    fecha_actualizacion.
-- ----------------------------------------------------------------------------
CREATE SCHEMA IF NOT EXISTS maosa_internal;

CREATE TABLE IF NOT EXISTS maosa_internal.cat_usuarios_importado (
    id_estacion integer,
    estacion character varying(255),
    id_socio integer,
    socio character varying(255),
    activo boolean DEFAULT true NOT NULL,
    fecha_creacion timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion timestamp with time zone
);

CREATE INDEX IF NOT EXISTS idx_cat_usuarios_importado_activo_estacion
    ON maosa_internal.cat_usuarios_importado (activo, id_estacion);

-- Datos de ejemplo SOLO para desarrollo (no ejecutar en Prod)
INSERT INTO maosa_internal.cat_usuarios_importado (id_estacion, estacion, id_socio, socio, activo, fecha_creacion)
SELECT * FROM (VALUES
    (51,  '1964-KAPETROM ZAPOPAN',      6::integer,  'Comercializadora Kapetrom'::varchar, true,  NOW()),
    (106, '1963-KAPETROM CASTILLO',     6,           'Comercializadora Kapetrom',          true,  NOW()),
    (5,   '1846-DICOMEX ATITALAQUIA TULA', 8,        'DICOMEX',                            true,  NOW()),
    (21,  'MA527-DIHICO PUEBLA',        9,           'DIHICO',                             true,  NOW()),
    (55,  'CASTELLANOS',                15,          'Grupo Castellanos',                  true,  NOW()),
    (99,  'ESTACION INACTIVA DEMO',     15,          'Grupo Castellanos',                  false, NOW())
) AS seed(id_estacion, estacion, id_socio, socio, activo, fecha_creacion)
WHERE NOT EXISTS (SELECT 1 FROM maosa_internal.cat_usuarios_importado);

-- ----------------------------------------------------------------------------
-- 2. Índices de apoyo para estadísticas y detección de usuarios inactivos
-- ----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_index
    ON public.user_sessions (user_id, created_at DESC);

CREATE INDEX IF NOT EXISTS page_visits_user_id_created_at_index
    ON public.page_visits (user_id, created_at);

CREATE INDEX IF NOT EXISTS users_id_estacion_index
    ON public.users (id_estacion);

COMMIT;
