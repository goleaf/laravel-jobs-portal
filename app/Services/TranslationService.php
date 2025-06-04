<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

/**
 * JSON-based Translation Service
 * 
 * Handles loading and caching of JSON translation files
 * for improved performance over PHP array files.
 */
class TranslationService
{
    private static array $loadedTranslations = [];
    private static string $defaultLocale = "en";

    /**
     * Get translation for a key
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale() ?: self::$defaultLocale;
        
        // Load translations for this locale if not already loaded
        if (!isset(self::$loadedTranslations[$locale])) {
            self::loadTranslations($locale);
        }

        // Get the translation
        $translation = self::getNestedTranslation($key, $locale);
        
        // If not found, try default locale
        if ($translation === $key && $locale !== self::$defaultLocale) {
            if (!isset(self::$loadedTranslations[self::$defaultLocale])) {
                self::loadTranslations(self::$defaultLocale);
            }
            $translation = self::getNestedTranslation($key, self::$defaultLocale);
        }

        // Replace placeholders
        foreach ($replace as $placeholder => $value) {
            $translation = str_replace(":{$placeholder}", $value, $translation);
        }

        return $translation;
    }

    /**
     * Load translations from JSON file
     */
    private static function loadTranslations(string $locale): void
    {
        $cacheKey = "translations_{$locale}";
        
        self::$loadedTranslations[$locale] = Cache::remember($cacheKey, 3600, function () use ($locale) {
            $filePath = lang_path("{$locale}.json");
            
            if (!File::exists($filePath)) {
                return [];
            }

            $content = File::get($filePath);
            return json_decode($content, true) ?: [];
        });
    }

    /**
     * Get nested translation using dot notation
     */
    private static function getNestedTranslation(string $key, string $locale): string
    {
        $translations = self::$loadedTranslations[$locale] ?? [];
        $keys = explode(".", $key);
        $value = $translations;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $key; // Return key if not found
            }
        }

        return is_string($value) ? $value : $key;
    }

    /**
     * Clear translation cache
     */
    public static function clearCache(): void
    {
        foreach (["ar", "de", "en", "es", "fr", "pt", "ru", "tr", "zh"] as $locale) {
            Cache::forget("translations_{$locale}");
        }
        self::$loadedTranslations = [];
    }

    /**
     * Get all available locales
     */
    public static function getAvailableLocales(): array
    {
        return ["ar", "de", "en", "es", "fr", "pt", "ru", "tr", "zh"];
    }
}
