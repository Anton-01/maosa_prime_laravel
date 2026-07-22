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
        $isAdminArea = $request->routeIs('admin.*');

        return [
            ...parent::share($request),
            'appName' => config('settings.site_name', config('app.name', 'Maosa Prime')),
            'settings' => $this->buildPublicSettings(),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                    'user_type' => $user->user_type,
                    'can_view_price_table' => $user->canViewPriceTable(),
                    'is_admin' => $user->user_type === 'admin',
                ] : null,
            ],
            'navigation' => ($user && $isAdminArea) ? $this->buildAdminNavigation($request) : [],
            // Public navbar/footer menus are only needed outside the admin area.
            'siteMenu' => $isAdminArea ? null : fn () => $this->buildSiteMenu(),
            'footerInfo' => $isAdminArea ? null : fn () => $this->buildFooterInfo(),
            'urls' => [
                'dashboard' => route('admin.dashboard.index'),
                'profile' => route('admin.profile'),
                'settings' => route('admin.settings.index'),
                'logout' => route('logout'),
                'home' => url('/'),
                'login' => route('login'),
                'forgotPassword' => route('password.request'),
                'userDashboard' => route('user.dashboard'),
                'userProfile' => route('user.profile.index'),
                'priceTable' => route('user.price-table.index'),
                'listings' => route('listings'),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
                'status' => $request->session()->get('status'),
            ],
        ];
    }

    /**
     * Public branding/settings consumed by the React layouts.
     *
     * @return array<string, mixed>
     */
    protected function buildPublicSettings(): array
    {
        return [
            'siteName' => config('settings.site_name', config('app.name', 'Maosa Prime')),
            'logo' => config('settings.logo'),
            'favicon' => config('settings.favicon'),
            'email' => config('settings.site_email'),
            'phone' => config('settings.site_phone'),
            'color' => config('settings.site_default_color') ?: '#6777ef',
            'timezone' => config('settings.site_timezone') ?: config('app.timezone', 'America/Mexico_City'),
        ];
    }

    /**
     * Public site menus (navbar + footer columns) sourced from the same
     * menu-builder tables used by the legacy Blade navbar.
     *
     * @return array<string, mixed>
     */
    protected function buildSiteMenu(): array
    {
        $byName = function (string $name): array {
            try {
                return collect(\Menu::getByName($name))->map(function ($item) {
                    return [
                        'label' => $item['label'] ?? '',
                        'link' => $item['link'] ?? '#',
                        'children' => collect($item['child'] ?? [])->map(fn ($child) => [
                            'label' => $child['label'] ?? '',
                            'link' => $child['link'] ?? '#',
                        ])->values()->all(),
                    ];
                })->values()->all();
            } catch (\Throwable $e) {
                return [];
            }
        };

        return [
            'main' => $byName('Main Menu'),
            'footerOne' => $byName('Footer Menu One'),
            'footerTwo' => $byName('Footer Menu Two'),
            'footerThree' => $byName('Footer Menu Three'),
        ];
    }

    /**
     * Footer contact block.
     *
     * @return array<string, mixed>|null
     */
    protected function buildFooterInfo(): ?array
    {
        try {
            $info = \App\Models\FooterInfo::first();
        } catch (\Throwable $e) {
            return null;
        }

        if (! $info) {
            return null;
        }

        return [
            'shortDescription' => $info->short_description,
            'address' => $info->address,
            'email' => $info->email,
            'phone' => $info->phone,
            'copyright' => $info->copyright,
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
                        'inertia' => true,
                        'active' => $request->routeIs('admin.category.*'),
                    ],
                    [
                        'key' => 'locations',
                        'label' => 'Ubicaciones',
                        'url' => route('admin.location.index'),
                        'inertia' => true,
                        'active' => $request->routeIs('admin.location.*'),
                    ],
                    [
                        'key' => 'amenities',
                        'label' => 'Servicios',
                        'url' => route('admin.amenity.index'),
                        'inertia' => true,
                        'active' => $request->routeIs('admin.amenity.*'),
                    ],
                    [
                        'key' => 'listings',
                        'label' => 'Todos los Proveedores',
                        'url' => route('admin.listing.index'),
                        'inertia' => true,
                        'active' => $request->routeIs('admin.listing.*'),
                    ],
                ],
            ],
            [
                'key' => 'blog',
                'label' => 'Seminarios',
                'icon' => 'blog',
                'permission' => 'blog index',
                'url' => route('admin.blog.index'),
                'inertia' => true,
                'active' => $request->routeIs('admin.blog.*'),
            ],
            [
                'key' => 'pages',
                'label' => 'Páginas',
                'icon' => 'pages',
                'permission' => 'access management pages',
                'url' => route('admin.pages.index'),
                'inertia' => true,
                'active' => $request->routeIs(
                    'admin.pages.index',
                    'admin.about-us.index',
                    'admin.contact.index',
                    'admin.privacy-policy.index',
                    'admin.terms-and-condition.index',
                ),
            ],
            [
                'key' => 'footer',
                'label' => 'Gestionar Footer',
                'icon' => 'footer',
                'permission' => 'access management footer',
                'url' => route('admin.footer-info.index'),
                'inertia' => true,
                'active' => $request->routeIs('admin.footer-info.index'),
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
                        'inertia' => true,
                        'active' => $request->routeIs('admin.role-user.*', 'admin.user-permissions.*'),
                    ],
                    [
                        'key' => 'inactive-users',
                        'label' => 'Usuarios inactivos',
                        'url' => route('admin.inactive-users.index'),
                        'inertia' => true,
                        'active' => $request->routeIs('admin.inactive-users.*'),
                    ],
                    [
                        'key' => 'roles',
                        'label' => 'Roles y permisos',
                        'url' => route('admin.role.index'),
                        'inertia' => true,
                        'active' => $request->routeIs('admin.role.*'),
                    ],
                ],
            ],
            [
                'key' => 'statistics',
                'label' => 'Estadísticas',
                'icon' => 'statistics',
                'permission' => 'access management statics users',
                'url' => route('admin.statistics.index'),
                'inertia' => true,
                'active' => $request->routeIs('admin.statistics.*'),
            ],
            [
                'key' => 'menu-builder',
                'label' => 'Menús',
                'icon' => 'menus',
                'permission' => 'access management menu builder',
                'url' => route('admin.menu-builder.index'),
                'inertia' => true,
                'active' => $request->routeIs('admin.menu-builder.*'),
            ],
            [
                'key' => 'settings',
                'label' => 'Ajustes',
                'icon' => 'settings',
                'permission' => 'access management settings maosa',
                'url' => route('admin.settings.index'),
                'inertia' => true,
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
