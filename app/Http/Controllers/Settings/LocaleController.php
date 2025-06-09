<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Services\TranslationService;
use App\Helpers\LanguageHelper;

/**
 * LocaleController
 * Handles language switching and locale management
 */
class LocaleController extends Controller
{
    /**
     * Switch application locale
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|size:2|in:' . implode(',', array_keys(Config::get('app.available_locales', [])))
        ]);

        $locale = $request->input('locale');
        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (!in_array($locale, $availableLocales)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('locale.invalid_locale')
                ], 400);
            }
            
            return redirect()->back()->withErrors(__('locale.invalid_locale'));
        }

        // Set the application locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Clear translation cache for the new locale
        Cache::forget("translations_{$locale}");

        $message = __('locale.language_switched_successfully');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'locale' => $locale,
                'direction' => LanguageHelper::getDirection($locale),
                'is_rtl' => LanguageHelper::isRtl($locale)
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get current locale information
     *
     * @return JsonResponse
     */
    public function current(): JsonResponse
    {
        $currentLocale = App::getLocale();
        $availableLocales = Config::get('app.available_locales', []);

        return response()->json([
            'current' => $currentLocale,
            'config' => $availableLocales[$currentLocale] ?? [],
            'direction' => LanguageHelper::getDirection($currentLocale),
            'is_rtl' => LanguageHelper::isRtl($currentLocale),
            'available_locales' => $availableLocales
        ]);
    }

    /**
     * Get all available locales
     *
     * @return JsonResponse
     */
    public function available(): JsonResponse
    {
        $availableLocales = Config::get('app.available_locales', []);
        $currentLocale = App::getLocale();

        $formattedLocales = [];
        foreach ($availableLocales as $code => $config) {
            $formattedLocales[] = [
                'code' => $code,
                'name' => $config['name'] ?? $code,
                'native' => $config['native'] ?? $code,
                'flag' => $this->getFlag($code),
                'rtl' => $config['rtl'] ?? false,
                'current' => $code === $currentLocale
            ];
        }

        return response()->json([
            'locales' => $formattedLocales,
            'current' => $currentLocale
        ]);
    }

    /**
     * Get translations for a specific locale
     *
     * @param Request $request
     * @param string $locale
     * @return JsonResponse
     */
    public function translations(Request $request, string $locale = null): JsonResponse
    {
        $locale = $locale ?: App::getLocale();
        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'error' => __('locale.invalid_locale')
            ], 400);
        }

        // Get specific namespace if requested
        $namespace = $request->query('namespace');
        
        if ($namespace) {
            $translations = TranslationService::getNamespaceTranslations($locale, $namespace);
        } else {
            $translations = TranslationService::getAllTranslations($locale);
        }

        return response()->json([
            'locale' => $locale,
            'translations' => $translations,
            'namespace' => $namespace
        ]);
    }

    /**
     * Clear translation cache
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        TranslationService::clearCache();
        LanguageHelper::clearCache();

        return response()->json([
            'success' => true,
            'message' => __('locale.cache_cleared_successfully')
        ]);
    }

    /**
     * Get flag emoji for locale
     *
     * @param string $locale
     * @return string
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
            'zh' => '🇨🇳'
        ];

        return $flags[$locale] ?? '🌐';
    }
} 