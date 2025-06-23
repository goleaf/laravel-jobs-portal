<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the comprehensive translation system
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale that will be used when no other locale is specified.
    |
    */
    'default_locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale is used when the current locale translation is not
    | available. This ensures that users always see some text rather than
    | translation keys.
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Translation Cache
    |--------------------------------------------------------------------------
    |
    | Cache configuration for translations to improve performance.
    |
    */
    'cache' => [
        'enabled' => env('TRANSLATION_CACHE_ENABLED', true),
        'ttl' => env('TRANSLATION_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('TRANSLATION_CACHE_PREFIX', 'translations'),
        'store' => env('TRANSLATION_CACHE_STORE', 'redis'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Loading
    |--------------------------------------------------------------------------
    |
    | Configuration for how translations are loaded and processed.
    |
    */
    'loading' => [
        'lazy' => env('TRANSLATION_LAZY_LOADING', true),
        'preload_namespaces' => ['common', 'messages', 'validation', 'auth'],
        'max_file_size' => env('TRANSLATION_MAX_FILE_SIZE', 1048576), // 1MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Validation
    |--------------------------------------------------------------------------
    |
    | Settings for translation validation and missing key detection.
    |
    */
    'validation' => [
        'enabled' => env('TRANSLATION_VALIDATION_ENABLED', true),
        'log_missing_keys' => env('TRANSLATION_LOG_MISSING_KEYS', true),
        'strict_mode' => env('TRANSLATION_STRICT_MODE', false),
        'report_missing_keys' => env('TRANSLATION_REPORT_MISSING_KEYS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Management
    |--------------------------------------------------------------------------
    |
    | Settings for translation management tools and admin interface.
    |
    */
    'management' => [
        'enabled' => env('TRANSLATION_MANAGEMENT_ENABLED', true),
        'auto_sync' => env('TRANSLATION_AUTO_SYNC', false),
        'backup_enabled' => env('TRANSLATION_BACKUP_ENABLED', true),
        'backup_path' => env('TRANSLATION_BACKUP_PATH', 'storage/app/translation-backups'),
        'export_formats' => ['json', 'php', 'csv'],
        'import_validation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | RTL Languages
    |--------------------------------------------------------------------------
    |
    | List of locales that use right-to-left text direction.
    |
    */
    'rtl_languages' => ['ar', 'he', 'fa', 'ur'],

    /*
    |--------------------------------------------------------------------------
    | Locale Detection
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic locale detection.
    |
    */
    'detection' => [
        'browser_detection' => env('TRANSLATION_BROWSER_DETECTION', true),
        'cookie_persistence' => env('TRANSLATION_COOKIE_PERSISTENCE', true),
        'cookie_name' => env('TRANSLATION_COOKIE_NAME', 'preferred_locale'),
        'cookie_lifetime' => env('TRANSLATION_COOKIE_LIFETIME', 43200), // 30 days
        'geo_detection' => env('TRANSLATION_GEO_DETECTION', false),
        'user_preference' => env('TRANSLATION_USER_PREFERENCE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Performance optimization settings for the translation system.
    |
    */
    'performance' => [
        'enable_opcache' => env('TRANSLATION_OPCACHE', true),
        'compress_cache' => env('TRANSLATION_COMPRESS_CACHE', false),
        'prefetch_translations' => env('TRANSLATION_PREFETCH', true),
        'minimize_json' => env('TRANSLATION_MINIMIZE_JSON', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security configuration for translation system.
    |
    */
    'security' => [
        'validate_locale_format' => true,
        'sanitize_translation_keys' => true,
        'escape_translation_values' => false, // Set to true if storing user content
        'rate_limit_enabled' => env('TRANSLATION_RATE_LIMIT', true),
        'rate_limit_attempts' => env('TRANSLATION_RATE_LIMIT_ATTEMPTS', 60),
        'rate_limit_decay' => env('TRANSLATION_RATE_LIMIT_DECAY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings
    |--------------------------------------------------------------------------
    |
    | Debug and development settings for translation system.
    |
    */
    'debug' => [
        'enabled' => env('TRANSLATION_DEBUG', false),
        'show_missing_keys' => env('TRANSLATION_SHOW_MISSING_KEYS', false),
        'log_translation_usage' => env('TRANSLATION_LOG_USAGE', false),
        'profile_performance' => env('TRANSLATION_PROFILE_PERFORMANCE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Integration
    |--------------------------------------------------------------------------
    |
    | Settings for frontend JavaScript integration.
    |
    */
    'frontend' => [
        'enabled' => env('TRANSLATION_FRONTEND_ENABLED', true),
        'expose_all_translations' => env('TRANSLATION_EXPOSE_ALL', false),
        'expose_namespaces' => ['common', 'messages', 'validation'],
        'cache_frontend_translations' => env('TRANSLATION_CACHE_FRONTEND', true),
        'minify_frontend_translations' => env('TRANSLATION_MINIFY_FRONTEND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Formatting Settings
    |--------------------------------------------------------------------------
    |
    | Locale-specific formatting settings for dates, numbers, and currency.
    |
    */
    'formatting' => [
        'auto_format_dates' => env('TRANSLATION_AUTO_FORMAT_DATES', true),
        'auto_format_numbers' => env('TRANSLATION_AUTO_FORMAT_NUMBERS', true),
        'auto_format_currency' => env('TRANSLATION_AUTO_FORMAT_CURRENCY', true),
        'use_intl_extension' => env('TRANSLATION_USE_INTL', true),
    ],
];
