<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Supported Locales
    |--------------------------------------------------------------------------
    |
    | Contains all languages your application supports.
    |
    */
    'supported_languages' => [
        'en' => [
            'display' => 'English',
            'native' => 'English',
            'is_rtl' => false,
        ],
        'lt' => [
            'display' => 'Lithuanian',
            'native' => 'Lietuvių',
            'is_rtl' => false,
        ],
        'es' => [
            'display' => 'Spanish',
            'native' => 'Español',
            'is_rtl' => false,
        ],
        'fr' => [
            'display' => 'French',
            'native' => 'Français',
            'is_rtl' => false,
        ],
        'de' => [
            'display' => 'German',
            'native' => 'Deutsch',
            'is_rtl' => false,
        ],
        'ru' => [
            'display' => 'Russian',
            'native' => 'Русский',
            'is_rtl' => false,
        ],
        'ar' => [
            'display' => 'Arabic',
            'native' => 'العربية',
            'is_rtl' => true,
        ],
        'zh' => [
            'display' => 'Chinese',
            'native' => '中文',
            'is_rtl' => false,
        ],
        'pt' => [
            'display' => 'Portuguese',
            'native' => 'Português',
            'is_rtl' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale of your application.
    | This will be used by the default Laravel's localization middleware.
    |
    */
    'default_language' => 'en',
]; 