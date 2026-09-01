BEGIN;

CREATE INDEX IF NOT EXISTS idx_cat_usuarios_importado_activo_estacion
    ON maosa_internal.cat_usuarios_importado (activo, id_estacion);

CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_index
    ON public.user_sessions (user_id, created_at DESC);

CREATE INDEX IF NOT EXISTS user_sessions_user_id_created_at_ip_index
    ON public.user_sessions (user_id, created_at, ip_address);

CREATE INDEX IF NOT EXISTS page_visits_user_id_created_at_index
    ON public.page_visits (user_id, created_at);

CREATE INDEX IF NOT EXISTS users_id_estacion_index
    ON public.users (id_estacion);
COMMIT;
