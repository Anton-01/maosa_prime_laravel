<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombres legibles de las páginas
    |--------------------------------------------------------------------------
    |
    | `PageVisit::page_title` se resuelve a partir del nombre de la ruta
    | visitada. Sin esta traducción la trazabilidad sólo guarda URLs, que se
    | fragmentan por query string y no se pueden agrupar para contar visitas
    | por módulo. Las rutas que no aparezcan aquí se registran sin título.
    |
    */
    'page_titles' => [
        'home' => 'Inicio',
        'start' => 'Inicio',
        'listings' => 'Proveedores',
        'listing.show' => 'Detalle de proveedor',
        'blog.show' => 'Seminario',
        'about.index' => 'Acerca de',
        'contact.index' => 'Contacto',
        'user.dashboard' => 'Panel del usuario',
        'user.profile.index' => 'Mi perfil',
        'user.price-table.index' => 'Precios Internacionales',
        'user.precio-pemex.index' => 'Precios PEMEX',
    ],

];
