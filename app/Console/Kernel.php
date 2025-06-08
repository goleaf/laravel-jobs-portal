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
        // Temporarily disabled to fix memory issues
        // $schedule->command('cache:prune-stale-tags')->hourly();
        // $schedule->command('delete:expired-featured-company')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Temporarily disable all command loading to fix memory issues
        // $this->load(__DIR__.'/Commands');

        // Temporarily disable all commands
        // $this->commands([
        //     \App\Console\Commands\ConvertBladeTranslations::class,
        // ]);

        // Add the TranslationCommand to the commands array
        $this->commands = array_merge($this->commands, [
            Commands\TranslationCommand::class,
        ]);

        require base_path('routes/console.php');
    }
}
