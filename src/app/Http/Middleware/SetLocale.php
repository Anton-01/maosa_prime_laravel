<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** Idiomas que el selector puede activar. */
    public const SUPPORTED = ['es', 'en'];

    /** Clave de sesión donde vive la preferencia del usuario. */
    public const SESSION_KEY = 'locale';

    /**
     * Aplica el idioma elegido en el selector. Sin preferencia guardada se
     * usa el de config/app.php, así que el comportamiento por defecto no
     * cambia para quien nunca lo toca.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get(self::SESSION_KEY);

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
