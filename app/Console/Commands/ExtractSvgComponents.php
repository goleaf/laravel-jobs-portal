<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class ExtractSvgComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:extract {--dir=resources/views : Directory to search for blade files} {--chunk-size=5 : Number of files to process in each chunk} {--single-file= : Process a specific blade file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract inline SVG elements from blade files into SVG components';

    /**
     * The target directory for SVG components
     */
    protected $componentsDir = 'resources/views/components/icons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create components directory if it doesn't exist
        if (!File::isDirectory($this->componentsDir)) {
            File::makeDirectory($this->componentsDir, 0755, true);
            $this->info("Created SVG components directory: {$this->componentsDir}");
        }

        // Process a single file if specified
        if ($singleFile = $this->option('single-file')) {
            if (File::exists($singleFile) && Str::endsWith($singleFile, '.blade.php')) {
                $this->processFile($singleFile);
                return Command::SUCCESS;
            } else {
                $this->error("File not found or not a blade file: {$singleFile}");
                return Command::FAILURE;
            }
        }

        // Process multiple files from directory
        $searchDir = $this->option('dir');
        $chunkSize = (int) $this->option('chunk-size');
        
        if (!File::isDirectory($searchDir)) {
            $this->error("Directory not found: {$searchDir}");
            return Command::FAILURE;
        }
        
        $this->info("Searching for inline SVGs in {$searchDir}...");
        
        // Find all blade files in chunks to avoid memory issues
        $this->processDirectoryInChunks($searchDir, $chunkSize);
        
        $this->info("SVG extraction completed.");
        return Command::SUCCESS;
    }
    
    /**
     * Process a directory in chunks
     */
    protected function processDirectoryInChunks(string $directory, int $chunkSize): void
    {
        $pattern = $directory . '/**/*.blade.php';
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->name('*.blade.php')->in($directory);
        
        $totalFiles = iterator_count($finder);
        $this->info("Found {$totalFiles} blade files. Processing in chunks of {$chunkSize}...");
        
        $processedFiles = 0;
        $currentChunk = [];
        $chunkCount = 0;
        
        foreach ($finder as $file) {
            $currentChunk[] = $file->getPathname();
            $processedFiles++;
            
            if (count($currentChunk) >= $chunkSize || $processedFiles === $totalFiles) {
                $chunkCount++;
                $this->info("Processing chunk {$chunkCount} ({$processedFiles}/{$totalFiles} files)...");
                
                foreach ($currentChunk as $filePath) {
                    $this->processFile($filePath);
                }
                
                $currentChunk = [];
                gc_collect_cycles();
            }
        }
    }
    
    /**
     * Process a single file to extract SVGs
     */
    protected function processFile(string $filePath): void
    {
        $this->info("Processing file: {$filePath}");
        
        try {
            $content = File::get($filePath);
            $matches = [];
            preg_match_all('/<svg[^>]*>.*?<\/svg>/s', $content, $matches);
            
            if (empty($matches[0])) {
                return;
            }
            
            $this->info("  Found " . count($matches[0]) . " SVGs");
            $fileName = basename($filePath, '.blade.php');
            
            foreach ($matches[0] as $index => $svg) {
                $componentName = $this->generateComponentName($filePath, $svg, $index);
                $cleanedSvg = $this->cleanSvg($svg);
                
                $this->saveSvgComponent($componentName, $cleanedSvg);
            }
        } catch (\Exception $e) {
            $this->error("  Error processing file {$filePath}: " . $e->getMessage());
        }
    }
    
    /**
     * Generate a component name based on the file and SVG content
     */
    protected function generateComponentName(string $filePath, string $svg, int $index): string
    {
        $fileName = basename($filePath, '.blade.php');
        $componentName = Str::kebab($fileName) . '-' . ($index + 1);
        
        // Try to extract a meaningful name from SVG content or file path
        // Check for common icon patterns in the path attribute
        if (preg_match('/d="([^"]+)"/', $svg, $pathMatches)) {
            $path = $pathMatches[1];
            
            // Map common SVG path patterns to meaningful names
            $iconPatterns = [
                'building' => ['/M18 6H|h14v-1.5/i'],
                'user' => ['/M10 0C4.48|circle.*10 11/i'],
                'location' => ['/8.9+\s+19|c8.9+\s+19/i'],
                'calendar' => ['/M15 8h-1v-2/i'],
                'clock' => ['/M10 12v-5/i'],
                'search' => ['/M\s*21\s+21l\-6\-6|m15.5 15.5l-5.2-5.2/i'],
                'edit' => ['/M16 3l5 5|M4 20h4l10-10/i'],
                'delete' => ['/M6 19c0 1.1.9|M19 7l-7 7/i'],
                'close' => ['/M6 18L18 6|M18 18L6 6/i'],
                'check' => ['/M9 16.17L4.83 12|M5 9l4 4/i'],
                'arrow-right' => ['/M14 5l7 7|m0 0l-7 7/i'],
                'arrow-left' => ['/M10 19l-7-7|m0 0l7-7/i'],
                'chevron-down' => ['/M19 9l-7 7-7-7/i'],
                'chevron-up' => ['/M5 15l7-7 7 7/i'],
                'plus' => ['/M12 4v16|M4 12h16/i'],
                'minus' => ['/M4 12h16/i'],
                'filter' => ['/M3 6h18|m-18 6h18/i'],
                'sort' => ['/M3 6h18|m-18 12h18/i']
            ];
            
            foreach ($iconPatterns as $name => $patterns) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $path)) {
                        $componentName = $name;
                        break 2;
                    }
                }
            }
        }
        
        // Check for SVG ID or title
        if (preg_match('/id=["\'](icon-)?([^"\'\s]+)["\']/', $svg, $idMatches)) {
            $id = $idMatches[2];
            $componentName = Str::kebab($id);
        }
        
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/', $svg, $titleMatches)) {
            $title = $titleMatches[1];
            $componentName = Str::kebab($title);
        }
        
        // Clean up the name
        $componentName = preg_replace('/[^a-z0-9\-]/', '', $componentName);
        return $componentName;
    }
    
    /**
     * Clean the SVG for use as a component
     */
    protected function cleanSvg(string $svg): string
    {
        // Remove hardcoded classes, IDs, etc.
        $svg = preg_replace('/(class|id|style)=(["\'])[^\2]*\2/', '', $svg);
        
        // Replace fixed colors with currentColor
        $svg = preg_replace('/(fill|stroke)=(["\'])(?!none|transparent)([^\2]*)\2/', 'fill="currentColor"', $svg);
        
        // Add merge attributes for dynamic classes
        $svg = preg_replace('/<svg([^>]*)>/', '<svg{{ $attributes->merge([\'class\' => \'\']) }}$1>', $svg);
        
        // Format properly
        $svg = trim($svg);
        
        return $svg;
    }
    
    /**
     * Save the SVG as a component
     */
    protected function saveSvgComponent(string $name, string $svg): void
    {
        $filename = Str::kebab($name) . '.blade.php';
        $componentPath = "{$this->componentsDir}/{$filename}";
        
        // Check if component already exists
        if (File::exists($componentPath)) {
            if (!$this->option('force') && !$this->confirm("Component {$filename} already exists. Overwrite?", false)) {
                return;
            }
        }
        
        File::put($componentPath, $svg);
        $this->info("  Created SVG component: {$filename}");
    }
} 