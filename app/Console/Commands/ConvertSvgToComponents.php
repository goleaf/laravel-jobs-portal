<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConvertSvgToComponents extends Command
{
    protected $signature = 'svg:convert {--path=resources/views : Path to search for SVG elements}';
    protected $description = 'Convert SVG elements to icon components';

    // Common SVG icons and their component mappings
    protected $svgMappings = [
        // Search icon
        '/<svg[^>]*class="[^"]*"[^>]*viewBox="0 0 20 20"[^>]*>.*?<path[^>]*d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.search class="$1" />',
        
        // Edit icon
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.edit class="$1" />',
        
        // Delete icon
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.delete class="$1" />',
        
        // View icon
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"[^>]*><\/path>.*?<path[^>]*d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.view class="$1" />',
        
        // Add icon
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.add class="$1" />',
        
        // Chevron down
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.chevron-down class="$1" />',
        
        // Chevron right
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.chevron-right class="$1" />',
        
        // User icon
        '/<svg[^>]*class="([^"]*)"[^>]*>.*?<path[^>]*d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"[^>]*><\/path>.*?<\/svg>/s' => '<x-icons.user class="$1" />',
    ];

    public function handle()
    {
        $path = $this->option('path');
        
        if (!File::exists($path)) {
            $this->error("Path {$path} does not exist!");
            return Command::FAILURE;
        }
        
        $this->info("Searching for SVG elements in {$path}...");
        
        // Get all blade files
        $files = File::glob("{$path}/**/*.blade.php");
        
        $totalFiles = count($files);
        $modifiedFiles = 0;
        $totalReplacements = 0;
        
        $this->info("Found {$totalFiles} blade files to process.");
        
        foreach ($files as $file) {
            // Skip icon component files
            if (Str::contains($file, 'components/icons')) {
                continue;
            }
            
            $content = File::get($file);
            $originalContent = $content;
            $replacements = 0;
            
            foreach ($this->svgMappings as $pattern => $replacement) {
                $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
                
                if ($count > 0) {
                    $content = $newContent;
                    $replacements += $count;
                }
            }
            
            if ($content !== $originalContent) {
                File::put($file, $content);
                $modifiedFiles++;
                $totalReplacements += $replacements;
                $this->info("Modified {$file} - {$replacements} replacements");
            }
        }
        
        $this->info("Conversion completed. Modified {$modifiedFiles} files with {$totalReplacements} replacements.");
        
        return Command::SUCCESS;
    }
} 