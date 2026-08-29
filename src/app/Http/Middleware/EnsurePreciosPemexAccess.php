<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * REQ-04: el submódulo "Precios PEMEX" sólo es accesible para usuarios con
 * el permiso `permiso_precios_pemex` activo. Se aplica tanto a la vista como
 * a los endpoints de layout, para que ocultar el menú no sea la única barrera.
 */
class EnsurePreciosPemexAccess
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->tienePermisoPreciosPemex()) {
            abort(403, 'No tiene permiso para consultar los precios PEMEX.');
        }

        return $next($request);
    }
}
