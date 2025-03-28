<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupRappasoftReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rappasoft:cleanup {--path=resources/views : Path to search for Rappasoft references}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up remaining Rappasoft references in blade files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->option('path');
        $this->info("Searching for Rappasoft references in: $path");
        
        // Search for all Blade files
        $bladeFiles = File::glob("{$path}/**/*.blade.php");
        $count = 0;
        
        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            $modified = false;
            
            // Check if file contains Rappasoft references
            if (strpos($content, 'rappasoft') !== false || 
                strpos($content, 'Rappasoft') !== false ||
                strpos($content, 'wire:sortable') !== false ||
                strpos($content, 'livewire-tables') !== false) {
                
                $this->info("Found Rappasoft references in: $file");
                
                // Replace common Rappasoft classes with Tailwind equivalents
                $mappings = [
                    'table-responsive' => 'overflow-x-auto',
                    'table-striped' => 'table-striped',
                    'table-hover' => 'hover:bg-gray-50',
                    'table-bordered' => 'border',
                    'table-sm' => 'text-sm',
                    'table-primary' => 'bg-primary-50',
                    'table-secondary' => 'bg-gray-50',
                    'table-success' => 'bg-green-50',
                    'table-danger' => 'bg-red-50',
                    'table-warning' => 'bg-yellow-50',
                    'table-info' => 'bg-blue-50',
                    'table-light' => 'bg-gray-50',
                    'table-dark' => 'bg-gray-800 text-white',
                    'thead-light' => 'bg-gray-50',
                    'thead-dark' => 'bg-gray-800 text-white',
                    'livewire-datatable' => 'w-full text-sm text-left text-gray-500',
                    'wire:sortable' => '',
                    'wire:sortable.item' => '',
                    'livewire-tables-no-results' => 'text-center p-4',
                ];
                
                // Apply replacements
                foreach ($mappings as $old => $new) {
                    if (strpos($content, $old) !== false) {
                        $content = str_replace($old, $new, $content);
                        $modified = true;
                    }
                }
                
                // Remove Rappasoft specific attributes
                $content = preg_replace('/wire:sortable(?:\.[\w-]+)?="[^"]*"/m', '', $content);
                $content = preg_replace('/wire:key="table-row-[^"]*"/m', '', $content);
                
                // Remove Rappasoft component references
                $content = preg_replace('/@include\([\'"]livewire-tables::bs[^\)]*\)/m', '', $content);
                
                if ($modified) {
                    // Save the updated file
                    File::put($file, $content);
                    $this->info("Cleaned up file: $file");
                    $count++;
                }
            }
        }
        
        $this->info("Cleaned up Rappasoft references in $count files");
        
        return Command::SUCCESS;
    }
} 