<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default with every Inertia response.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'appName' => config('settings.site_name', config('app.name', 'Maosa Prime')),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                ] : null,
            ],
            'navigation' => $user ? $this->buildAdminNavigation($request) : [],
            'urls' => [
                'dashboard' => route('admin.dashboard.index'),
                'profile' => route('admin.profile'),
                'settings' => route('admin.settings.index'),
                'logout' => route('logout'),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }

    /**
     * Build the admin sidebar navigation tree, filtered by the
     * authenticated user's permissions. Mirrors admin/layouts/sidebar.blade.php.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildAdminNavigation(Request $request): array
    {
        $user = $request->user();

        $items = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'url' => route('admin.dashboard.index'),
                'inertia' => true,
                'active' => $request->routeIs('admin.dashboard.index'),
            ],
            [
                'key' => 'sections',
                'label' => 'Secciones',
                'icon' => 'sections',
                'permission' => 'access management sections content',
                'url' => route('admin.sections.index'),
                'inertia' => true,
                'active' => $request->routeIs(
                    'admin.sections.index',
                    'admin.hero.index',
                    'admin.hero-public.index',
                    'admin.our-features.*',
                    'admin.section-title.index',
                ),
            ],
            [
                'key' => 'suppliers',
                'label' => 'Proveedores',
                'icon' => 'suppliers',
                'permission' => 'access management suppliers',
                'children' => [
                    [
                        'key' => 'categories',
                        'label' => 'Categorías',
                        'url' => route('admin.category.index'),
                        'active' => $request->routeIs('admin.category.*'),
                    ],
                    [
                        'key' => 'locations',
                        'label' => 'Ubicaciones',
                        'url' => route('admin.location.index'),
                        'active' => $request->routeIs('admin.location.*'),
                    ],
                    [
                        'key' => 'listings',
                        'label' => 'Todos los Proveedores',
                        'url' => route('admin.listing.index'),
                        'active' => $request->routeIs('admin.listing.*'),
                    ],
                ],
            ],
            [
                'key' => 'pages',
                'label' => 'Páginas',
                'icon' => 'pages',
                'permission' => 'access management pages',
                'children' => [
                    [
                        'key' => 'about-us',
                        'label' => 'Sobre nosotros',
                        'url' => route('admin.about-us.index'),
                        'active' => $request->routeIs('admin.about-us.index'),
                    ],
                    [
                        'key' => 'contact',
                        'label' => 'Contacto',
                        'url' => route('admin.contact.index'),
                        'active' => $request->routeIs('admin.contact.index'),
                    ],
                    [
                        'key' => 'privacy-policy',
                        'label' => 'Política de privacidad',
                        'url' => route('admin.privacy-policy.index'),
                        'active' => $request->routeIs('admin.privacy-policy.index'),
                    ],
                    [
                        'key' => 'terms-and-conditions',
                        'label' => 'Términos y condiciones',
                        'url' => route('admin.terms-and-condition.index'),
                        'active' => $request->routeIs('admin.terms-and-condition.index'),
                    ],
                ],
            ],
            [
                'key' => 'footer',
                'label' => 'Gestionar Footer',
                'icon' => 'footer',
                'permission' => 'access management footer',
                'children' => [
                    [
                        'key' => 'footer-info',
                        'label' => 'Footer Info',
                        'url' => route('admin.footer-info.index'),
                        'active' => $request->routeIs('admin.footer-info.index'),
                    ],
                ],
            ],
            [
                'key' => 'access-management',
                'label' => 'Gestión de accesos',
                'icon' => 'access',
                'permission' => 'access management users',
                'children' => [
                    [
                        'key' => 'users',
                        'label' => 'Usuarios',
                        'url' => route('admin.role-user.index'),
                        'active' => $request->routeIs('admin.role-user.*'),
                    ],
                    [
                        'key' => 'inactive-users',
                        'label' => 'Usuarios inactivos',
                        'url' => route('admin.inactive-users.index'),
                        'active' => $request->routeIs('admin.inactive-users.*'),
                    ],
                    [
                        'key' => 'roles',
                        'label' => 'Roles y permisos',
                        'url' => route('admin.role.index'),
                        'active' => $request->routeIs('admin.role.*'),
                    ],
                ],
            ],
            [
                'key' => 'statistics',
                'label' => 'Estadísticas',
                'icon' => 'statistics',
                'permission' => 'access management statics users',
                'children' => [
                    [
                        'key' => 'statistics-panel',
                        'label' => 'Panel General',
                        'url' => route('admin.statistics.index'),
                        'active' => $request->routeIs('admin.statistics.*'),
                    ],
                ],
            ],
            [
                'key' => 'menu-builder',
                'label' => 'Menús',
                'icon' => 'menus',
                'permission' => 'access management menu builder',
                'url' => route('admin.menu-builder.index'),
                'active' => $request->routeIs('admin.menu-builder.index'),
            ],
            [
                'key' => 'settings',
                'label' => 'Ajustes',
                'icon' => 'settings',
                'permission' => 'access management settings maosa',
                'url' => route('admin.settings.index'),
                'active' => $request->routeIs('admin.settings.index'),
            ],
        ];

        return collect($items)
            ->filter(fn (array $item) => ! isset($item['permission']) || $user->can($item['permission']))
            ->map(function (array $item) {
                unset($item['permission']);

                if (isset($item['children'])) {
                    $item['active'] = collect($item['children'])->contains('active', true);
                }

                return $item;
            })
            ->values()
            ->all();
    }
}
