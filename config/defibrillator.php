<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Defibrillator Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the Laravel Defibrillator system
    | that monitors your job portal application's health and ensures it
    | maintains a normal rhythm.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Health Check Intervals
    |--------------------------------------------------------------------------
    */
    'intervals' => [
        'quick_check' => 10, // minutes
        'full_check' => 5,   // minutes
        'cache_ttl' => 600,  // seconds (10 minutes)
    ],

    /*
    |--------------------------------------------------------------------------
    | Thresholds
    |--------------------------------------------------------------------------
    */
    'thresholds' => [
        'database_connection_time' => 2000, // milliseconds
        'memory_usage_warning' => 80,       // percentage
        'memory_usage_critical' => 95,      // percentage
        'queue_backlog_warning' => 100,     // jobs
        'queue_backlog_critical' => 500,    // jobs
        'stuck_jobs_threshold' => 10,       // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Portal Specific Settings
    |--------------------------------------------------------------------------
    */
    'job_portal' => [
        'monitor_applications' => true,
        'monitor_job_postings' => true,
        'monitor_user_registrations' => true,
        'monitor_company_activity' => true,
        'daily_application_threshold' => 10, // minimum expected per day
    ],

    /*
    |--------------------------------------------------------------------------
    | System Rhythm Definitions
    |--------------------------------------------------------------------------
    */
    'rhythm' => [
        'normal_sinus' => [
            'status' => 'healthy',
            'max_issues' => 0,
            'max_warnings' => 0,
        ],
        'sinus_with_irregularities' => [
            'status' => 'healthy',
            'max_issues' => 0,
            'max_warnings' => 3,
        ],
        'arrhythmia' => [
            'status' => 'critical',
            'any_issues' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Components
    |--------------------------------------------------------------------------
    */
    'monitors' => [
        'database' => [
            'enabled' => true,
            'checks' => ['connection', 'performance', 'integrity'],
        ],
        'queue' => [
            'enabled' => true,
            'checks' => ['pending', 'failed', 'stuck'],
        ],
        'cache' => [
            'enabled' => true,
            'checks' => ['connectivity', 'performance'],
        ],
        'storage' => [
            'enabled' => true,
            'checks' => ['connectivity', 'space'],
        ],
        'memory' => [
            'enabled' => true,
            'checks' => ['usage', 'leaks'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Configuration
    |--------------------------------------------------------------------------
    */
    'alerts' => [
        'enabled' => env('DEFIBRILLATOR_ALERTS_ENABLED', false),
        'channels' => ['log', 'mail'], // Available: log, mail, slack, webhook
        'recipients' => [
            'mail' => env('DEFIBRILLATOR_ALERT_EMAIL'),
        ],
        'throttle' => 300, // seconds between duplicate alerts
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Repair Configuration
    |--------------------------------------------------------------------------
    */
    'auto_repair' => [
        'enabled' => env('DEFIBRILLATOR_AUTO_REPAIR', false),
        'actions' => [
            'clear_failed_jobs' => true,
            'restart_queue_workers' => true,
            'cleanup_expired_jobs' => true,
            'clear_cache' => false, // Can be disruptive
        ],
        'safety_limits' => [
            'max_repairs_per_hour' => 3,
            'require_confirmation' => ['clear_cache', 'restart_services'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'enabled' => true,
        'levels' => [
            'basic' => [
                'routes' => ['web'],
                'block_on_critical' => false,
            ],
            'heavy' => [
                'routes' => ['admin.reports.*', 'admin.bulk.*'],
                'block_on_critical' => true,
                'block_on_warnings' => ['High memory usage', 'High queue backlog'],
            ],
            'critical' => [
                'routes' => ['api.jobs.apply', 'payment.*'],
                'block_on_critical' => true,
                'block_on_any_warning' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Configuration
    |--------------------------------------------------------------------------
    */
    'reporting' => [
        'history_retention' => 30, // days
        'metrics_collection' => true,
        'performance_tracking' => true,
        'trend_analysis' => true,
    ],
];
