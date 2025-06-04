<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

/**
 * Translation Service for JSON-based translations
 */
class TranslationService
{
    private $defaultLocale = 'en';
    private $fallbackLocale = 'en';
    private $translations = [];

    /**
     * Load translations for a specific locale
     */
    public function loadTranslations($locale = null)
    {
        $locale = $locale ?: App::getLocale();
        
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }

        $cacheKey = "translations.$locale";
        
        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            return $this->loadTranslationsFromFile($locale);
        });
    }

    /**
     * Load translations from JSON file
     */
    private function loadTranslationsFromFile($locale)
    {
        $filePath = lang_path("$locale.json");
        
        if (!File::exists($filePath)) {
            // Fallback to default locale
            $filePath = lang_path("{$this->fallbackLocale}.json");
        }

        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $translations = json_decode($content, true);

        return $translations ?: [];
    }

    /**
     * Get a translation by key
     */
    public function get($key, $replace = [], $locale = null)
    {
        $translations = $this->loadTranslations($locale);
        
        $value = data_get($translations, $key, $key);
        
        if (!empty($replace)) {
            foreach ($replace as $placeholder => $replacement) {
                $value = str_replace(":$placeholder", $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Check if a translation exists
     */
    public function has($key, $locale = null)
    {
        $translations = $this->loadTranslations($locale);
        return data_get($translations, $key) !== null;
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        $cacheKeys = [];
        foreach (['ar', 'de', 'en', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'] as $locale) {
            $cacheKeys[] = "translations.$locale";
        }
        
        Cache::forget($cacheKeys);
    }

    /**
     * Get all available locales
     */
    public function getAvailableLocales()
    {
        $locales = [];
        $langPath = lang_path();
        
        foreach (glob($langPath . '/*.json') as $file) {
            $locale = basename($file, '.json');
            $locales[] = $locale;
        }

        return $locales;
    }
}