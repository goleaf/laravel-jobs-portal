<?php

namespace App\Console;

use App\Console\Commands\DeleteExpiredFeaturedCompany;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('cache:prune-stale-tags')->hourly();
        $schedule->command('delete:expired-featured-company')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Register custom commands
        $this->commands([
            \App\Console\Commands\ConsolidateTranslations::class,
            \App\Console\Commands\CreateLithuanianTranslations::class,
            \App\Console\Commands\ExtractSvgComponents::class,
            \App\Console\Commands\ConvertRappasoftTables::class,
            \App\Console\Commands\CleanupRappasoftReferences::class,
            \App\Console\Commands\MigrateJsonTranslations::class,
            \App\Console\Commands\ConvertSvgToComponents::class,
            \App\Console\Commands\StandardizeTranslations::class,
            \App\Console\Commands\StandardizeJavaScript::class,
        ]);

        require base_path('routes/console.php');
    }
}
