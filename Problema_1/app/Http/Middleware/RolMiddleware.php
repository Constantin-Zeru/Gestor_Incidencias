<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware de control por rol
 */
class RolMiddleware
{
    public function handle($request, Closure $next, $rol)
    {
        if (!Auth::check() || Auth::user()->tipo !== $rol) {
            abort(403);
        }

        return $next($request);
    }
}
