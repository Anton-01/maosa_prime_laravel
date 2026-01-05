<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{

    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response{
        if ($request->has('honeypot') && ! empty($request->input('honeypot'))) {
            abort(403, '¡Acceso Denegado! Parece que eres un robot.');
        }
        return $next($request);
    }
}
