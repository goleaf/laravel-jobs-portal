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
        // Load all commands
        $this->load(__DIR__.'/Commands');

        // Register translation commands
        $this->commands([
            \App\Console\Commands\ConvertBladeTranslations::class,
        ]);

        require base_path('routes/console.php');
    }
}
