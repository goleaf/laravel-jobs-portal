<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales.
     */
    private const AVAILABLE_LOCALES = [
        'en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh',
    ];

    /**
     * Default locale.
     */
    private const DEFAULT_LOCALE = 'en';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Store in session for consistency
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Detect the appropriate locale using Enhanced patterns.
     */
    private function detectLocale(Request $request): string
    {
        // Priority 1: URL parameter (for direct links)
        if ($request->has('locale') && $this->isValidLocale($request->get('locale'))) {
            return $request->get('locale');
        }

        // Priority 2: Session (for user preference)
        if (Session::has('locale') && $this->isValidLocale(Session::get('locale'))) {
            return Session::get('locale');
        }

        // Priority 3: User preference (for authenticated users)
        if (auth()->check() && auth()->user()->locale && $this->isValidLocale(auth()->user()->locale)) {
            return auth()->user()->locale;
        }

        // Priority 4: Cookie (for guest users)
        if ($request->hasCookie('locale') && $this->isValidLocale($request->cookie('locale'))) {
            return $request->cookie('locale');
        }

        // Priority 5: Browser preference (Accept-Language header)
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale && $this->isValidLocale($browserLocale)) {
            return $browserLocale;
        }

        // Priority 6: Default locale
        return self::DEFAULT_LOCALE;
    }

    /**
     * Get browser's preferred locale from Accept-Language header.
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            $subparts = explode(';', trim($part));
            $lang = trim($subparts[0]);
            $quality = 1.0;

            if (count($subparts) > 1 && 0 === strpos($subparts[1], 'q=')) {
                $quality = floatval(substr($subparts[1], 2));
            }

            // Extract main language code (e.g., 'en' from 'en-US')
            $langCode = substr($lang, 0, 2);

            if ($this->isValidLocale($langCode)) {
                $languages[$langCode] = $quality;
            }
        }

        if (empty($languages)) {
            return null;
        }

        // Sort by quality and return the highest one
        arsort($languages);

        return array_key_first($languages);
    }

    /**
     * Validate if locale is supported.
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, self::AVAILABLE_LOCALES);
    }

    /**
     * Get language data for frontend.
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
            'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文'],
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
     * Check if language is RTL.
     */
    private function isRtlLanguage(string $locale): bool
    {
        $rtlLanguages = Config::get('languages.rtl_languages', ['ar', 'fa', 'he', 'ur']);

        return in_array($locale, $rtlLanguages);
    }
}
