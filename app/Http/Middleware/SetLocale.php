<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLanguages = ['en', 'lt'];
        
        // Get locale from session, URL parameter, or user preference
        $locale = $request->session()->get('locale') 
                  ?? $request->get('lang') 
                  ?? auth()->user()->preferred_language ?? 'en';
        
        // Validate locale
        if (in_array($locale, $supportedLanguages)) {
            App::setLocale($locale);
        } else {
            App::setLocale('en'); // Default fallback
        }
        
        return $next($request);
    }
}