<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur a le rôle SUPER_ADMIN
        if (!$request->user()->hasRole('SUPER_ADMIN')) {
            abort(403, 'Accès réservé au Super Administrateur.');
        }

        return $next($request);
    }
}
