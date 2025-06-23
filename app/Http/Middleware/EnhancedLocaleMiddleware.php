<?php

namespace App\Http\Middleware;

use App\Services\TranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

/**
 * Enhanced Locale Middleware
 * Advanced language detection and locale management.
 *
 * Features:
 * - Cookie-based persistence
 * - IP-based geo-location detection
 * - User preference storage
 * - Enhanced browser detection
 * - Translation preloading
 * - Performance optimization
 */
class EnhancedLocaleMiddleware
{
    /**
     * Handle an incoming request with enhanced locale detection.
     *
     * @param \Closure(Request): (RedirectResponse|Response) $next
     *
     * @return RedirectResponse|Response
     */
    public function handle(Request $request, \Closure $next)
    {
        // Get available locales from config
        $availableLocales = array_keys(Config::get('app.available_locales', ['en' => []]));

        // Determine locale using enhanced detection
        $locale = $this->determineLocaleEnhanced($request, $availableLocales);

        // Validate and set locale
        if (in_array($locale, $availableLocales)) {
            $this->setApplicationLocale($locale, $request);
        } else {
            // Fallback to default locale
            $defaultLocale = Config::get('app.locale', 'en');
            $this->setApplicationLocale($defaultLocale, $request);
        }

        return $next($request);
    }

    /**
     * Enhanced locale determination with multiple detection methods
     * Priority: URL Parameter > User Preference > Cookie > Session > Browser > GeoIP > Default.
     */
    private function determineLocaleEnhanced(Request $request, array $availableLocales): string
    {
        // 1. Check URL parameter (highest priority)
        if ($request->has('locale')) {
            $urlLocale = $request->get('locale');
            if (in_array($urlLocale, $availableLocales)) {
                return $urlLocale;
            }
        }

        // 2. Check user preference (if authenticated)
        if ($request->user() && method_exists($request->user(), 'preferred_locale')) {
            $userLocale = $request->user()->preferred_locale;
            if ($userLocale && in_array($userLocale, $availableLocales)) {
                return $userLocale;
            }
        }

        // 3. Check persistent cookie
        $cookieLocale = Cookie::get('preferred_locale');
        if ($cookieLocale && in_array($cookieLocale, $availableLocales)) {
            return $cookieLocale;
        }

        // 4. Check session (existing implementation)
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $availableLocales)) {
            return $sessionLocale;
        }

        // 5. Check Accept-Language header (enhanced)
        $browserLocale = $this->getBrowserLocaleEnhanced($request, $availableLocales);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 6. Try GeoIP detection (optional - requires additional service)
        $geoLocale = $this->getGeoLocale($request, $availableLocales);
        if ($geoLocale) {
            return $geoLocale;
        }

        // 7. Fallback to default locale
        return Config::get('app.locale', 'en');
    }

    /**
     * Enhanced browser locale detection with better parsing.
     */
    private function getBrowserLocaleEnhanced(Request $request, array $availableLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header with quality values
        $languages = [];
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            $lang = trim($part);
            $priority = 1.0;

            if (false !== strpos($lang, ';q=')) {
                [$lang, $q] = explode(';q=', $lang);
                $priority = (float) $q;
            }

            $lang = trim($lang);

            // Handle various language formats
            $lang = strtolower($lang);

            // Store with priority
            $languages[$lang] = $priority;

            // Also store language family (e.g., 'en-US' -> 'en')
            if (false !== strpos($lang, '-')) {
                $shortLang = substr($lang, 0, 2);
                if (!isset($languages[$shortLang]) || $languages[$shortLang] < $priority) {
                    $languages[$shortLang] = $priority * 0.9; // Slightly lower priority
                }
            }
        }

        // Sort by priority (highest first)
        arsort($languages);

        // Find best match
        foreach ($languages as $lang => $priority) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Get locale based on IP geolocation (optional feature)
     * This would require integration with a GeoIP service.
     */
    private function getGeoLocale(Request $request, array $availableLocales): ?string
    {
        // Cache geo-detection results
        $ip = $request->ip();
        $cacheKey = "geo_locale_{$ip}";

        return Cache::remember($cacheKey, 3600, function () {
            // This is a placeholder - you would integrate with a real GeoIP service
            // Example services: MaxMind, IPStack, IPInfo, etc.

            // For demo purposes, we'll return null
            // In a real implementation:
            // $country = $this->getCountryFromIP($ip);
            // return $this->mapCountryToLocale($country, $availableLocales);

            return null;
        });
    }

    /**
     * Set application locale with enhanced features.
     */
    private function setApplicationLocale(string $locale, Request $request): void
    {
        // Set Laravel application locale
        App::setLocale($locale);

        // Store in session
        Session::put('locale', $locale);

        // Set persistent cookie (30 days)
        Cookie::queue('preferred_locale', $locale, 60 * 24 * 30);

        // Update user preference if authenticated
        if ($request->user() && method_exists($request->user(), 'updatePreferredLocale')) {
            $request->user()->updatePreferredLocale($locale);
        }

        // Set locale-specific data for views
        $this->setLocaleViewData($locale);

        // Preload translations for performance
        $this->preloadTranslations($locale);

        // Set PHP locale for date/number formatting
        $this->setPHPLocale($locale);
    }

    /**
     * Set locale-specific data for views with enhanced information.
     */
    private function setLocaleViewData(string $locale): void
    {
        $localeConfig = Config::get("app.available_locales.{$locale}", []);

        // Enhanced view data
        view()->share([
            'currentLocale' => $locale,
            'isRTL' => $localeConfig['rtl'] ?? false,
            'localeNative' => $localeConfig['native'] ?? $locale,
            'localeName' => $localeConfig['name'] ?? $locale,
            'availableLocales' => Config::get('app.available_locales', []),
            'localeDirection' => ($localeConfig['rtl'] ?? false) ? 'rtl' : 'ltr',
            'localeScript' => $localeConfig['script'] ?? 'Latn',
            'localeRegional' => $localeConfig['regional'] ?? $locale,
            'localeFlag' => $this->getFlag($locale),
        ]);

        // Set HTML attributes
        if (function_exists('app') && app()->bound('request')) {
            $request = app('request');
            $request->attributes->set('locale', $locale);
            $request->attributes->set('direction', ($localeConfig['rtl'] ?? false) ? 'rtl' : 'ltr');
        }
    }

    /**
     * Preload translations for performance.
     */
    private function preloadTranslations(string $locale): void
    {
        // Preload critical translation namespaces
        $criticalNamespaces = ['common', 'messages', 'validation', 'auth'];

        foreach ($criticalNamespaces as $namespace) {
            TranslationService::getNamespaceTranslations($locale, $namespace);
        }
    }

    /**
     * Set PHP locale for proper date/number formatting.
     */
    private function setPHPLocale(string $locale): void
    {
        $localeConfig = Config::get("app.available_locales.{$locale}", []);
        $regional = $localeConfig['regional'] ?? $locale;

        // Try to set PHP locale
        $localeVariants = [
            $regional.'.UTF-8',
            $regional.'.utf8',
            $regional,
            $locale.'.UTF-8',
            $locale.'.utf8',
            $locale,
        ];

        foreach ($localeVariants as $variant) {
            if (setlocale(LC_TIME, $variant)) {
                break;
            }
        }
    }

    /**
     * Get flag emoji for locale.
     */
    private function getFlag(string $locale): string
    {
        $flags = [
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'tr' => '🇹🇷',
            'zh' => '🇨🇳',
        ];

        return $flags[$locale] ?? '🌐';
    }
}
