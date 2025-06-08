<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request with Universal locale management.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get available languages from config
        $availableLanguages = Config::get('languages.available', ['en']);
        $defaultLanguage = Config::get('languages.default', 'en');
        $fallbackLanguage = Config::get('languages.fallback', 'en');

        // Priority order for locale determination:
        // 1. URL parameter 'lang'
        // 2. Session stored locale
        // 3. Browser Accept-Language header
        // 4. Default language from config

        $locale = null;

        // 1. Check URL parameter
        if ($request->has('lang')) {
            $requestedLocale = $request->get('lang');
            if (in_array($requestedLocale, $availableLanguages)) {
                $locale = $requestedLocale;
                // Store in session for future requests
                Session::put('locale', $locale);
            }
        }

        // 2. Check session
        if (!$locale && Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $availableLanguages)) {
                $locale = $sessionLocale;
            }
        }

        // 3. Check browser language preference
        if (!$locale) {
            $browserLocale = $this->detectBrowserLanguage($request, $availableLanguages);
            if ($browserLocale) {
                $locale = $browserLocale;
                Session::put('locale', $locale);
            }
        }

        // 4. Fallback to default
        if (!$locale) {
            $locale = $defaultLanguage;
        }

        // Set the application locale
        App::setLocale($locale);

        // Add locale to view data for frontend access
        view()->share('currentLocale', $locale);
        view()->share('availableLanguages', $this->getLanguageData($availableLanguages));
        view()->share('isRtl', $this->isRtlLanguage($locale));

        // Add locale headers for API responses
        if ($request->expectsJson()) {
            $response = $next($request);
            return $response->header('Content-Language', $locale);
        }

        return $next($request);
    }

    /**
     * Detect browser language preference
     */
    private function detectBrowserLanguage(Request $request, array $availableLanguages): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';q=', trim($lang));
            $code = trim($parts[0]);
            $quality = isset($parts[1]) ? (float) $parts[1] : 1.0;
            
            // Handle language-country codes (e.g., en-US -> en)
            if (strpos($code, '-') !== false) {
                $code = substr($code, 0, strpos($code, '-'));
            }
            
            $languages[$code] = $quality;
        }

        // Sort by quality (preference)
        arsort($languages);

        // Find first available language
        foreach (array_keys($languages) as $browserLang) {
            if (in_array($browserLang, $availableLanguages)) {
                return $browserLang;
            }
        }

        return null;
    }

    /**
     * Get language data for frontend
     */
    private function getLanguageData(array $availableLanguages): array
    {
        $languageMap = [
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
            'ar' => ['name' => 'Arabic', 'flag' => '🇸🇦', 'native' => 'العربية'],
            'de' => ['name' => 'German', 'flag' => '🇩🇪', 'native' => 'Deutsch'],
            'es' => ['name' => 'Spanish', 'flag' => '🇪🇸', 'native' => 'Español'],
            'fr' => ['name' => 'French', 'flag' => '🇫🇷', 'native' => 'Français'],
            'pt' => ['name' => 'Portuguese', 'flag' => '🇵🇹', 'native' => 'Português'],
            'ru' => ['name' => 'Russian', 'flag' => '🇷🇺', 'native' => 'Русский'],
            'tr' => ['name' => 'Turkish', 'flag' => '🇹🇷', 'native' => 'Türkçe'],
            'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文']
        ];

        $result = [];
        foreach ($availableLanguages as $lang) {
            if (isset($languageMap[$lang])) {
                $result[$lang] = $languageMap[$lang];
            }
        }

        return $result;
    }

    /**
     * Check if language is RTL
     */
    private function isRtlLanguage(string $locale): bool
    {
        $rtlLanguages = Config::get('languages.rtl_languages', ['ar', 'fa', 'he', 'ur']);
        return in_array($locale, $rtlLanguages);
    }
}
