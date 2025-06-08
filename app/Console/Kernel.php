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
        // Temporarily disable command loading to prevent memory issues
        // $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
