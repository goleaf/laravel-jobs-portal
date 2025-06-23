<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Portal Unique Values Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for generating unique values
    | specific to the job portal application using Laravel Unique Values package.
    |
    */

    'scopes' => [
        'job_references' => [
            'prefix' => 'JOB',
            'format' => '{prefix}-{year}-{counter}',
            'attempts' => 10,
            'counter_start' => 1000,
            'counter_padding' => 4,
        ],

        'application_references' => [
            'prefix' => 'APP',
            'format' => '{prefix}-{date}-{job_id}-{candidate_id}',
            'attempts' => 5,
            'use_subject' => true,
        ],

        'company_slugs' => [
            'attempts' => 20,
            'use_subject' => true,
            'format' => '{slug}',
        ],

        'user_references' => [
            'prefixes' => [
                'candidate' => 'CAN',
                'employer' => 'EMP',
                'admin' => 'ADM',
            ],
            'format' => '{prefix}-{date}-{random}-{counter}',
            'attempts' => 15,
            'random_length' => 3,
            'counter_padding' => 3,
        ],

        'job_slugs' => [
            'attempts' => 15,
            'format' => '{slug}',
            'include_company_id' => true,
        ],

        'api_keys' => [
            'prefix' => 'jp',
            'format' => '{prefix}_{timestamp}_{company_id}_{random}',
            'attempts' => 3,
            'random_length' => 32,
            'use_subject' => true,
        ],

        'invoice_numbers' => [
            'prefix' => 'INV',
            'format' => '{prefix}-{year}{month}-{company_id}-{counter}',
            'attempts' => 10,
            'counter_start' => 1000,
            'counter_padding' => 4,
        ],

        'verification_tokens' => [
            'format' => '{type}-{timestamp}-{user_id}-{random}',
            'attempts' => 3,
            'random_length' => 16,
            'use_subject' => true,
        ],

        'payment_references' => [
            'prefix' => 'PAY',
            'format' => '{prefix}-{plan_code}-{timestamp}-{company_id}-{counter}',
            'attempts' => 8,
            'counter_padding' => 3,
        ],

        'interview_codes' => [
            'prefix' => 'INT',
            'format' => '{prefix}-{date}-{application_id}-{random}',
            'attempts' => 5,
            'random_length' => 6,
            'use_subject' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'attempts' => 5,
        'random_length' => 8,
        'counter_padding' => 3,
        'date_format' => 'ymd',
        'timestamp_format' => 'ymdHi',
        'year_format' => 'Y',
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Portal Specific Settings
    |--------------------------------------------------------------------------
    */
    'job_portal' => [
        'enable_reference_tracking' => true,
        'enable_slug_generation' => true,
        'enable_api_key_management' => true,
        'enable_verification_tokens' => true,
        
        'reference_formats' => [
            'short' => '{prefix}-{counter}',
            'standard' => '{prefix}-{year}-{counter}',
            'detailed' => '{prefix}-{year}-{month}-{counter}',
            'timestamped' => '{prefix}-{timestamp}-{counter}',
        ],
        
        'slug_strategies' => [
            'simple' => 'base_slug_only',
            'incremental' => 'append_number',
            'company_based' => 'include_company_id',
            'hash_based' => 'append_hash',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'job_reference' => 'required|string|min:8|max:20|unique:jobs,job_reference',
        'application_reference' => 'required|string|min:10|max:30',
        'company_slug' => 'required|string|min:3|max:100|unique:companies,slug',
        'job_slug' => 'required|string|min:3|max:150|unique:jobs,slug',
        'api_key' => 'required|string|min:40|max:80|unique:company_api_keys,key',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache_generated_values' => true,
        'cache_ttl' => 3600, // 1 hour
        'batch_generation_limit' => 100,
        'concurrent_generation_limit' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Logging
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'log_generation_attempts' => false,
        'log_failed_generations' => true,
        'track_usage_statistics' => true,
        'alert_on_high_attempts' => true,
        'high_attempts_threshold' => 8,
    ],
]; 