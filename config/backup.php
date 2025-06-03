<?php
return [
    'backup' => [
        'name' => env('APP_NAME', 'Laravel'),
        'source' => [
            'files' => [
                'include' => [],
                'exclude' => [],
                'followLinks' => false,
            ],
            'databases' => [],
        ],
        'database_dump_compressor' => null,
        'destination' => [
            'filename_prefix' => '',
            'disks' => [],
        ],
        'temporary_directory' => storage_path('app/backup-temp'),
    ],
    'notifications' => [
        'notifications' => [],
        'notifiable' => [],
    ],
    'monitor_backups' => [],
    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];
