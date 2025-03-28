<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtractSvgComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract SVG code from blade files into separate components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SVG extraction...');
        
        // Create components directory if it doesn't exist
        $componentsDir = resource_path('views/components/icons');
        if (!File::exists($componentsDir)) {
            File::makeDirectory($componentsDir, 0755, true);
            $this->info("Created components directory: {$componentsDir}");
        }
        
        // Get all blade files
        $bladeFiles = $this->getAllBladeFiles(resource_path('views'));
        $this->info('Found ' . count($bladeFiles) . ' blade files to process');
        
        // Patterns to match SVG code
        $svgPattern = '/<svg\b[^>]*>(.*?)<\/svg>/is';
        
        $extractedCount = 0;
        $replacedCount = 0;
        
        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            
            // Extract SVG code
            if (preg_match_all($svgPattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $svgCode = $match[0];
                    $extractedCount++;
                    
                    // Generate a unique name for the SVG component
                    $name = $this->generateComponentName($file, $extractedCount);
                    
                    // Create component file
                    $componentPath = "{$componentsDir}/{$name}.blade.php";
                    File::put($componentPath, $svgCode);
                    $this->info("Created SVG component: {$name}.blade.php");
                    
                    // Replace SVG code with component include
                    $componentInclude = "<x-icons.{$name} />";
                    $content = str_replace($svgCode, $componentInclude, $content);
                    $replacedCount++;
                }
                
                // Save modified content
                File::put($file, $content);
            }
        }
        
        $this->info("Extraction complete. Extracted {$extractedCount} SVGs and replaced {$replacedCount} instances.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get all blade files in a directory recursively.
     *
     * @param string $directory
     * @return array
     */
    private function getAllBladeFiles(string $directory): array
    {
        $files = [];
        
        foreach (File::allFiles($directory) as $file) {
            if ($file->getExtension() === 'php' && Str::endsWith($file->getRelativePathname(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
    
    /**
     * Generate a unique name for the SVG component.
     *
     * @param string $file
     * @param int $count
     * @return string
     */
    private function generateComponentName(string $file, int $count): string
    {
        $baseFilename = pathinfo($file, PATHINFO_FILENAME);
        $directory = pathinfo($file, PATHINFO_DIRNAME);
        $dirName = basename($directory);
        
        // Remove common prefixes and suffixes
        $baseFilename = Str::replaceFirst('index', '', $baseFilename);
        $baseFilename = Str::replaceFirst('show', '', $baseFilename);
        $baseFilename = Str::replaceFirst('edit', '', $baseFilename);
        $baseFilename = Str::replaceFirst('create', '', $baseFilename);
        
        // Create a name based on directory and filename
        $name = Str::slug("{$dirName}-{$baseFilename}-{$count}", '-');
        
        // Ensure the name is unique
        $componentsDir = resource_path('views/components/icons');
        $i = 1;
        $originalName = $name;
        
        while (File::exists("{$componentsDir}/{$name}.blade.php")) {
            $name = "{$originalName}-{$i}";
            $i++;
        }
        
        return $name;
    }
} 