<?php

namespace App\Http\Middleware;

use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): Response
    {
        AuthorizationService::requireAdmin();

        return $next($request);
    }
}
