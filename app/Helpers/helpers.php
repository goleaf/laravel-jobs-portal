<?php

if (!function_exists('settings')) {
    /**
     * Get application settings
     * @return array
     */
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
    /**
     * Get application name
     * @return string
     */
    function getAppName()
    {
        return config('app.name', 'Job Portal');
    }
}

if (!function_exists('getSettingValue')) {
    /**
     * Get a specific setting value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
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

if (!function_exists('googleJobSchema')) {
    /**
     * Generate Google job schema
     * @return array
     */
    function googleJobSchema()
    {
        // Return empty array for now - this would typically generate JSON-LD schema for jobs
        return [];
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, $currency = 'USD')
    {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Get time ago string
     * @param string|Carbon $date
     * @return string
     */
    function timeAgo($date)
    {
        $carbon = \Carbon\Carbon::parse($date);
        return $carbon->diffForHumans();
    }
}

if (!function_exists('isActiveRoute')) {
    /**
     * Check if route is active
     * @param string $route
     * @return bool
     */
    function isActiveRoute($route)
    {
        return request()->routeIs($route);
    }
}

if (!function_exists('truncateText')) {
    /**
     * Truncate text
     * @param string $text
     * @param int $length
     * @return string
     */
    function truncateText($text, $length = 100)
    {
        return Str::limit($text, $length);
    }
} 