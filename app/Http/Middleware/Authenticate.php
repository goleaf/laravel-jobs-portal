<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string[]  ...$guards
     * @param  mixed  $request
     *
     * @throws AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards): Response
    {
        $this->authenticate($request, $guards);

        // Set locale based on authenticated user's language preference
        if ($request->user() && isset($request->user()->language)) {
            App::setLocale($request->user()->language);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  mixed  $request
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
