<?php

namespace App\Http\Middleware;

use Closure;

use \Spatie\Permission\Middleware\RoleMiddleware as BaseRoleMiddleware;

class RoleMiddleware extends BaseRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role, $guard = null)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!auth()->user()->hasRole($role)) {
            return apiResponse(null,'You do not have the required role.',\Illuminate\Http\Response::HTTP_FORBIDDEN);
        }

        return $next($request);

    }
}
