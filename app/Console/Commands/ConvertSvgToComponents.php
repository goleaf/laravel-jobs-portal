<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConvertSvgToComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:convert {--path=resources/views : Path to search for SVG elements}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert SVG elements to icon components';

    /**
     * Array of common SVG icons mapped to their component replacements
     * 
     * @var array
     */
    protected $iconMappings = [
        // Format: 'SVG pattern' => 'component name'
        'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z' => 'edit',
        'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' => 'delete',
        'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' => 'search',
        'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' => 'view',
        'M12 4v16m8-8H4' => 'add',
        'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' => 'chevron-down',
        'M9 5l7 7-7 7' => 'chevron-right',
        '22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3' => 'filter',
        'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' => 'user',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->option('path');
        
        $this->info("Searching for SVG elements in path: $path");
        
        if (!File::isDirectory($path)) {
            $this->error("The specified path does not exist or is not a directory.");
            return 1;
        }
        
        $bladeFiles = $this->findBladeFiles($path);
        $this->info("Found " . count($bladeFiles) . " Blade files to process.");
        
        $totalReplaced = 0;
        $modifiedFiles = 0;
        
        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            $originalContent = $content;
            $replacementCount = 0;
            
            foreach ($this->iconMappings as $svgPattern => $componentName) {
                // Look for SVG elements containing this pattern
                if (Str::contains($content, $svgPattern)) {
                    // Prepare regex patterns to match SVG elements
                    $svgRegex = '/<svg[^>]*>.*?' . preg_quote($svgPattern, '/') . '.*?<\/svg>/s';
                    
                    // Count occurrences
                    preg_match_all($svgRegex, $content, $matches);
                    $replacementCount += count($matches[0]);
                    
                    // Perform replacements
                    $content = $this->replaceSvgWithComponent($content, $svgPattern, $componentName);
                }
            }
            
            // Save the file if changes were made
            if ($content !== $originalContent) {
                File::put($file, $content);
                $modifiedFiles++;
                $totalReplaced += $replacementCount;
                $this->line("Modified file: $file (Replaced $replacementCount SVG elements)");
            }
        }
        
        $this->info("Conversion complete! Modified $modifiedFiles files and replaced $totalReplaced SVG elements.");
        
        return 0;
    }
    
    /**
     * Find all Blade files in the specified directory and its subdirectories.
     *
     * @param string $path
     * @return array
     */
    protected function findBladeFiles($path)
    {
        return File::glob("$path/**/*.blade.php", GLOB_BRACE);
    }
    
    /**
     * Replace SVG elements with component calls.
     *
     * @param string $content
     * @param string $svgPattern
     * @param string $componentName
     * @return string
     */
    protected function replaceSvgWithComponent($content, $svgPattern, $componentName)
    {
        // Pattern to match SVG elements containing the specific pattern
        $svgRegex = '/<svg[^>]*(?:class="([^"]*)")?[^>]*>.*?' . preg_quote($svgPattern, '/') . '.*?<\/svg>/s';
        
        return preg_replace_callback($svgRegex, function ($matches) use ($componentName) {
            // Extract class if it exists
            $class = isset($matches[1]) ? $matches[1] : 'w-5 h-5';
            
            // Create the component call
            return "<x-icons.{$componentName} class=\"{$class}\" />";
        }, $content);
    }
} 