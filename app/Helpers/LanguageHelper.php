<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LanguageHelper
{
    /**
     * Get all available languages
     */
    public static function getAvailableLanguages(): array
    {
        return config('languages.available', ['en']);
    }

    /**
     * Get translations for a specific language
     */
    public static function getTranslations(string $locale): array
    {
        return Cache::remember("translations.{$locale}", 3600, function () use ($locale) {
            $jsonPath = str_replace('{locale}', $locale, config('languages.json_path'));
            $masterFile = $jsonPath . '/master.json';
            
            if (File::exists($masterFile)) {
                return json_decode(File::get($masterFile), true) ?: [];
            }
            
            return [];
        });
    }

    /**
     * Get a specific translation
     */
    public static function get(string $key, string $locale = null, array $replace = []): string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = self::getTranslations($locale);
        
        $keys = explode('.', $key);
        $value = $translations;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $key; // Return key if translation not found
            }
            $value = $value[$k];
        }
        
        // Replace placeholders
        foreach ($replace as $search => $replacement) {
            $value = str_replace(":$search", $replacement, $value);
        }
        
        return $value;
    }

    /**
     * Check if language is RTL
     */
    public static function isRtl(string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();
        return in_array($locale, config('languages.rtl_languages', []));
    }

    /**
     * Clear translation cache
     */
    public static function clearCache(): void
    {
        $languages = self::getAvailableLanguages();
        
        foreach ($languages as $language) {
            Cache::forget("translations.{$language}");
        }
    }

    /**
     * Get language direction
     */
    public static function getDirection(string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }
}
