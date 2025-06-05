<?php

if (!function_exists('trans_json')) {
    /**
     * Get translation from JSON files
     */
    function trans_json(string $key, array $replace = [], string $locale = null): string
    {
        return App\Helpers\LanguageHelper::get($key, $locale, $replace);
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL
     */
    function is_rtl(string $locale = null): bool
    {
        return App\Helpers\LanguageHelper::isRtl($locale);
    }
}

if (!function_exists('lang_direction')) {
    /**
     * Get language direction
     */
    function lang_direction(string $locale = null): string
    {
        return App\Helpers\LanguageHelper::getDirection($locale);
    }
}

if (!function_exists('getCountries')) {
    /**
     * Get list of countries
     * @return array
     */
    function getCountries()
    {
        try {
            $countries = \App\Models\Country::orderBy('name')->pluck('name')->toArray();
            return $countries;
        } catch (\Exception $e) {
            // Return empty array if countries table doesn't exist or error occurs
            return [];
        }
    }
}

if (!function_exists("getCountries")) {
    function getCountries() {
        try {
            $countries = \App\Models\Country::orderBy("name")->pluck("name")->toArray();
            return $countries;
        } catch (\Exception $e) {
            return [];
        }
    }
}
