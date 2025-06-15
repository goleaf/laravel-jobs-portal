<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * TranslationService
 * Comprehensive translation management with caching and namespace support.
 */
class TranslationService
{
    private static array $loadedTranslations = [];
    private static array $availableLocales = ['ar', 'de', 'en', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];

    /**
     * Get translation with fallback support.
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();

        // Load translations if not already loaded
        if (!isset(self::$loadedTranslations[$locale])) {
            self::loadTranslations($locale);
        }

        // Get translation value
        $value = self::getNestedTranslation($key, $locale);

        // Fallback to default locale if not found
        if ($value === $key && $locale !== config('app.fallback_locale', 'en')) {
            $fallbackLocale = config('app.fallback_locale', 'en');

            if (!isset(self::$loadedTranslations[$fallbackLocale])) {
                self::loadTranslations($fallbackLocale);
            }

            $value = self::getNestedTranslation($key, $fallbackLocale);
        }

        // Replace placeholders
        foreach ($replace as $search => $replacement) {
            $value = str_replace(":{$search}", $replacement, $value);
        }

        return $value;
    }

    /**
     * Get all translations for a locale.
     */
    public static function getAllTranslations(string $locale): array
    {
        if (!isset(self::$loadedTranslations[$locale])) {
            self::loadTranslations($locale);
        }

        return self::$loadedTranslations[$locale] ?? [];
    }

    /**
     * Get translations for a specific namespace.
     */
    public static function getNamespaceTranslations(string $locale, string $namespace): array
    {
        $allTranslations = self::getAllTranslations($locale);

        // Return specific namespace if it exists
        if (isset($allTranslations[$namespace])) {
            return [$namespace => $allTranslations[$namespace]];
        }

        // Search for namespace in nested structure
        $namespaceTranslations = [];
        foreach ($allTranslations as $key => $value) {
            if (0 === strpos($key, $namespace.'.')) {
                $namespaceTranslations[$key] = $value;
            }
        }

        return $namespaceTranslations;
    }

    /**
     * Clear translation cache.
     */
    public static function clearCache(): void
    {
        foreach (self::$availableLocales as $locale) {
            Cache::forget("translations_{$locale}");
        }
        self::$loadedTranslations = [];
    }

    /**
     * Check if a translation key exists.
     */
    public static function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: App::getLocale();

        if (!isset(self::$loadedTranslations[$locale])) {
            self::loadTranslations($locale);
        }

        $translations = self::$loadedTranslations[$locale] ?? [];

        // Check direct key
        if (isset($translations[$key])) {
            return true;
        }

        // Check nested keys with dot notation
        $keys = explode('.', $key);
        $value = $translations;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing translation keys for a locale.
     */
    public static function getMissingKeys(string $locale, string $compareLocale = 'en'): array
    {
        $sourceTranslations = self::getAllTranslations($compareLocale);
        $targetTranslations = self::getAllTranslations($locale);

        $missingKeys = [];

        foreach (array_keys($sourceTranslations) as $key) {
            if (!isset($targetTranslations[$key])) {
                $missingKeys[] = $key;
            }
        }

        return $missingKeys;
    }

    /**
     * Get translation statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [];
        $baseLocale = 'en';
        $baseTranslations = self::getAllTranslations($baseLocale);
        $totalKeys = count($baseTranslations);

        foreach (self::$availableLocales as $locale) {
            $translations = self::getAllTranslations($locale);
            $translatedKeys = count($translations);
            $missingKeys = count(self::getMissingKeys($locale, $baseLocale));
            $coverage = $totalKeys > 0 ? round(($translatedKeys / $totalKeys) * 100, 2) : 0;

            $stats[$locale] = [
                'total_keys' => $totalKeys,
                'translated_keys' => $translatedKeys,
                'missing_keys' => $missingKeys,
                'coverage_percentage' => $coverage,
                'is_complete' => 0 === $missingKeys,
            ];
        }

        return $stats;
    }

    /**
     * Preload translations for multiple locales.
     */
    public static function preloadTranslations(array $locales = []): void
    {
        $locales = empty($locales) ? self::$availableLocales : $locales;

        foreach ($locales as $locale) {
            if (in_array($locale, self::$availableLocales)) {
                self::loadTranslations($locale);
            }
        }
    }

    /**
     * Get available locales.
     */
    public static function getAvailableLocales(): array
    {
        return self::$availableLocales;
    }

    /**
     * Add custom locale.
     */
    public static function addLocale(string $locale): void
    {
        if (!in_array($locale, self::$availableLocales)) {
            self::$availableLocales[] = $locale;
        }
    }

    /**
     * Load translations from files with caching.
     */
    private static function loadTranslations(string $locale): void
    {
        $cacheKey = "translations_{$locale}";

        self::$loadedTranslations[$locale] = Cache::remember($cacheKey, 3600, function () use ($locale) {
            $translations = [];

            try {
                // Load JSON translations
                $jsonFilePath = lang_path("{$locale}.json");
                if (File::exists($jsonFilePath)) {
                    $jsonContent = File::get($jsonFilePath);
                    $jsonTranslations = json_decode($jsonContent, true) ?: [];
                    $translations = array_merge($translations, $jsonTranslations);
                }

                // Load PHP array translations
                $phpLangPath = lang_path($locale);
                if (File::isDirectory($phpLangPath)) {
                    $phpFiles = File::allFiles($phpLangPath);

                    foreach ($phpFiles as $file) {
                        if ('php' === $file->getExtension()) {
                            $namespace = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                            $fileTranslations = include $file->getPathname();

                            if (is_array($fileTranslations)) {
                                // Flatten nested arrays with dot notation
                                $flattenedTranslations = self::flattenArray($fileTranslations, $namespace);
                                $translations = array_merge($translations, $flattenedTranslations);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to load translations for locale {$locale}: ".$e->getMessage());
            }

            return $translations;
        });
    }

    /**
     * Get nested translation using dot notation.
     */
    private static function getNestedTranslation(string $key, string $locale): string
    {
        $translations = self::$loadedTranslations[$locale] ?? [];

        // Check direct key first
        if (isset($translations[$key])) {
            $value = $translations[$key];

            return is_string($value) ? $value : $key;
        }

        // Check nested keys with dot notation
        $keys = explode('.', $key);
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
     * Flatten nested array with dot notation.
     */
    private static function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, self::flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
