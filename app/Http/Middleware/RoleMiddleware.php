<?php

namespace App\Http\Middleware;

use App\Facades\ApiResponse;
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
        if (!auth()->user()->hasRole($role)) {
            return ApiResponse::unauthorized();
        }

        return $next($request);

    }
}
