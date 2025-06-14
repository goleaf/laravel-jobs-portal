<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

/**
 * Enhanced Locale Middleware
 * Handles automatic language detection and locale switching
 * 
 * Features:
 * - Browser language detection
 * - Session persistence
 * - URL parameter handling
 * - Fallback to default locale
 * - Available locale validation
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request and set the application locale
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get available locales from config
        $availableLocales = array_keys(Config::get('app.available_locales', ['en' => []]));
        
        // Determine locale from multiple sources (priority order)
        $locale = $this->determineLocale($request, $availableLocales);
        
        // Validate and set locale
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            
            // Set additional locale data for views
            $this->setLocaleViewData($locale);
        } else {
            // Fallback to default locale
            $defaultLocale = Config::get('app.locale', 'en');
            App::setLocale($defaultLocale);
            Session::put('locale', $defaultLocale);
            $this->setLocaleViewData($defaultLocale);
        }

        return $next($request);
    }

    /**
     * Determine the best locale from multiple sources
     * Priority: URL parameter > Session > Browser > Default
     *
     * @param Request $request
     * @param array $availableLocales
     * @return string
     */
    private function determineLocale(Request $request, array $availableLocales): string
    {
        // 1. Check URL parameter (highest priority)
        if ($request->has('locale')) {
            $urlLocale = $request->get('locale');
            if (in_array($urlLocale, $availableLocales)) {
                return $urlLocale;
            }
        }

        // 2. Check session (user preference)
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $availableLocales)) {
            return $sessionLocale;
        }

        // 3. Check Accept-Language header (browser preference)
        $browserLocale = $this->getBrowserLocale($request, $availableLocales);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 4. Fallback to default locale
        return Config::get('app.locale', 'en');
    }

    /**
     * Extract locale from browser Accept-Language header
     *
     * @param Request $request
     * @param array $availableLocales
     * @return string|null
     */
    private function getBrowserLocale(Request $request, array $availableLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $lang = trim($part);
            $priority = 1.0;
            
            if (strpos($lang, ';q=') !== false) {
                [$lang, $q] = explode(';q=', $lang);
                $priority = (float) $q;
            }
            
            $lang = trim($lang);
            $languages[$lang] = $priority;
        }
        
        // Sort by priority (highest first)
        arsort($languages);
        
        // Find best match
        foreach ($languages as $lang => $priority) {
            // Exact match
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
            
            // Language family match (e.g., 'en-US' -> 'en')
            $shortLang = substr($lang, 0, 2);
            if (in_array($shortLang, $availableLocales)) {
                return $shortLang;
            }
        }
        
        return null;
    }

    /**
     * Set locale-specific data for views
     *
     * @param string $locale
     * @return void
     */
    private function setLocaleViewData(string $locale): void
    {
        $localeConfig = Config::get("app.available_locales.{$locale}", []);
        
        // Share locale data with all views
        view()->share([
            'currentLocale' => $locale,
            'isRTL' => $localeConfig['rtl'] ?? false,
            'localeNative' => $localeConfig['native'] ?? $locale,
            'localeName' => $localeConfig['name'] ?? $locale,
            'availableLocales' => Config::get('app.available_locales', [])
        ]);
    }
} 