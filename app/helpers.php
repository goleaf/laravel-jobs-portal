<?php

use App\Helpers\LanguageHelper;
use App\Models\Company;
use App\Models\Country;
use Illuminate\Support\Str;

if (! function_exists('trans_json')) {
    /**
     * Get translation from JSON files.
     */
    function trans_json(string $key, array $replace = [], ?string $locale = null): string
    {
        return LanguageHelper::get($key, $locale, $replace);
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL.
     */
    function is_rtl(?string $locale = null): bool
    {
        return LanguageHelper::isRtl($locale);
    }
}

if (! function_exists('lang_direction')) {
    /**
     * Get language direction.
     */
    function lang_direction(?string $locale = null): string
    {
        return LanguageHelper::getDirection($locale);
    }
}

if (! function_exists('getCountries')) {
    /**
     * Get list of countries.
     *
     * @return array
     */
    function getCountries()
    {
        try {
            return Country::orderBy('name')->pluck('name')->toArray();
        } catch (Exception $e) {
            // Return empty array if countries table doesn't exist or error occurs
            return [];
        }
    }
}

if (! function_exists('getCountries')) {
    function getCountries()
    {
        try {
            return Country::orderBy('name')->pluck('name')->toArray();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (! function_exists('getUniqueCompanyId')) {
    /**
     * Generate a unique company ID.
     */
    function getUniqueCompanyId(): string
    {
        $companyUniqueId = Str::random(12);
        while (true) {
            $isExist = Company::where('unique_id', $companyUniqueId)->exists();
            if ($isExist) {
                $companyUniqueId = Str::random(12);

                continue;
            }

            break;
        }

        return $companyUniqueId;
    }
}
