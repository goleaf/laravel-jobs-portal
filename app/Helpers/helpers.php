<?php

if (!function_exists('settings')) {
    function settings()
    {
        return [
            'app_name' => config('app.name', 'Job Portal'),
            'favicon' => '/favicon.ico',
            'default_country_code' => 'US',
            'logo' => '/images/logo.png'
        ];
    }
}

if (!function_exists('getAppName')) {
    function getAppName()
    {
        return config('app.name', 'Job Portal');
    }
}

if (!function_exists('getSettingValue')) {
    function getSettingValue($key, $default = null)
    {
        $settings = [
            'favicon' => '/favicon.ico',
            'app_name' => config('app.name', 'Job Portal'),
            'default_country_code' => 'US',
            'logo' => '/images/logo.png',
            'company_name' => config('app.name', 'Job Portal'),
            'app_url' => config('app.url', 'http://localhost'),
        ];
        
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency = 'USD')
    {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($date)
    {
        $carbon = \Carbon\Carbon::parse($date);
        return $carbon->diffForHumans();
    }
}

if (!function_exists('getCountries')) {
    function getCountries()
    {
        try {
            return \App\Models\Country::orderBy('name')->pluck('name')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}

if (!function_exists('getUniqueCompanyId')) {
    function getUniqueCompanyId(): string
    {
        $companyUniqueId = \Illuminate\Support\Str::random(12);
        while (\App\Models\Company::where('unique_id', $companyUniqueId)->exists()) {
            $companyUniqueId = \Illuminate\Support\Str::random(12);
        }
        return $companyUniqueId;
    }
}
