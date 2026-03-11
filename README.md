# MAOSA Prime

<p align="center">
  <img src="src/public/uploads/media_6851d26160ca7.png" alt="MAOSA Prime Logo" width="200">
</p>

<p align="center">
  <strong>Plataforma B2B de Directorio de Proveedores</strong><br>
  Gestión integral de listados, precios, usuarios y contenido para negocios.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

---

## Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Módulos Principales](#módulos-principales)
- [Base de Datos](#base-de-datos)
- [API y Rutas](#api-y-rutas)
- [Roles y Permisos](#roles-y-permisos)
- [Pruebas](#pruebas)
- [Despliegue](#despliegue)
- [Contribución](#contribución)
- [Licencia](#licencia)

---

## Descripción

**MAOSA Prime** es una plataforma web B2B completa construida con Laravel 11, diseñada para gestionar directorios de proveedores, listas de precios personalizadas y contenido empresarial. Permite a usuarios registrados administrar sus listados de negocios, mientras que los administradores tienen control total sobre usuarios, contenido, precios y analíticas del sitio.

La aplicación está orientada principalmente al mercado hispanohablante (locale primario: Español).

---

## Características

### Para Usuarios Finales
- **Directorio de Proveedores**: Exploración y búsqueda avanzada de listados por categoría, ubicación y amenidades
- **Perfil de Proveedor**: Páginas detalladas con galería de imágenes/videos, horarios, redes sociales y mapa
- **Lista de Precios**: Acceso a tablas de precios personalizadas con exportación a PDF
- **Dashboard Personal**: Gestión de listados propios, perfil y sucursales
- **Blog / Información**: Acceso a artículos y noticias del sector
- **Páginas Estáticas**: About Us, Contacto, Política de Privacidad, Términos y Condiciones

### Para Administradores
- **Panel de Administración** completo con gestión de:
    - Proveedores/Listados (verificación, aprobación, destacados)
    - Categorías, Ubicaciones y Amenidades
    - Usuarios, Roles y Permisos (RBAC)
    - Blog y Categorías de Blog
    - Secciones Hero (públicas y privadas)
    - Terminales de Combustible
    - Precios por usuario (leyendas, importación masiva)
    - Configuración general, logo y apariencia
    - Menú del sitio (constructor de menús)
    - Redes sociales, Footer, Títulos de secciones
- **Gestión de Usuarios**: Flujo de aprobación, importación/exportación, estadísticas
- **Analíticas**: Seguimiento de sesiones, actividades, visitas por página y flujos de navegación
- **Notificaciones por Email**: Aprobación de cuentas, formulario de contacto

### Técnicas
- Control de acceso basado en roles (RBAC) con Spatie Laravel Permission
- Generación de PDFs con DomPDF
- DataTables con renderizado del lado del servidor (Yajra)
- Editor de texto enriquecido TinyMCE
- Componentes reactivos con Livewire y Alpine.js
- Soporte de eventos en tiempo real con Laravel Echo + Pusher
- Sistema de caché con Redis
- Protección anti-spam con Honeypot
- Sanitización de HTML con Mews Purifier
- Autenticación con Laravel Breeze + Sanctum

---

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Backend** | PHP 8.1+, Laravel 11.0 |
| **Frontend** | Tailwind CSS 3.x, Alpine.js 3.x, Bootstrap 5 |
| **Build Tool** | Vite 4.x |
| **Base de Datos** | MySQL 8.x |
| **Caché / Cola** | Redis (Predis), Laravel Queue |
| **Autenticación** | Laravel Breeze, Laravel Sanctum |
| **RBAC** | Spatie Laravel Permission 6.x |
| **PDF** | Barryvdh Laravel DomPDF 3.x |
| **DataTables** | Yajra Laravel DataTables 11.x |
| **Editor** | TinyMCE |
| **Tiempo Real** | Laravel Echo, Pusher.js |
| **Testing** | Pest PHP 2.x |
| **Pago** | Razorpay 2.x |
| **Menú** | efectn/laravel-menu-builder |
| **HTTP Client** | Guzzle 7.x |

---

## Requisitos del Sistema

- **PHP**: >= 8.1
- **Composer**: >= 2.x
- **Node.js**: >= 18.x
- **NPM**: >= 9.x
- **MySQL**: >= 8.0
- **Redis**: >= 6.x (opcional, para caché y colas)
- **Extensiones PHP**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> maosa-prime
cd maosa-prime/src
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias Node.js

```bash
npm install
```

### 4. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar base de datos

Editar el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maosa_prime
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 7. Configurar almacenamiento

```bash
php artisan storage:link
```

### 8. Compilar assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 9. Iniciar el servidor de desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Configuración

### Variables de entorno importantes

```env
# Aplicación
APP_NAME="MAOSA Prime"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maosa_prime
DB_USERNAME=root
DB_PASSWORD=

# Correo electrónico
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@example.com
MAIL_PASSWORD=tu_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="MAOSA Prime"

# Redis (opcional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher (tiempo real, opcional)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

# Razorpay (pagos, opcional)
RAZORPAY_KEY=
RAZORPAY_SECRET=
```

### Configuración del locale

La aplicación usa **Español** como idioma principal. Para cambiar el idioma, editar `config/app.php`:

```php
'locale' => 'es',
'fallback_locale' => 'en',
```

---

## Estructura del Proyecto

```
src/
├── app/
│   ├── DataTables/          # 15 clases DataTable (Yajra)
│   ├── Events/              # Eventos de la aplicación
│   ├── Helpers/             # Funciones helpers globales
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/       # 33 controladores de administración
│   │   │   ├── Auth/        # 8 controladores de autenticación
│   │   │   └── Frontend/    # 6 controladores frontend
│   │   ├── Middleware/      # 12 middlewares personalizados
│   │   └── Requests/        # 20+ clases de validación
│   ├── Listeners/           # Listeners de eventos
│   ├── Livewire/            # Componentes Livewire
│   ├── Mail/                # Clases de correo
│   ├── Models/              # 29 modelos Eloquent
│   ├── Providers/           # 5 service providers
│   ├── Services/            # Servicios de negocio
│   └── Traits/              # Traits reutilizables
├── config/                  # 25 archivos de configuración
├── database/
│   ├── factories/           # Factories para testing
│   ├── migrations/          # 36 migraciones
│   └── seeders/             # 7 seeders
├── lang/
│   ├── en/                  # Traducciones en inglés
│   └── es/                  # Traducciones en español
├── public/
│   ├── admin/               # Assets del panel admin
│   └── frontend/            # Assets del frontend
├── resources/
│   ├── css/                 # Punto de entrada Tailwind CSS
│   ├── js/                  # Alpine.js, Bootstrap, TinyMCE
│   └── views/
│       ├── admin/           # 90+ plantillas del admin
│       ├── auth/            # Vistas de autenticación
│       ├── components/      # Componentes reutilizables
│       ├── frontend/        # Vistas del sitio público
│       ├── mail/            # Plantillas de email
│       └── vendor/          # Vistas de paquetes
├── routes/
│   ├── admin.php            # Rutas del panel admin
│   ├── api.php              # Rutas API
│   ├── auth.php             # Rutas de autenticación
│   ├── console.php          # Comandos de consola
│   └── web.php              # Rutas web frontend
└── tests/
    ├── Feature/             # Tests de integración (Pest)
    └── Unit/                # Tests unitarios (Pest)
```

---

## Módulos Principales

### Módulo de Proveedores (Listings)

Los listados son el núcleo del directorio. Cada proveedor puede tener:
- Información general (nombre, descripción, imagen, thumbnail)
- Categoría y ubicación geográfica
- Amenidades y servicios
- Galería de imágenes y videos
- Horarios de atención por día
- Redes sociales vinculadas
- Estado de verificación y aprobación del administrador
- Marca de "destacado"

```
app/Models/Listing.php
app/Http/Controllers/Admin/ListingController.php
app/Http/Controllers/Frontend/ListingController.php
resources/views/frontend/supplier/
resources/views/admin/listing/
```

### Módulo de Precios

Sistema de precios personalizado por usuario:
- **Listas de precios**: Versiones con fecha por usuario
- **Ítems de precio**: Con precios de envío incluidos
- **Leyendas**: Columnas configurables y ordenables
- **Leyendas por defecto**: Plantillas del sistema
- **Importación masiva**: Carga de precios para múltiples usuarios

```
app/Models/UserPriceList.php
app/Models/UserPriceItem.php
app/Models/UserPriceLegend.php
resources/views/frontend/price-table/
resources/views/admin/pricing/
```

### Módulo de Analíticas

Seguimiento completo de comportamiento de usuarios:
- Sesiones de usuario
- Actividades por usuario
- Visitas por página
- Flujos de navegación

```
app/Services/UserTrackingService.php
app/Models/UserSession.php
app/Models/UserActivity.php
app/Models/PageVisit.php
app/Models/UserNavigationFlow.php
app/Http/Middleware/TrackUserActivity.php
```

### Módulo de Configuración

Gestión dinámica de la configuración del sitio:
- Nombre del sitio, logo, favicon
- Colores por defecto
- Información del footer
- Títulos de secciones
- Redes sociales del sitio

```
app/Services/SettingsService.php
app/Models/Setting.php
resources/views/admin/settings/
```

---

## Base de Datos

### Resumen de Tablas (36 migraciones)

| Grupo | Tablas |
|-------|--------|
| **Usuarios** | `users`, `user_branches`, `password_reset_tokens`, `personal_access_tokens` |
| **Proveedores** | `listings`, `listing_amenities`, `listing_schedules`, `listing_social_links`, `listing_image_galleries`, `listing_video_galleries` |
| **Taxonomía** | `categories`, `locations`, `amenities` |
| **Precios** | `user_price_lists`, `user_price_items`, `user_price_legends`, `default_price_legends`, `fuel_terminals` |
| **Blog** | `blogs`, `blog_categories`, `blog_comments` |
| **Contenido** | `heroes`, `hero_types`, `our_features`, `about_us`, `section_titles` |
| **Páginas** | `privacy_policies`, `terms_and_conditions`, `contacts`, `footer_infos` |
| **Social** | `social_networks`, `social_links` |
| **Analíticas** | `visits`, `page_visits`, `user_sessions`, `user_activities`, `user_navigation_flows` |
| **Configuración** | `settings` |
| **Menú** | `menus_wp`, `menu_items_wp` |
| **Permisos** | `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions` |

### Relaciones Principales

```
User
 ├── hasMany: UserBranch, UserPriceList, UserSession, UserActivity, PageVisit, UserNavigationFlow
 └── belongsToMany: roles, permissions (via Spatie)

Listing
 ├── belongsTo: Category, Location
 ├── hasMany: ListingSchedule, ListingImageGallery, ListingVideoGallery, ListingSocialLink
 └── belongsToMany: Amenity

UserPriceList
 ├── belongsTo: User
 ├── hasMany: UserPriceItem, UserPriceLegend
 └── belongsTo: FuelTerminal

Blog
 ├── belongsTo: BlogCategory, User (author)
 └── hasMany: BlogComment
```

---

## API y Rutas

### Rutas Frontend Principales

| Método | URI | Descripción |
|--------|-----|-------------|
| `GET` | `/` | Página principal |
| `GET` | `/suppliers` | Directorio de proveedores |
| `GET` | `/suppliers/{slug}` | Detalle de proveedor |
| `GET` | `/information/{slug}` | Artículo del blog |
| `GET` | `/about-us` | Página About Us |
| `GET` | `/contact` | Formulario de contacto |
| `GET/PUT` | `/user/profile` | Perfil del usuario |
| `GET` | `/user/price-table` | Lista de precios |
| `GET` | `/user/price-table/pdf` | Exportar PDF de precios |
| `RESOURCE` | `/user/listing` | CRUD de listados propios |

### Rutas de Autenticación

| Método | URI | Descripción |
|--------|-----|-------------|
| `GET/POST` | `/login` | Inicio de sesión |
| `POST` | `/logout` | Cerrar sesión |
| `GET/POST` | `/forgot-password` | Recuperar contraseña |
| `GET/POST` | `/reset-password` | Restablecer contraseña |

### Rutas Admin Principales

Todas las rutas admin están protegidas por los middlewares `auth` y `user.type:admin`.

| Método | URI | Descripción |
|--------|-----|-------------|
| `GET` | `/admin/dashboard` | Panel de control |
| `RESOURCE` | `/admin/listing` | Gestión de proveedores |
| `RESOURCE` | `/admin/category` | Categorías |
| `RESOURCE` | `/admin/location` | Ubicaciones |
| `RESOURCE` | `/admin/user` | Gestión de usuarios |
| `RESOURCE` | `/admin/role` | Roles |
| `RESOURCE` | `/admin/blog` | Blog |
| `RESOURCE` | `/admin/hero` | Secciones hero |
| `GET` | `/admin/statistics` | Estadísticas y analíticas |
| `RESOURCE` | `/admin/user-price` | Precios por usuario |
| `POST` | `/admin/price-import` | Importar precios |
| `GET/POST` | `/admin/settings/general` | Configuración general |

---

## Roles y Permisos

El sistema usa **Spatie Laravel Permission** para RBAC (Role-Based Access Control).

### Roles del Sistema

| Rol | Acceso |
|-----|--------|
| `admin` | Acceso total al panel de administración |
| `user` | Acceso al dashboard de usuario y sus propios recursos |

### Middleware de Tipo de Usuario

El middleware `UserTypeMiddleware` redirige automáticamente a los usuarios según su tipo:
- Tipo `admin` → Panel de administración (`/admin/dashboard`)
- Tipo `user` → Dashboard de usuario (`/user/dashboard`)

### Flujo de Aprobación

1. Usuario se registra en la plataforma
2. El sistema envía notificación al administrador
3. Admin aprueba o rechaza la cuenta
4. Usuario recibe email con el estado de su cuenta

---

## Pruebas

El proyecto usa **Pest PHP v2** como framework de testing.

```bash
# Ejecutar todos los tests
php artisan test

# Con cobertura de código
php artisan test --coverage

# Tests específicos
php artisan test --filter=NombreDelTest
```

### Configuración de Testing

El entorno de test usa:
- Cache: `array` driver
- Queue: `sync` driver
- Mail: `array` driver

Configurado en `phpunit.xml`.

---

## Despliegue

### Requisitos de Producción

1. Servidor web: Apache / Nginx con soporte PHP-FPM
2. PHP 8.1+ con extensiones requeridas
3. MySQL 8.x
4. Redis (recomendado para caché y colas)
5. SSL/TLS habilitado

### Pasos para Producción

```bash
# 1. Configurar entorno de producción
cp .env.example .env
# Editar .env con valores de producción (APP_ENV=production, APP_DEBUG=false)

# 2. Instalar dependencias (sin dev)
composer install --optimize-autoloader --no-dev

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Compilar assets para producción
npm ci && npm run build

# 5. Ejecutar migraciones
php artisan migrate --force

# 6. Optimizar la aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Configurar permisos de carpetas
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Enlazar almacenamiento
php artisan storage:link
```

### Configuración Nginx (ejemplo)

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /var/www/maosa-prime/src/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Seguridad

- **CSRF Protection**: Habilitado en todos los formularios
- **SQL Injection**: Prevenida mediante Eloquent ORM y Query Builder
- **XSS**: Sanitización con Mews Purifier y Blade escaping
- **Honeypot**: Protección anti-spam en formularios públicos
- **RBAC**: Control de acceso basado en roles con Spatie
- **Password Hashing**: Bcrypt via Laravel
- **Rate Limiting**: Configurado en rutas de autenticación
- **Verificación de Email**: Soporte habilitado

---

## Contribución

1. Fork el repositorio
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y haz commit: `git commit -m 'feat: agregar nueva funcionalidad'`
4. Push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

### Estilo de Código

El proyecto usa **Laravel Pint** para mantener estilo consistente:

```bash
./vendor/bin/pint
```

---

## Licencia

Este proyecto está licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  Desarrollado con ❤️ usando <a href="https://laravel.com">Laravel</a> &bull; &copy; 2024 MAOSA Prime
</p>
