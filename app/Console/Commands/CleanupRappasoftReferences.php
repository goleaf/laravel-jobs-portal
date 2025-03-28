<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupRappasoftReferences extends Command
{
    protected $signature = 'cleanup:rappasoft';
    protected $description = 'Clean up Rappasoft references in the project';

    public function handle()
    {
        $this->info('Cleaning up Rappasoft references in the project...');

        // 1. Remove rappasoft vendor directory
        if (File::exists(public_path('vendor/rappasoft'))) {
            $this->info('Removing Rappasoft public vendor directory...');
            File::deleteDirectory(public_path('vendor/rappasoft'));
            $this->info('Rappasoft public vendor directory removed.');
        }

        // 2. Check and remove livewire-tables.php config file
        if (File::exists(config_path('livewire-tables.php'))) {
            $this->info('Removing livewire-tables.php config file...');
            File::delete(config_path('livewire-tables.php'));
            $this->info('livewire-tables.php config file removed.');
        }

        // 3. Check imports in Livewire components
        $this->info('Checking Livewire components for Rappasoft imports...');
        $livewireFiles = File::glob(app_path('Livewire') . '/*.php');
        $rappaImports = 0;
        
        foreach ($livewireFiles as $file) {
            $content = File::get($file);
            
            if (strpos($content, 'Rappasoft\\') !== false) {
                $rappaImports++;
                $this->info('Found Rappasoft import in ' . basename($file));
            }
        }
        
        $this->info("Found {$rappaImports} files with Rappasoft imports. Use the 'tables:convert' command to convert them.");

        $this->info('Clean up completed successfully!');
        
        return Command::SUCCESS;
    }
} 