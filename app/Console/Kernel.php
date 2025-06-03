<?php

namespace App\Console;

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
        // Comment out heavy commands during testing
        // $this->load(__DIR__.'/Commands');

        // Register only essential commands for testing
        /*
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
            \App\Console\Commands\MigrateFromSpatie::class,
        ]);
        */

        require base_path('routes/console.php');
    }
}
