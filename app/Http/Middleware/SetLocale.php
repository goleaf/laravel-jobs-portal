<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from various sources
        $locale = $this->getLocale($request);
        
        // Validate locale
        if (!$this->isValidLocale($locale)) {
            $locale = config('languages.default', 'en');
        }
        
        // Set application locale
        App::setLocale($locale);
        
        // Store in session for persistence
        Session::put('locale', $locale);
        
        return $next($request);
    }

    /**
     * Get locale from request
     */
    private function getLocale(Request $request): string
    {
        // 1. Check URL parameter
        if ($request->has('lang')) {
            return $request->get('lang');
        }
        
        // 2. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // 3. Check user preference (if authenticated)
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }
        
        // 4. Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLanguage = $request->getPreferredLanguage(config('languages.available', ['en']));
            if ($preferredLanguage) {
                return $preferredLanguage;
            }
        }
        
        // 5. Default locale
        return config('languages.default', 'en');
    }

    /**
     * Check if locale is valid
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, config('languages.available', ['en']));
    }
}
