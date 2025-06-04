<?php

if (!function_exists('getLoggedInUserId')) {
    function getLoggedInUserId(): ?int
    {
        return auth()->id();
    }
}

if (!function_exists('settings')) {
    function settings(string $key = null, $default = null)
    {
        if ($key === null) {
            return collect([
                'app_name' => config('app.name', 'Job Portal'),
                'app_logo' => '/images/logo.png',
                'currency_symbol' => '$',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
            ]);
        }
        
        $settings = [
            'app_name' => config('app.name', 'Job Portal'),
            'app_logo' => '/images/logo.png',
            'currency_symbol' => '$',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
        ];
        
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('getAppName')) {
    function getAppName(): string
    {
        return config('app.name', 'Job Portal');
    }
}

if (!function_exists('getSettingValue')) {
    function getSettingValue(string $key, $default = null)
    {
        return settings($key, $default);
    }
}

if (!function_exists('googleJobSchema')) {
    function googleJobSchema(array $job): array
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'JobPosting',
            'title' => $job['title'] ?? '',
            'description' => $job['description'] ?? '',
            'datePosted' => $job['created_at'] ?? now()->toISOString(),
            'validThrough' => $job['expires_on'] ?? now()->addDays(30)->toISOString(),
        ];
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, string $currency = 'USD'): string
    {
        if ($amount === null || $amount === '') {
            return '$0.00';
        }
        
        return '$' . number_format((float) $amount, 2);
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($datetime): string
    {
        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }
        
        if (!$datetime instanceof \Carbon\Carbon) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }
        
        return $datetime->diffForHumans();
    }
}

if (!function_exists('truncateText')) {
    function truncateText(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
}

if (!function_exists('getSuperAdmin')) {
    function getSuperAdmin(): ?int
    {
        // For testing environment, return a default value
        if (app()->environment('testing')) {
            return 1;
        }
        
        // In production, try to find the first admin user
        $admin = \App\Models\User::where('role', 'admin')->first();
        return $admin ? $admin->id : 1;
    }
}