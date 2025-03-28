<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsVerified
{
         /**
          * Handle an incoming request.
          */
         public function handle(Request $request, Closure $next): Response
         {
                  $response = $next($request);
                  $user = Auth::user();
                  if (Auth::check() && !$user->is_active) {
                           Auth::logout();
                           return redirect()->back()->withErrors('Your account is not active. Please contact the administrator.');
                  } elseif (Auth::check() && !$user->email_verified_at) {
                           Auth::logout();
                           return redirect()->back()->withErrors('Please verify your email address.');
                  }

                  return $response;
         }
}
