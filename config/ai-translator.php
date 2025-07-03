<?php

return [
    // Language file directory. 'lang' for Laravel.
    'source_directory' => 'lang',

    'ai' => [
        'provider' => 'anthropic',
        'model' => 'claude-3-5-sonnet-latest', // Best result. Recommend for production.
        'api_key' => env('ANTHROPIC_API_KEY'),

        // claude-3-haiku
        // 'provider' => 'anthropic',
        // 'model' => 'claude-3-haiku-20240307', // Recommend to use for testing purpose. It's better than gpt-3.5
        // 'api_key' => env('ANTHROPIC_API_KEY'),

        // gpt-4o
        // 'provider' => 'openai',
        // 'model' => 'gpt-4o', // Balanced. Normal price, normal accuracy. Recommend for production.
        // 'api_key' => env('OPENAI_API_KEY'),

        // gpt-4o-mini
        // 'provider' => 'openai',
        // 'model' => 'gpt-4o-mini', // Recommend to use for testing purpose. It sometimes doesn't translate.
        // 'api_key' => env('OPENAI_API_KEY'),

        // gemini-2.5-pro-preview-05-06
        // 'provider' => 'gemini',
        // 'model' => 'gemini-2.5-pro-preview-05-06',
        // 'api_key' => env('GEMINI_API_KEY'),

        // Additional options for better translation quality
        'retries' => 5,
        'max_tokens' => 4096,
        'disable_stream' => true, // Disable streaming mode for better error messages

        // 'prompt_custom_system_file_path' => null, // Full path to your own custom prompt-system.txt - i.e. resource_path('prompt-system.txt')
        // 'prompt_custom_user_file_path' => null, // Full path to your own custom prompt-user.txt - i.e. resource_path('prompt-user.txt')
    ],

    // 'disable_plural' => true,
    // 'skip_locales' => [],
    // 'skip_files' => [],

    // If set to true, translations will be saved as flat arrays using dot notation keys. If set to false, translations will be saved as multi-dimensional arrays.
    'dot_notation' => true,

    // Custom locale names for our multilingual job portal
    'locale_names' => [
        'en' => 'English',
        'hi' => 'Hindi',
        'ja' => 'Japanese',
        'it' => 'Italian',
        'es' => 'Spanish',
        'zh' => 'Chinese (Simplified)',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'ar' => 'Arabic',
        'fr' => 'French',
        'de' => 'German',
        'tr' => 'Turkish',
    ],

    // Translation rules for job portal context
    'additional_rules' => [
        'default' => [
            '- This is a job portal/recruitment website translation',
            '- Use professional and formal tone appropriate for job seekers and employers',
            '- Maintain consistency in job-related terminology',
            "- Keep technical terms like 'API', 'dashboard', 'profile' in original form when appropriate",
        ],
        'hi' => [
            '- Use formal Hindi appropriate for professional job portal context',
            '- Keep English technical terms where commonly used in Indian professional context',
            '- Use Devanagari script',
            '- Maintain respectful tone suitable for job seekers and employers',
        ],
        'ja' => [
            '- Use polite Japanese (丁寧語) appropriate for business context',
            '- Use appropriate keigo (honorific language) for professional situations',
            '- Keep some English technical terms in katakana when commonly used',
            '- Maintain professional tone suitable for recruitment industry',
        ],
        'it' => [
            '- Use professional Italian appropriate for business context',
            '- Maintain formal tone suitable for job portal and recruitment',
            '- Use appropriate business terminology',
            '- Keep consistency with Italian employment and HR terminology',
        ],
        'es' => [
            '- Use professional Spanish suitable for international job portal',
            '- Maintain formal tone appropriate for business context',
            '- Use appropriate employment and recruitment terminology',
        ],
        'zh' => [
            '- Use Simplified Chinese for broader accessibility',
            '- Maintain professional tone for business context',
            '- Use appropriate Chinese employment terminology',
        ],
        'pt' => [
            '- Use professional Portuguese suitable for job portal context',
            '- Maintain formal business tone',
            '- Use appropriate employment terminology',
        ],
        'ru' => [
            '- Use professional Russian appropriate for business context',
            '- Maintain formal tone suitable for recruitment industry',
            '- Use appropriate Russian employment terminology',
        ],
        'ar' => [
            '- Use Modern Standard Arabic (MSA) for professional context',
            '- Maintain formal tone suitable for business and employment',
            '- Use appropriate Arabic employment terminology',
            '- Consider right-to-left text formatting requirements',
        ],
        'fr' => [
            '- Use professional French appropriate for business context',
            '- Maintain formal tone suitable for job portal',
            '- Use appropriate French employment and recruitment terminology',
        ],
        'de' => [
            '- Use professional German appropriate for business context',
            '- Maintain formal tone suitable for recruitment industry',
            '- Use appropriate German employment terminology',
        ],
        'tr' => [
            '- Use professional Turkish appropriate for business context',
            '- Maintain formal tone suitable for job portal',
            '- Use appropriate Turkish employment terminology',
        ],
    ],
];
