<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMiddleware
{
    public function handle(Request $request, \Closure $next, string ...$guards): Response
    {
        if (! Auth::guard($guards[0] ?? null)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Authentication required',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
