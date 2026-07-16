# Contexto de Migración — Maosa Prime → Inertia.js + React + Ant Design

> **Propósito:** este archivo es la bitácora viva del proceso de rediseño. Se
> actualiza en **cada iteración** de la migración: qué se hizo, qué decisiones
> se tomaron y qué queda pendiente. Leerlo primero antes de migrar cualquier
> pantalla nueva.

---

## 🎯 Objetivo general

Migrar el panel de administración de Blade + Bootstrap/Stisla + jQuery a
**React puro con Inertia.js y Ant Design**, aplicando principios SOLID en el
backend (controladores delgados + servicios + FormRequests) y estandarizando
el código en inglés (la UI visible permanece en español).

## 📐 Reglas acordadas

1. **Solo Ant Design** en el frontend: `antd`, `@ant-design/icons` y
   `@ant-design/plots` (gráficas oficiales del ecosistema AntV). Nada de
   Bootstrap, FontAwesome, jQuery ni CSS de terceros en pantallas migradas.
2. **Código en inglés / UI en español.**
3. **Tablas**: `<Table />` de antd, paginación siempre abajo
   (`position: ['bottomRight']`), ordenamiento/filtros reactivos y `loading`
   manejado con eventos de Inertia (o `useDataTable` para server-side/Yajra).
4. **Pantallas con muchas secciones** → agrupar con `<Tabs />`.
5. **Fidelidad de negocio**: se respeta la lógica actual, solo se eleva la UI.

---

## ✅ Fase 1 — Infraestructura (completada)

| Pieza | Archivo |
|---|---|
| Paquete backend | `inertiajs/inertia-laravel: ^2.0` en `src/composer.json` ⚠️ requiere `composer install` en Docker (Packagist está bloqueado en el entorno del agente) |
| Paquetes frontend | `react@18`, `@inertiajs/react@2`, `antd@5`, `@ant-design/icons@5`, `@ant-design/plots@2`, `@vitejs/plugin-react` |
| Root view Inertia | `src/resources/views/app.blade.php` |
| Middleware compartido | `src/app/Http/Middleware/HandleInertiaRequests.php` (registrado en grupo `web` del Kernel). Comparte: `appName`, `auth.user`, `navigation` (árbol del sidebar filtrado por permisos Spatie, con flag `inertia` por item), `urls`, `flash` |
| Bootstrap React | `src/resources/js/app.jsx` — `ConfigProvider` locale `es_ES`, tema primario `#6777ef`, dayjs en español |
| **Layout principal** | `src/resources/js/Layouts/AdminLayout.jsx` — `<Layout>/<Sider>/<Header>/<Content>/<Footer>`; se aplica automático a `Pages/Admin/**`; flash de sesión → `message` de antd; logout vía form POST clásico |
| Contenedor de página | `src/resources/js/Components/PageContainer.jsx` (breadcrumb + título + acciones + Card opcional `wrapInCard`) |
| Hook tablas server-side | `src/resources/js/Hooks/useDataTable.js` — contrato: envía `page, per_page, sort_field, sort_order, search, filters[col]`; espera `{data, total}` |

**Convivencia Blade ↔ Inertia:** el menú lateral y los enlaces usan `<Link>`
solo si el item tiene `inertia: true` en `HandleInertiaRequests`; el resto son
`<a>` con recarga completa. Al migrar una pantalla, marcar su item como
`inertia => true`.

---

## ✅ Fase 2 — Dashboard + módulo Secciones (completada)

### Dashboard (`/admin/dashboard`)

- **Backend:** `DashboardController` quedó delgado; toda la agregación vive en
  `app/Services/Admin/DashboardStatsService` (cache 5 min, misma llave
  `admin_dashboard_stats`). Se agregaron comparativas vs semana anterior
  (`weeklyActivity.previous`) para mostrar tendencias.
- **Frontend:** `Pages/Admin/Dashboard/Index.jsx`. Estadísticas **segmentadas**
  en tarjetas: *Proveedores* (con % verificados/activos en `Progress`),
  *Usuarios*, *Catálogos y Contenido*, *Actividad de los últimos 7 días*
  (Line chart + tendencias), *Últimos usuarios / proveedores* (Tables),
  *Proveedores por categoría* (Pie chart) y *Accesos rápidos*.
- Los enlaces a pantallas aún no migradas (proveedores, usuarios, categorías,
  estadísticas) son anchors normales a Blade.

### Módulo Secciones (`/admin/sections`) — pantalla unificada con Tabs

Antes eran 4 items del sidebar (Banner, Banner público, Nuestras funciones,
Títulos - Secciones); ahora es **un solo item "Secciones"** →
`Pages/Admin/Sections/Index.jsx` con 4 tabs (`?tab=` en la URL):

| Tab | Partial | Endpoint que consume |
|---|---|---|
| `private-banner` | `Partials/BannerForm.jsx` | `PUT admin/hero` (POST + `_method=put`, multipart) |
| `public-banner` | `Partials/BannerForm.jsx` | `PUT admin/hero-public` |
| `features` | `Partials/FeaturesPanel.jsx` (tabla + modal crear/editar + Popconfirm eliminar) | `POST/PUT/DELETE admin/our-features` |
| `titles` | `Partials/SectionTitlesForm.jsx` | `POST admin/section-titles` |

- **Composición:** nuevo `SectionController@index` (ruta
  `admin.sections.index`) inyecta `HeroService`, `OurFeatureService` y
  `SectionTitleService`.
- **Rutas viejas conservadas** como redirects al tab correspondiente
  (`admin.hero.index`, `admin.hero.public.index`,
  `admin.our-features.index/create/edit`, `admin.section-title.index`) para no
  romper bookmarks.
- Los flash cambiaron de llaves ad-hoc (`statusHero`, `statusScnTtl`,
  `statusCdFeature`…) a `success` con mensaje en español (el layout los
  muestra como toast antd).

### Refactor SOLID aplicado

| Servicio | Responsabilidad |
|---|---|
| `app/Services/ImageUploadService.php` | Subida de imágenes inyectable (envuelve el `FileUploadTrait` legado) |
| `app/Services/Admin/DashboardStatsService.php` | Agregación y cache de métricas del dashboard |
| `app/Services/Admin/HeroService.php` | Lectura/actualización de banners por tipo + invalidación de cache `hero_private`/`hero_public` |
| `app/Services/Admin/OurFeatureService.php` | CRUD de funciones (payload mapeado para el frontend) |
| `app/Services/Admin/SectionTitleService.php` | Singleton de títulos de secciones (`FIELDS` como única fuente de campos editables) |

### FormRequests

- `HeroUpdateRequest` — **corregido**: ahora valida `sub_title` (antes se
  guardaba sin validar) y `old_background`; mensajes con atributos en español.
- `OurFeatureCreateRequest` — endurecido (`string`, `max`).
- `OurFeatureUpdateRequest` — **nuevo** (update ya no reutiliza el de create).
- `SectionTitleUpdateRequest` — ya existía y cubre los campos.

### Fixes de seguridad/consistencia detectados y aplicados

- `SectionTitleController` **no tenía middleware de permisos** → se agregó
  `permission:access management sections content` (igual que Hero/OurFeature).
- `OurFeature` sin `$fillable` → agregado (`icon, title, short_description, status`).

---

## ⚠️ Deudas / notas técnicas

- **`composer install` pendiente en Docker** (el entorno del agente no alcanza
  Packagist). Sin eso la app no levanta (el middleware Inertia está registrado).
- La tabla `heroes` usa columnas `type` y `active` que **no existen en ninguna
  migración** del repo (se agregaron directo en BD). Considerar una migración
  de regularización.
- Los **iconos de "Nuestras funciones" se guardan como clase FontAwesome**
  (ej. `fas fa-star`) porque el sitio público los renderiza así. En el admin se
  editan como texto plano. Si algún día se migra el sitio público, cambiar a un
  picker.
- Quedaron **obsoletos** (sin uso, pendientes de borrar en una fase de
  limpieza): `resources/views/admin/dashboard/`, `admin/hero/`,
  `admin/our-feature/`, `admin/section-title/` y
  `app/DataTables/OurFeatureDataTable.php`.
- El chunk del dashboard pesa ~1.5 MB minificado por `@ant-design/plots`
  (se carga lazy solo en esa página). Optimizable con `manualChunks` si estorba.
- `section_titles` tiene pares `our_our_pricing_*` y `our_testimonial_*` que la
  pantalla no gestiona (igual que la vista Blade original).

---

## 🗺️ Pendientes de migración (actualizar al completar)

- [x] Layout principal + infraestructura Inertia/antd
- [x] Dashboard
- [x] Secciones (Banner, Banner público, Nuestras funciones, Títulos) → tabs
- [ ] Proveedores: Categorías, Ubicaciones, Todos los Proveedores (+ amenities, schedules) — usar `useDataTable` + Yajra para la tabla principal
- [ ] Páginas: Sobre nosotros, Contacto, Política de privacidad, Términos y condiciones (candidata a pantalla unificada con tabs)
- [ ] Gestionar Footer
- [ ] Gestión de accesos: Usuarios, Usuarios inactivos, Roles y permisos
- [ ] Estadísticas (Panel General + detalle de usuario/sesiones)
- [ ] Menús (menu builder)
- [ ] Ajustes
- [ ] Perfil
- [ ] Login/Forgot password del admin
- [ ] Fase de limpieza: borrar blades/datatables/assets legados (Stisla, jQuery, FontAwesome del admin)

## 📌 Checklist al migrar cada pantalla

1. Controlador delgado → `Inertia::render('Admin/Modulo/Pantalla', [...])`;
   lógica en `app/Services/Admin/*`.
2. FormRequests para store/update (crearlos si faltan) con atributos en español.
3. Página en `resources/js/Pages/Admin/**` (hereda `AdminLayout` automático);
   usar `PageContainer`.
4. Tablas: paginación abajo, sorters/filters, loading por eventos Inertia;
   si es pesada, endpoint JSON + `useDataTable`.
5. Flash: `->with('success', '...')` / `->with('error', '...')` en español.
6. Marcar el item del menú como `inertia => true` en `HandleInertiaRequests`.
7. Rutas viejas → redirect si la URL cambia.
8. `npm run build` + `php -l` antes de commitear.
9. **Actualizar este archivo** (fase completada, deudas nuevas, checklist).
