<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

/**
 * Context7 Locale Controller
 * Handles language switching and locale management
 * 
 * Features:
 * - AJAX language switching
 * - Locale validation
 * - Session persistence
 * - JSON API responses
 * - Redirect-based switching
 */
class LocaleController extends Controller
{
    /**
     * Switch application locale
     * Supports both AJAX and redirect responses
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function switch(Request $request)
    {
        // Validate locale parameter
        $validator = Validator::make($request->all(), [
            'locale' => [
                'required',
                'string',
                'size:2',
                function ($attribute, $value, $fail) {
                    $availableLocales = array_keys(Config::get('app.available_locales', []));
                    if (!in_array($value, $availableLocales)) {
                        $fail(__('validation.locale_not_supported', ['locale' => $value]));
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('validation.invalid_locale'),
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $locale = $request->input('locale');
        
        // Set application locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Get locale information
        $localeConfig = Config::get("app.available_locales.{$locale}", []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('locale.switched_successfully', ['language' => $localeConfig['native'] ?? $locale]),
                'data' => [
                    'locale' => $locale,
                    'name' => $localeConfig['name'] ?? $locale,
                    'native' => $localeConfig['native'] ?? $locale,
                    'rtl' => $localeConfig['rtl'] ?? false,
                    'regional' => $localeConfig['regional'] ?? $locale,
                ]
            ]);
        }

        // Redirect back with success message
        return redirect()->back()
            ->with('success', __('locale.switched_successfully', ['language' => $localeConfig['native'] ?? $locale]));
    }

    /**
     * Get available locales as JSON
     *
     * @return JsonResponse
     */
    public function getAvailableLocales(): JsonResponse
    {
        $availableLocales = Config::get('app.available_locales', []);
        $currentLocale = App::getLocale();

        return response()->json([
            'success' => true,
            'data' => [
                'current' => $currentLocale,
                'available' => $availableLocales,
                'default' => Config::get('app.locale', 'en'),
                'fallback' => Config::get('app.fallback_locale', 'en')
            ]
        ]);
    }

    /**
     * Get current locale information
     *
     * @return JsonResponse
     */
    public function getCurrentLocale(): JsonResponse
    {
        $currentLocale = App::getLocale();
        $localeConfig = Config::get("app.available_locales.{$currentLocale}", []);

        return response()->json([
            'success' => true,
            'data' => [
                'locale' => $currentLocale,
                'name' => $localeConfig['name'] ?? $currentLocale,
                'native' => $localeConfig['native'] ?? $currentLocale,
                'rtl' => $localeConfig['rtl'] ?? false,
                'regional' => $localeConfig['regional'] ?? $currentLocale,
                'script' => $localeConfig['script'] ?? 'Latn'
            ]
        ]);
    }

    /**
     * Check if a locale is supported
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkLocale(Request $request): JsonResponse
    {
        $locale = $request->input('locale');
        $availableLocales = array_keys(Config::get('app.available_locales', []));
        
        $isSupported = in_array($locale, $availableLocales);
        
        return response()->json([
            'success' => true,
            'data' => [
                'locale' => $locale,
                'supported' => $isSupported,
                'message' => $isSupported 
                    ? __('locale.locale_supported') 
                    : __('locale.locale_not_supported')
            ]
        ]);
    }

    /**
     * Get RTL languages list
     *
     * @return JsonResponse
     */
    public function getRTLLanguages(): JsonResponse
    {
        $availableLocales = Config::get('app.available_locales', []);
        $rtlLanguages = [];

        foreach ($availableLocales as $locale => $config) {
            if (isset($config['rtl']) && $config['rtl'] === true) {
                $rtlLanguages[] = [
                    'locale' => $locale,
                    'name' => $config['name'] ?? $locale,
                    'native' => $config['native'] ?? $locale
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'rtl_languages' => $rtlLanguages,
                'total' => count($rtlLanguages)
            ]
        ]);
    }
} 