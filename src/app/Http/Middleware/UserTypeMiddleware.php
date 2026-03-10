<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $userType): Response {
        $user = $request->user();

        if ($userType === 'admin') {
            if ($user->hasRole('Super Admin')) {
                return $next($request);
            }
            return to_route('user.dashboard');
        }

        if ($userType === 'user') {
            if ($user->hasRole('User') || $user->user_type === $userType) {
                return $next($request);
            }
            if ($user->hasRole('Super Admin')) {
                return to_route('admin.dashboard.index');
            }
        }

        // 3. Fallback de seguridad
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
