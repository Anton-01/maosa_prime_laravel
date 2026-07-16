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

## ✅ Fase 3 — Proveedores, Páginas y Gestión de accesos (completada)

### Proveedores

- **Categorías** (`/admin/category`) → `Pages/Admin/Categories/Index.jsx`: tabla
  antd + modal crear/editar (2 uploads: icono y fondo). `CategoryService`
  (uploads, slug, cache flush). Create/edit legacy redirigen al index.
- **Ubicaciones** (`/admin/location`) → `Pages/Admin/Locations/Index.jsx`: mismo
  patrón sin imágenes. `LocationService`. **Fix:** `update()` usaba `Request`
  plano; ahora usa el `LocationUpdateRequest` que ya existía sin conectar.
- **Todos los Proveedores** (`/admin/listing`) → `Pages/Admin/Listings/Index.jsx`
  (tabla con filtros por categoría/ubicación/estatus/destacado/verificado) y
  `Pages/Admin/Listings/Form.jsx`: formulario **con Tabs** (Información general,
  Contacto y redes —con `Form.List` para redes sociales—, SEO, Configuración).
  `ListingService` (uploads, slug, social links sync, cache flush).
  Las sub-pantallas de **Horarios** y **Servicios (amenities)** del proveedor
  siguen en Blade (enlaces con recarga completa) — pendientes de una fase futura.

### Páginas — pantalla unificada `/admin/pages` (Tabs)

4 items del sidebar → **un solo item "Páginas"** → `Pages/Admin/Pages/Index.jsx`
con tabs `about | contact | privacy-policy | terms` (`?tab=` en URL). Rutas
viejas redirigen al tab. `StaticPageService` concentra los `updateOrCreate`.
- **Nuevos FormRequests:** `PrivacyPolicyUpdateRequest` y
  `TermsAndConditionUpdateRequest` (antes validaban inline en el controlador);
  `AboutUsUpdateRequest` ahora valida `old_image`.
- **Decisión — contenido enriquecido:** los campos `description` guardan HTML
  (antes TinyMCE/summernote). Para mantener pureza antd se creó
  `Components/HtmlEditor.jsx`: TextArea de HTML + tab de **vista previa**
  renderizada. Si el cliente pide WYSIWYG real, evaluar integración aparte.

### Gestión de accesos

- **Usuarios** (`/admin/role-user`) → `Pages/Admin/Users/Index.jsx` con **tabla
  server-side** (endpoint JSON `GET /admin/role-user-data`, contrato
  `useDataTable`): búsqueda con debounce, filtro por estación, orden por
  id/nombre/correo/fecha, paginación server. Toggle de aprobación con `Switch`
  (POST + refresh), dropdown de acciones (detalle, editar, permisos directos,
  estadísticas —Blade—, eliminar). Export Excel se conserva (PhpSpreadsheet).
  - `Pages/Admin/Users/Form.jsx`: crear/editar (rol, aprobado, tabla de precios
    + estación condicional — replica `applyPriceTableSettings`).
  - `Pages/Admin/Users/Show.jsx`: detalle con permisos agrupados (directos en
    amarillo, por rol en azul).
  - `Pages/Admin/Users/Permissions.jsx`: permisos directos con los heredados
    del rol deshabilitados.
  - `UserManagementService` concentra tableData/create/update/delete/toggle/
    permisos directos + protecciones de Super Admin.
- **Usuarios inactivos** (`/admin/inactive-users`) →
  `Pages/Admin/InactiveUsers/Index.jsx`: filtro de días reactivo (partial reload
  `only: ['users','days']`), búsqueda, selección múltiple y **eliminación masiva
  con confirmación por contraseña** (`InactiveUserDestroyRequest` se conserva).
  `InactiveUserService` (query compartida con el export Excel).
- **Roles y permisos** (`/admin/role`) → `Pages/Admin/Roles/Index.jsx` (tags de
  permisos con overflow "+N más") y `Pages/Admin/Roles/Form.jsx` (checkboxes
  agrupados por `group_name` con "Seleccionar todo" por grupo e indeterminate).
  `RoleService` con protección del rol Super Admin.
  **Nuevos FormRequests:** `RoleStoreRequest`, `RoleUpdateRequest` (antes inline).

### Cambios de infraestructura en esta fase

- `useDataTable`: nuevo `setFilter(column, value)` para filtros externos a la
  tabla y fix de serialización de filtros numéricos.
- Navegación: "Páginas" colapsó a un solo item; Proveedores y Gestión de
  accesos marcados `inertia => true` (SPA).
- Los `destroy`/`toggle` que devolvían JSON ahora devuelven redirect + flash
  (patrón Inertia); los exports Excel siguen siendo descargas normales.

---

## ✅ Fase 4 — Footer, Estadísticas, Menús, Ajustes, Perfil y Auth del admin (completada)

### Footer (`/admin/footer-info`)
`Pages/Admin/FooterInfo/Index.jsx` (form simple) + `FooterInfoService`. El grupo
del sidebar colapsó a un solo item.

### Ajustes (`/admin/settings`) — tabs laterales
`Pages/Admin/Settings/Index.jsx` con tabs (General, Logo y favicon, Apariencia
con `ColorPicker` de antd). `SettingUpdateService` + **nuevos FormRequests**
(`GeneralSettingsUpdateRequest`, `LogoSettingsUpdateRequest`,
`AppearanceSettingsUpdateRequest` — antes validaban inline).
**Fix:** el controlador leía `old_image` pero la vista enviaba `old_logo`, por
lo que el logo anterior nunca se preservaba/borraba; ahora usa
`old_logo`/`old_favicon` de forma consistente.

### Perfil (`/admin/profile`) — tabs Información / Contraseña
`Pages/Admin/Profile/Index.jsx` + `ProfileService`. **Nuevo
`ProfilePasswordUpdateRequest`** (antes inline). Avatar/banner con preview.

### Auth del admin (`/admin/login`, `/admin/forgot-password`)
`Pages/Auth/AdminLogin.jsx` y `AdminForgotPassword.jsx` con **`AuthLayout`**
propio (centrado, sin AdminLayout — por eso viven fuera de `Pages/Admin`).
**Decisión:** el submit es un **POST clásico** (helper
`Utils/classicFormPost.js`, también usado por el logout) porque el redirect
post-login puede caer en páginas Blade del frontend para usuarios no-admin, y
una visita Inertia no puede renderizarlas. Los errores de validación llegan
igualmente vía la prop compartida `errors`. Se replica el campo oculto
`honeypot` que exige el middleware anti-bots, y `flash.status` (mensaje de
Breeze) ahora se comparte vía Inertia.

### Menús (`/admin/menu-builder`) — gestor nativo antd
Se reemplazó el widget jQuery de `efectn/laravel-menu-builder` por un gestor
propio que escribe **las mismas tablas** (`admin_menus`/`admin_menu_items`),
así el frontend público (`Menu::getByName`) sigue funcionando sin cambios:
- Modelos `AdminMenu` y `AdminMenuItem` (tabla vía `config('menu.*')`).
- `MenuBuilderService`: árbol aplanado en orden de despliegue, crear/eliminar
  menú, CRUD de items con `depth`/`sort` automáticos, reorden subir/bajar
  (swap de `sort` entre hermanos), hijos re-anclados al nivel raíz al eliminar.
- Rutas nuevas bajo `admin/menu-builder/*` (menus store/destroy, items
  store/update/destroy/move) + FormRequests `MenuStoreRequest`,
  `MenuItemStoreRequest`, `MenuItemUpdateRequest`.
- `Pages/Admin/Menus/Index.jsx`: selector de menú, tabla indentada por nivel,
  modal de item con `AutoComplete` de enlaces sugeridos (config
  `menu-builder.php`) y selector de padre.
- El paquete sigue instalado (sus rutas propias quedan sin uso); eliminarlo es
  parte de la fase de limpieza.

### Estadísticas (`/admin/statistics`)
- `StatisticsService` concentra TODAS las consultas (KPIs, desgloses, usuarios
  activos, detalle por usuario, sesiones, detalle de sesión, actividades).
- **Panel General** (`Pages/Admin/Statistics/Index.jsx`): `RangePicker` de
  fechas (KPIs se recargan con partial reload; las tablas reaccionan solas),
  4 KPI cards y **Tabs** con 5 tablas server-side: Usuarios más activos,
  Dispositivos, Navegadores, Países y Páginas — todas con búsqueda, orden,
  paginación inferior y botón de export Excel (endpoints de
  `StatisticsExportController`, que se conserva tal cual).
- El endpoint `statistics/data/{type}` ahora responde el contrato de
  `useDataTable` (`{data,total}`) y suma el tipo `active-users`.
- **Detalle de usuario** (`UserDetail.jsx`): KPIs + duración media de sesión,
  Line de visitas por día, Pie de tipos de actividad, páginas top, sesiones
  recientes y flujos de navegación; enlaces a Sesiones y Actividades.
- **Sesiones** (`Sessions.jsx`) y **Actividades** (`Activities.jsx`): tablas
  paginadas server-side vía props Inertia (page en query) con filtros de rango
  y tipo. **Detalle de sesión** (`SessionDetail.jsx`): `Descriptions` + tabs de
  visitas y actividades.

### Infra de esta fase
- `useDataTable` acepta `extraParams` (query params estáticos reactivos — usado
  para el rango de fechas del panel de estadísticas).
- Navegación: Footer y Estadísticas colapsaron a items únicos; Menús, Ajustes,
  Estadísticas, Footer con `inertia => true`. Dropdown de usuario (Perfil,
  Configuración) y accesos rápidos del dashboard ahora navegan como SPA.
- **Todo el panel de administración quedó migrado a Inertia + antd**, excepto
  las sub-pantallas de horarios/servicios de proveedores y la importación de
  usuarios (siguen en Blade).

---

## ✅ Fase 5 — Pendientes finales y limpieza de legacy (completada)

### Horarios del proveedor (`/admin/listing-schedule/{id}`)
`Pages/Admin/Listings/Schedules.jsx`: tabla ordenada por día de la semana +
modal crear/editar (día desde `config('listing-schedule.days')`, horas como
texto libre para respetar el formato ya guardado). `ListingScheduleService` +
**nuevos** `ListingScheduleStoreRequest`/`ListingScheduleUpdateRequest` con
`Rule::in` de días (reemplazan al `ListingScheduleStoreReqeust` con typo, que
se eliminó; el controlador frontend de agentes se actualizó a los nuevos).

### Servicios del proveedor (`/admin/listing/{id}/amenities`)
`Pages/Admin/Listings/Amenities.jsx` con **`Transfer`** de antd
(disponibles/asignados + búsqueda) y modal "Nuevo servicio" que crea y asigna.
`ListingAmenityService` + `ListingAmenitiesUpdateRequest` y
`ListingAmenityCreateRequest`. Los endpoints JSON `add`/`remove` por item se
eliminaron (el Transfer sincroniza todo con un solo PUT).

### Catálogo de Servicios (`/admin/amenity`) — módulo rescatado
El CRUD de amenities existía en Blade pero **no estaba en el sidebar**
(inaccesible). Se migró con el patrón tabla+modal (`Pages/Admin/Amenities/`),
`AmenityService`, y se agregó al menú bajo Proveedores → Servicios.
`AmenityUpdateReqeust` (typo) → `AmenityUpdateRequest`.

### Blog (`/admin/blog`) — módulo rescatado
También estaba en Blade y fuera del sidebar. Migrado: `Pages/Admin/Blogs/Index`
(tabla con filtros) y `Form` (imagen, categoría, contenido con `HtmlEditor`,
popular/activo). `BlogService`, **nuevo modelo `BlogCategory`** (la tabla
existía sin modelo; el blade de crear ni siquiera recibía `$categories`).
Item "Blog" agregado al sidebar con permiso `blog index`.

### Importación de usuarios (`/admin/user-import`)
`Pages/Admin/Users/Import.jsx` (Dragger de Excel + instrucciones + descarga de
layout) e `ImportResult.jsx` (reporte con contraseñas + descarga TXT).
La lógica de parseo/creación/reporte se movió a `UserImportService`; nuevo
`UserImportRequest`. Botón "Importar usuarios" agregado al índice de Usuarios
(antes la pantalla no tenía enlace de entrada).

### Limpieza de legacy ejecutada

- 🗑️ `resources/views/admin/**` completo y `resources/views/vendor/menu-builder/**`.
- 🗑️ `public/admin/**` (~31 MB de assets Stisla/Bootstrap/jQuery/FontAwesome del admin).
- 🗑️ Todos los DataTables del admin (`OurFeature|Category|Location|Listing|
  ListingSchedule|Amenity|Blog|RoleUser|InactiveUser|RolePermission|
  ActiveUsers|Statistics/*`). **Se conservan** `AgentListingDataTable` y
  `AgentListingScheduleDataTable` porque los usa el dashboard de agentes del
  frontend (Blade) — por eso `yajra/laravel-datatables` sigue en composer.
- 🗑️ `TinyMCEController` + ruta `admin/upload-image` + `resources/js/tinymce-init.js`.
- 🗑️ npm: `laravel-datatables-vite` desinstalado.
- ➕ Migración de regularización `add_type_and_active_to_heroes_table`
  (con guardas `hasColumn`, no afecta entornos que ya tienen las columnas).
- La query base de "usuarios más activos" se movió del DataTable a
  `StatisticsService::activeUsersBaseQuery` (compartida con el export Excel).

---

## ⚠️ Deudas / notas técnicas

- **`composer install` pendiente en Docker** (el entorno del agente no alcanza
  Packagist). Sin eso la app no levanta (el middleware Inertia está registrado).
- ~~La tabla `heroes` usa columnas `type` y `active` sin migración~~ →
  resuelto en Fase 5 con migración de regularización guardada.
- Los **iconos de "Nuestras funciones" se guardan como clase FontAwesome**
  (ej. `fas fa-star`) porque el sitio público los renderiza así. En el admin se
  editan como texto plano. Si algún día se migra el sitio público, cambiar a un
  picker.
- ~~Blades y DataTables del admin obsoletos~~ → **eliminados en Fase 5**.
- `yajra/laravel-datatables` y `efectn/laravel-menu-builder` siguen en
  composer: el primero por los DataTables del dashboard de agentes (frontend
  Blade) y el segundo porque el navbar del sitio público usa
  `Menu::getByName`. Podrán retirarse cuando se migre el frontend público.
- `resources/js/app.js` + `resources/css/app.css` (Alpine/Tailwind) no los
  carga ninguna vista actualmente (el frontend Blade usa assets estáticos);
  se conservan por si el frontend los retoma. Candidatos a revisión.
- El chunk del dashboard pesa ~1.5 MB minificado por `@ant-design/plots`
  (se carga lazy solo en esa página). Optimizable con `manualChunks` si estorba.
- `section_titles` tiene pares `our_our_pricing_*` y `our_testimonial_*` que la
  pantalla no gestiona (igual que la vista Blade original).

---

## 🗺️ Pendientes de migración (actualizar al completar)

- [x] Layout principal + infraestructura Inertia/antd
- [x] Dashboard
- [x] Secciones (Banner, Banner público, Nuestras funciones, Títulos) → tabs
- [x] Proveedores: Categorías, Ubicaciones, Todos los Proveedores
- [ ] Proveedores: sub-pantallas de Horarios (listing-schedule) y Servicios (listing amenities) — siguen en Blade
- [x] Páginas: Sobre nosotros, Contacto, Política de privacidad, Términos → pantalla unificada con tabs
- [x] Gestionar Footer
- [x] Gestión de accesos: Usuarios (tabla server-side), Usuarios inactivos, Roles y permisos, Permisos directos
- [x] Gestión de accesos: Importación de usuarios
- [x] Estadísticas (Panel General + detalle de usuario/sesiones/actividades)
- [x] Menús (gestor nativo antd sobre las tablas del paquete)
- [x] Ajustes
- [x] Perfil
- [x] Login/Forgot password del admin
- [x] Proveedores: Horarios y Servicios por proveedor
- [x] Catálogo de Servicios (amenities) y Blog — módulos rescatados que estaban fuera del sidebar
- [x] Fase de limpieza: blades/datatables/assets del admin eliminados

**🎉 El panel de administración está 100% migrado a Inertia + React + Ant Design.**
Pendiente futuro (fuera del alcance actual): migrar el sitio público/frontend
(sigue en Blade con sus propios assets) y, con ello, retirar `yajra` y `efectn`.

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
