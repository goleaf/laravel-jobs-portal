<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Locale Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for locale switching and
    | internationalization messages throughout the application.
    |
    */

    'choose_language' => 'Choose Language',
    'current_language' => 'Current Language',
    'switch_language' => 'Switch Language',
    'language_switched_successfully' => 'Language switched successfully',
    'invalid_locale' => 'Invalid locale selected',
    'locale_not_supported' => 'The selected locale is not supported',
    'cache_cleared_successfully' => 'Translation cache cleared successfully',
    'language_settings' => 'Language Settings',
    'auto_detect_language' => 'Auto-detect language from browser',
    'default_language' => 'Default Language',
    'fallback_language' => 'Fallback Language',

    // Language names in English
    'languages' => [
        'en' => 'English',
        'ar' => 'Arabic',
        'de' => 'German',
        'es' => 'Spanish',
        'fr' => 'French',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'tr' => 'Turkish',
        'zh' => 'Chinese',
    ],

    // RTL Information
    'rtl_languages' => 'Right-to-Left Languages',
    'ltr_languages' => 'Left-to-Right Languages',
    'text_direction' => 'Text Direction',
    'left_to_right' => 'Left to Right',
    'right_to_left' => 'Right to Left',

    // Browser detection
    'browser_language_detected' => 'Browser language detected: :language',
    'browser_language_not_supported' => 'Your browser language is not supported, defaulting to :language',

    // Validation messages
    'validation' => [
        'locale_required' => 'Locale is required',
        'locale_invalid' => 'The selected locale is invalid',
        'locale_unsupported' => 'The locale :locale is not supported',
    ],

    // Success/Error messages
    'messages' => [
        'switched_to' => 'Language switched to :language',
        'failed_to_switch' => 'Failed to switch language',
        'translations_loaded' => 'Translations loaded successfully',
        'translations_failed' => 'Failed to load translations',
    ],
];
