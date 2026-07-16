# Migración Maosa Prime → Inertia.js + React + Ant Design

Guía de la infraestructura creada para el proceso de rediseño y las convenciones
que debe seguir cada pantalla migrada.

## ⚠️ Paso obligatorio antes de correr el proyecto

El paquete backend de Inertia ya está declarado en `composer.json`, pero debe
instalarse dentro del contenedor Docker (el entorno donde se preparó esta rama
no tiene acceso de red a Packagist):

```bash
docker compose exec <servicio-php> composer install
# o bien, para regenerar el lock:
docker compose exec <servicio-php> composer update inertiajs/inertia-laravel
```

En el frontend, `package.json` y `package-lock.json` ya incluyen todo; basta:

```bash
npm install && npm run build   # o npm run dev
```

> **Importante:** el middleware `HandleInertiaRequests` ya está registrado en el
> grupo `web` del Kernel, por lo que la app **no levantará** hasta que se ejecute
> `composer install` con el nuevo `composer.json`.

## Arquitectura instalada

| Pieza | Archivo | Rol |
|---|---|---|
| Root view de Inertia | `resources/views/app.blade.php` | Documento HTML único que monta React |
| Middleware compartido | `app/Http/Middleware/HandleInertiaRequests.php` | Comparte `auth.user`, `navigation` (filtrada por permisos Spatie), `flash`, `urls` y `appName` en todas las páginas |
| Bootstrap React | `resources/js/app.jsx` | `createInertiaApp` + `ConfigProvider` (locale `es_ES`, tema corporativo) + `<App />` de antd |
| **Layout Principal** | `resources/js/Layouts/AdminLayout.jsx` | `<Layout>` con `<Sider>` colapsable, `<Header>` con menú de usuario, `<Content>` y `<Footer>`. Se aplica automáticamente a toda página bajo `Pages/Admin/**` |
| Contenedor de página | `resources/js/Components/PageContainer.jsx` | Título de documento, breadcrumb, título de página, acciones y `<Card>` |
| Hook de tablas | `resources/js/Hooks/useDataTable.js` | Estado reactivo para `<Table />` contra endpoints JSON (Yajra) |

## Convenciones obligatorias por pantalla

1. **Solo Ant Design**: componentes de `antd` e iconos de `@ant-design/icons`.
   Nada de Bootstrap, FontAwesome, jQuery, DataTables clásico ni CSS externo.
2. **Código en inglés, UI en español**: variables, funciones, estados y props en
   inglés; todo texto visible para el usuario en español.
3. **Tablas**: `<Table />` de antd con paginación **siempre en la parte inferior**
   (`pagination={{ position: ['bottomRight'], ... }}`), ordenamiento y filtros
   reactivos, y `loading` controlado (eventos de Inertia o `useDataTable`).
4. **Formularios/secciones extensas**: agrupar con `<Tabs />` con nombres
   descriptivos en español.

## Cómo migrar una pantalla

### 1. Controlador

```php
use Inertia\Inertia;

public function index()
{
    return Inertia::render('Admin/Category/Index', [
        'categories' => Category::query()->latest()->get(['id', 'name', 'status']),
    ]);
}
```

### 2. Página React

Crear `resources/js/Pages/Admin/Category/Index.jsx`. El `AdminLayout` se hereda
automáticamente; la página solo se ocupa de su contenido:

```jsx
import React from 'react';
import { router } from '@inertiajs/react';
import { Button, Table } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

export default function Index({ categories }) {
    return (
        <PageContainer
            title="Categorías"
            breadcrumbItems={[{ title: 'Proveedores' }, { title: 'Categorías' }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={() => router.get('/admin/category/create')}>
                    Nueva categoría
                </Button>
            }
        >
            <Table
                rowKey="id"
                dataSource={categories}
                pagination={{ position: ['bottomRight'], showSizeChanger: true }}
                columns={[/* ... */]}
            />
        </PageContainer>
    );
}
```

### 3. Tablas pesadas con Yajra (server-side)

Para catálogos grandes o estadísticas, la página Inertia solo recibe metadatos y
la tabla consume un endpoint JSON. El hook `useDataTable` envía:

```
page, per_page, sort_field, sort_order (asc|desc), search, filters[columna]
```

y espera `{ "data": [...], "total": <int> }`. Patrón de controlador:

```php
public function data(Request $request)
{
    $query = User::query();

    if ($search = $request->string('search')->toString()) {
        $query->where(fn ($q) => $q
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%"));
    }

    if ($request->filled('sort_field')) {
        $query->orderBy($request->input('sort_field'), $request->input('sort_order', 'asc'));
    }

    $page = $query->paginate($request->integer('per_page', 10));

    return response()->json([
        'data' => $page->items(),
        'total' => $page->total(),
    ]);
}
```

(Con Yajra: aplicar los mismos parámetros y devolver la misma forma de JSON.)

En el frontend:

```jsx
const { data, total, loading, tableParams, handleTableChange, setSearch } = useDataTable(dataUrl);

<Table
    rowKey="id"
    dataSource={data}
    loading={loading}
    onChange={handleTableChange}
    pagination={{
        position: ['bottomRight'],
        current: tableParams.page,
        pageSize: tableParams.perPage,
        total,
        showSizeChanger: true,
        showTotal: (t) => `${t} registros`,
    }}
/>
```

## Navegación lateral

El menú se construye en el backend
(`HandleInertiaRequests::buildAdminNavigation`) replicando
`admin/layouts/sidebar.blade.php`, incluyendo los permisos `@can` de Spatie y el
estado activo por nombre de ruta. Al agregar una sección nueva al panel, se
agrega ahí (una sola fuente de verdad para Blade → React durante la transición).

## Convivencia con las vistas Blade

Las vistas clásicas siguen funcionando: `vite.config.js` conserva
`resources/js/app.js` + `resources/css/app.css` y solo agrega
`resources/js/app.jsx`. Cada pantalla se migra cambiando su controlador a
`Inertia::render(...)`; el resto del panel no se ve afectado.
