<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class ExtractSvgComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:extract {--dir=resources/views : Directory to search for blade files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract inline SVG elements from blade files into SVG components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchDir = $this->option('dir');
        $this->info("Searching for inline SVGs in $searchDir...");
        
        $componentsDir = 'resources/views/components/icons';
        if (!File::isDirectory($componentsDir)) {
            File::makeDirectory($componentsDir, 0755, true);
            $this->info("Created SVG components directory: $componentsDir");
        }
        
        // Get all blade files
        $bladeFiles = File::glob("$searchDir/**/*.blade.php");
        $this->info("Found " . count($bladeFiles) . " blade files.");
        
        $extractedCount = 0;
        $svgComponents = [];
        
        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            $matches = [];
            preg_match_all('/<svg[^>]*>.*?<\/svg>/s', $content, $matches);
            
            if (count($matches[0]) > 0) {
                $this->info("Found " . count($matches[0]) . " SVGs in $file");
                
                foreach ($matches[0] as $svgIndex => $svg) {
                    // Generate a component name based on the file and index
                    $fileBaseName = basename($file, '.blade.php');
                    $componentName = Str::kebab($fileBaseName) . '-' . ($svgIndex + 1);
                    
                    // Try to extract a more meaningful name from path attribute or id
                    $doc = new DOMDocument();
                    @$doc->loadHTML($svg);
                    $xpath = new DOMXPath($doc);
                    
                    // Look for path with a descriptive d attribute
                    $paths = $xpath->query('//path');
                    foreach ($paths as $path) {
                        if ($path->hasAttribute('d')) {
                            $d = $path->getAttribute('d');
                            
                            // Try to extract a name based on common SVG paths
                            if (Str::contains(strtolower($d), ['m18 6h-14v-1.5', 'h14v-1.5'])) {
                                $componentName = 'building';
                            } elseif (Str::contains(strtolower($d), ['10 0c4.48', '10 17.2c7.5'])) {
                                $componentName = 'company';
                            } elseif (Str::contains(strtolower($d), ['8.99955 19.0002c', 'm8.99955 19.0002'])) {
                                $componentName = 'location';
                            } elseif (Str::contains(strtolower($d), ['m15 8h-1v-2'])) {
                                $componentName = 'calendar';
                            } elseif (Str::contains(strtolower($d), ['m10 12v-5'])) {
                                $componentName = 'clock';
                            } elseif (Str::contains(strtolower($d), ['m13 8v-6'])) {
                                $componentName = 'currency';
                            }
                        }
                    }
                    
                    // Also try to find a descriptive id or class
                    $svgNode = $xpath->query('//svg')[0];
                    if ($svgNode && $svgNode->hasAttribute('id')) {
                        $id = $svgNode->getAttribute('id');
                        if (preg_match('/icon-(\w+)/', $id, $idMatches)) {
                            $componentName = $idMatches[1];
                        }
                    }
                    
                    // Clean the SVG: remove hardcoded classes, widths, etc.
                    $svg = preg_replace('/(class|id|stroke|fill)="[^"]*"/', '', $svg);
                    $svg = preg_replace('/<svg\s+/', '<svg {{ $attributes->merge([\'class\' => \'\']) }} ', $svg);
                    
                    // Replace fixed colors with currentColor
                    $svg = preg_replace('/#[0-9A-Fa-f]{3,6}/', 'currentColor', $svg);
                    
                    // Store in the array
                    $svgComponents[$componentName] = $svg;
                }
            }
        }
        
        // Now save all components
        foreach ($svgComponents as $name => $svg) {
            $filename = Str::kebab($name) . '.blade.php';
            $componentPath = "$componentsDir/$filename";
            
            // Check if component already exists
            if (File::exists($componentPath)) {
                if (!$this->confirm("Component $filename already exists. Overwrite?", false)) {
                    continue;
                }
            }
            
            File::put($componentPath, $svg);
            $this->info("Created SVG component: $filename");
            $extractedCount++;
        }
        
        $this->info("Extracted $extractedCount SVG components into $componentsDir");
    }
} 