<?php

namespace App\Http\Middleware;

use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next, string $role): Response
    {
        AuthorizationService::requireRole($role);

        return $next($request);
    }
}
