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
    protected $signature = 'rappasoft:cleanup {--path=resources/views : Path to search for Rappasoft references} {--fix-translations=1 : Fix old translation keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up remaining Rappasoft references in blade files and fix translation keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->option('path');
        $fixTranslations = (bool) $this->option('fix-translations');
        
        if (!File::isDirectory($path)) {
            $this->error("Path {$path} is not a valid directory.");
            return 1;
        }
        
        $this->info("Searching for Rappasoft references in: $path");
        
        $files = File::glob("$path/**/*.blade.php", GLOB_BRACE);
        $this->info("Found " . count($files) . " Blade files.");
        
        $count = 0;
        
        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;
            
            // Check if file contains Rappasoft references
            if (strpos($content, 'rappasoft') !== false ||
                strpos($content, 'Rappasoft') !== false ||
                strpos($content, 'wire:sortable') !== false ||
                strpos($content, 'wire:sorted') !== false) {
                
                $this->info("Found Rappasoft references in: $file");
                
                // Replace common Rappasoft classes with Tailwind equivalents
                $content = preg_replace('/wire:sortable/', 'data-sortable', $content);
                $content = preg_replace('/wire:sorted/', 'data-sorted', $content);
                
                // Replace Rappasoft table classes with our custom ones
                $content = preg_replace('/w-full whitespace-no-wrap/', 'w-full whitespace-nowrap', $content);
                $content = preg_replace('/w-full overflow-hidden/', 'w-full overflow-x-auto', $content);
                
                // Replace pagination styles
                $content = preg_replace('/pagination-links/', 'pagination flex justify-center mt-4', $content);
                
                // Remove Rappasoft specific attributes
                $content = preg_replace('/ wire:key="[^"]*"/', '', $content);
                $content = preg_replace('/ wire:loading[^>]*/', '', $content);
                
                // Remove Rappasoft component references
                $content = preg_replace('/<livewire:tables[^>]*>/', '<div class="table-responsive">', $content);
                $content = preg_replace('/<\/livewire:tables[^>]*>/', '</div>', $content);
                
                if ($content !== $originalContent) {
                    File::put($file, $content);
                    $count++;
                }
            }
            
            // Fix translation keys if enabled
            if ($fixTranslations) {
                $content = File::get($file);
                $originalContent = $content;
                
                // Replace old translation keys with new ones
                $content = preg_replace('/__\([\'"]messages\.common\.([^\'"]*)[\'"]/', '__(\'"common.$1"', $content);
                $content = preg_replace('/__\("messages\.common\.([^"]*)"/', '__("common.$1"', $content);
                
                // Fix other translation namespaces
                $content = preg_replace('/__\([\'"]messages\.job\.([^\'"]*)[\'"]/', '__(\'"job.$1"', $content);
                $content = preg_replace('/__\("messages\.job\.([^"]*)"/', '__("job.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.company\.([^\'"]*)[\'"]/', '__(\'"company.$1"', $content);
                $content = preg_replace('/__\("messages\.company\.([^"]*)"/', '__("company.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.filter_name\.([^\'"]*)[\'"]/', '__(\'"filter.$1"', $content);
                $content = preg_replace('/__\("messages\.filter_name\.([^"]*)"/', '__("filter.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.pagination\.([^\'"]*)[\'"]/', '__(\'"pagination.$1"', $content);
                $content = preg_replace('/__\("messages\.pagination\.([^"]*)"/', '__("pagination.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.candidate\.([^\'"]*)[\'"]/', '__(\'"candidate.$1"', $content);
                $content = preg_replace('/__\("messages\.candidate\.([^"]*)"/', '__("candidate.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.flash\.([^\'"]*)[\'"]/', '__(\'"flash.$1"', $content);
                $content = preg_replace('/__\("messages\.flash\.([^"]*)"/', '__("flash.$1"', $content);
                
                $content = preg_replace('/__\([\'"]messages\.setting\.([^\'"]*)[\'"]/', '__(\'"settings.$1"', $content);
                $content = preg_replace('/__\("messages\.setting\.([^"]*)"/', '__("settings.$1"', $content);
                
                if ($content !== $originalContent) {
                    File::put($file, $content);
                    $this->info("Updated translation keys in: $file");
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            $this->info("Cleaned up Rappasoft references and fixed translations in $count files");
        } else {
            $this->info("No files needed cleaning");
        }
        
        return 0;
    }
} 