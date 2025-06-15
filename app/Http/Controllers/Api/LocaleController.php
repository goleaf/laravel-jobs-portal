<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
     * Set application locale.
     */
    public function setLocale(Request $request): JsonResponse
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
     * Get current locale information.
     */
    public function getCurrentLocale(): JsonResponse
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
     * Get all available locales.
     */
    public function getAvailableLocales(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'locales' => self::AVAILABLE_LOCALES,
            'current' => App::getLocale(),
        ]);
    }

    /**
     * Get translations for a specific locale.
     */
    public function getTranslations(Request $request, ?string $locale = null): JsonResponse
    {
        try {
            $locale = $locale ?? $request->input('locale', App::getLocale());

            if (!$this->isValidLocale($locale)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid locale provided',
                ], 400);
            }

            // Get translation file path
            $translationPath = lang_path("{$locale}.json");

            if (!file_exists($translationPath)) {
                return response()->json([
                    'success' => false,
                    'message' => "Translation file not found for locale: {$locale}",
                ], 404);
            }

            // Load translations
            $translations = json_decode(file_get_contents($translationPath), true);

            if (JSON_ERROR_NONE !== json_last_error()) {
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
                    $quality = floatval(substr($subparts[1], 2));
                }

                // Extract main language code (e.g., 'en' from 'en-US')
                $langCode = substr($lang, 0, 2);

                if ($this->isValidLocale($langCode)) {
                    $languages[$langCode] = $quality;
                }
            }

            if (!empty($languages)) {
                // Sort by quality and get the highest one
                arsort($languages);
                $preferredLocale = array_key_first($languages);
            }
        }

        return response()->json([
            'success' => true,
            'browser_locale' => $preferredLocale,
            'locale_config' => self::AVAILABLE_LOCALES[$preferredLocale],
            'fallback_used' => !$this->isValidLocale(substr($acceptLanguage, 0, 2)),
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
