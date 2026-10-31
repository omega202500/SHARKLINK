<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->must_change_password) {
            // Autoriser l'accès uniquement aux routes de changement de mot de passe et de déconnexion
            if (!$request->routeIs('password.change') && !$request->routeIs('password.update') && !$request->routeIs('logout')) {
                return redirect()->route('password.change')->with('warning', 'Vous devez changer votre mot de passe temporaire avant de continuer.');
            }
        }

        return $next($request);
    }
}
