<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur a un des rôles autorisés.
     * Usage : middleware('role:admin') ou middleware('role:admin,seller')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role->value, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
