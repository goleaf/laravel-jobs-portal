<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * LocaleController.
 *
 * Handles language switching and locale management for the Laravel Job Portal
 * Enhanced Level 4 transformation - Universal locale management
 */
class LocaleController extends Controller
{
    /**
     * Available languages with Enhanced configuration.
     */
    private const AVAILABLE_LOCALES = [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl'],
        'de' => ['name' => 'German', 'native' => 'Deutsch', 'dir' => 'ltr'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'dir' => 'ltr'],
        'fr' => ['name' => 'French', 'native' => 'Français', 'dir' => 'ltr'],
        'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'dir' => 'ltr'],
        'ru' => ['name' => 'Russian', 'native' => 'Русский', 'dir' => 'ltr'],
        'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'dir' => 'ltr'],
        'zh' => ['name' => 'Chinese', 'native' => '中文', 'dir' => 'ltr'],
    ];

    /**
     * Switch application locale (POST endpoint).
     */
    public function switch(Request $request): JsonResponse
    {
        try {
            $locale = $request->input('locale', 'en');

            // Validate locale
            if (!$this->isValidLocale($locale)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid locale provided',
                    'available_locales' => array_keys(self::AVAILABLE_LOCALES),
                ], 400);
            }

            // Set application locale
            App::setLocale($locale);

            // Store in session
            Session::put('locale', $locale);

            // Store in user preferences if authenticated
            if (auth()->check()) {
                auth()->user()->update(['locale' => $locale]);
            }

            // Log locale change
            Log::info('Locale changed', [
                'user_id' => auth()->id(),
                'previous_locale' => Session::get('previous_locale', 'en'),
                'new_locale' => $locale,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Store previous locale for logging
            Session::put('previous_locale', $locale);

            return response()->json([
                'success' => true,
                'message' => __('messages.locale_changed_successfully'),
                'locale' => $locale,
                'locale_config' => self::AVAILABLE_LOCALES[$locale],
                'direction' => self::AVAILABLE_LOCALES[$locale]['dir'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting locale', [
                'error' => $e->getMessage(),
                'locale' => $request->input('locale'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error setting locale',
                'error' => app()->environment('production') ? 'Internal server error' : $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current locale information (GET endpoint).
     */
    public function current(): JsonResponse
    {
        $currentLocale = App::getLocale();

        return response()->json([
            'success' => true,
            'current_locale' => $currentLocale,
            'locale_config' => self::AVAILABLE_LOCALES[$currentLocale] ?? self::AVAILABLE_LOCALES['en'],
            'available_locales' => self::AVAILABLE_LOCALES,
            'user_preference' => auth()->check() ? auth()->user()->locale : null,
        ]);
    }

    /**
     * Get all available locales (GET endpoint).
     */
    public function available(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'locales' => self::AVAILABLE_LOCALES,
            'current' => App::getLocale(),
        ]);
    }

    /**
     * Get translations for a specific locale (GET endpoint).
     */
    public function translations(Request $request, ?string $locale = null): JsonResponse
    {
        try {
            $locale = $locale ?? $request->input('locale', App::getLocale());

            if (!$this->isValidLocale($locale)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid locale provided',
                ], 400);
            }

            // Try cache first
            $cacheKey = "translations.{$locale}";
            $translations = Cache::remember($cacheKey, 3600, function () use ($locale) {
                $translationPath = lang_path("{$locale}.json");

                if (!file_exists($translationPath)) {
                    return null;
                }

                $content = file_get_contents($translationPath);

                return json_decode($content, true);
            });

            if (null === $translations) {
                return response()->json([
                    'success' => false,
                    'message' => "Translation file not found for locale: {$locale}",
                ], 404);
            }

            if (!is_array($translations)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON in translation file',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'translations' => $translations,
                'locale_config' => self::AVAILABLE_LOCALES[$locale],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting translations', [
                'error' => $e->getMessage(),
                'locale' => $locale,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading translations',
                'error' => app()->environment('production') ? 'Internal server error' : $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear translation cache (POST endpoint).
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            // Clear all translation caches
            foreach (array_keys(self::AVAILABLE_LOCALES) as $locale) {
                Cache::forget("translations.{$locale}");
            }

            Log::info('Translation cache cleared', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Translation cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing translation cache', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache',
                'error' => app()->environment('production') ? 'Internal server error' : $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get browser's preferred language.
     */
    public function getBrowserLocale(Request $request): JsonResponse
    {
        $acceptLanguage = $request->header('Accept-Language', '');
        $preferredLocale = 'en'; // Default fallback

        if ($acceptLanguage) {
            // Parse Accept-Language header
            $languages = [];
            $parts = explode(',', $acceptLanguage);

            foreach ($parts as $part) {
                $subparts = explode(';', trim($part));
                $lang = trim($subparts[0]);
                $quality = 1.0;

                if (count($subparts) > 1 && 0 === strpos($subparts[1], 'q=')) {
                    $quality = (float) substr($subparts[1], 2);
                }

                // Extract language code (first 2 characters)
                $langCode = substr($lang, 0, 2);
                $languages[$langCode] = $quality;
            }

            // Sort by quality (highest first)
            arsort($languages);

            // Find first supported language
            foreach ($languages as $lang => $quality) {
                if ($this->isValidLocale($lang)) {
                    $preferredLocale = $lang;

                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'browser_locale' => $preferredLocale,
            'supported' => $this->isValidLocale($preferredLocale),
            'locale_config' => self::AVAILABLE_LOCALES[$preferredLocale] ?? self::AVAILABLE_LOCALES['en'],
        ]);
    }

    /**
     * Validate if locale is supported.
     */
    private function isValidLocale(string $locale): bool
    {
        return array_key_exists($locale, self::AVAILABLE_LOCALES);
    }
}
