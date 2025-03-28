<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConsolidateTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:consolidate {--locale=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidate all translation files into a single file per language with memory optimization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        
        // Determine which locales to process
        $locales = [];
        $langPath = resource_path('lang');
        
        if ($locale === 'all') {
            // Get all directory names as locales
            if (File::exists($langPath)) {
                foreach (File::directories($langPath) as $directory) {
                    if (basename($directory) !== 'backup') {
                        $locales[] = basename($directory);
                    }
                }
            }
            
            // Also check for PHP files in the root lang directory
            foreach (File::files($langPath) as $file) {
                if ($file->getExtension() === 'php') {
                    $locales[] = $file->getFilenameWithoutExtension();
                }
            }
            
            $locales = array_unique($locales);
        } else {
            $locales = [$locale];
        }
        
        if (empty($locales)) {
            $this->info('No locales found to consolidate.');
            return;
        }
        
        $this->info('Consolidating translations for locales: ' . implode(', ', $locales));
        
        // Create backup directory
        $backupDir = resource_path('lang/backup');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        // Process each locale
        foreach ($locales as $currentLocale) {
            $this->consolidateLocale($currentLocale, $langPath, $backupDir);
            
            // Free memory
            gc_collect_cycles();
        }
        
        $this->info('Translation consolidation completed.');
    }
    
    protected function consolidateLocale($locale, $langPath, $backupDir)
    {
        $this->info("Processing locale: {$locale}");
        
        $localeDir = "{$langPath}/{$locale}";
        $outputFile = "{$langPath}/{$locale}.php";
        
        $allTranslations = [];
        
        // Process directory-based translations if they exist
        if (File::isDirectory($localeDir)) {
            $this->info("Found locale directory: {$localeDir}");
            
            try {
                // Process each PHP file in the locale directory
                foreach (File::files($localeDir) as $file) {
                    if ($file->getExtension() === 'php') {
                        $this->processPhpFile($file->getPathname(), $allTranslations, $file->getFilenameWithoutExtension());
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error processing directory {$localeDir}: " . $e->getMessage());
            }
        }
        
        // Process JSON file if it exists (with memory optimization)
        $jsonFile = "{$langPath}/{$locale}.json";
        if (File::exists($jsonFile)) {
            $this->info("Processing JSON file: {$jsonFile}");
            try {
                $jsonContent = File::get($jsonFile);
                $jsonTranslations = json_decode($jsonContent, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Error parsing JSON file {$jsonFile}: " . json_last_error_msg());
                } else {
                    // Add JSON translations to the consolidated array
                    foreach ($jsonTranslations as $key => $value) {
                        $allTranslations[$key] = $value;
                    }
                    
                    // Back up the JSON file
                    $backupFile = "{$backupDir}/{$locale}_" . date('Y-m-d_His') . '.json';
                    File::copy($jsonFile, $backupFile);
                    
                    // Optionally remove the original JSON file
                    // File::delete($jsonFile);
                }
                
                // Free memory
                unset($jsonContent);
                unset($jsonTranslations);
                gc_collect_cycles();
            } catch (\Exception $e) {
                $this->error("Error processing JSON file {$jsonFile}: " . $e->getMessage());
            }
        }
        
        // Save consolidated translations
        if (!empty($allTranslations)) {
            try {
                // Sort to help with memory
                ksort($allTranslations);
                
                // Create content in chunks
                $content = "<?php\n\nreturn " . $this->varExportOptimized($allTranslations) . ";\n";
                
                // Backup existing file if it exists
                if (File::exists($outputFile)) {
                    $backupFile = "{$backupDir}/{$locale}_" . date('Y-m-d_His') . '.php';
                    File::copy($outputFile, $backupFile);
                }
                
                // Write to file
                File::put($outputFile, $content);
                
                $this->info("Successfully consolidated translations for locale: {$locale}");
                
                // Free memory
                unset($content);
                unset($allTranslations);
                gc_collect_cycles();
                
            } catch (\Exception $e) {
                $this->error("Error saving consolidated file for {$locale}: " . $e->getMessage());
            }
        } else {
            $this->warn("No translations found for locale: {$locale}");
        }
    }
    
    protected function processPhpFile($filePath, &$translations, $namespace)
    {
        try {
            $fileTranslations = require $filePath;
            
            if (is_array($fileTranslations)) {
                // Add translations under their namespace
                $translations[$namespace] = $fileTranslations;
                
                // Backup the file
                $backupDir = resource_path('lang/backup');
                $backupFile = "{$backupDir}/" . basename(dirname($filePath)) . "_" . basename($filePath) . "_" . date('Y-m-d_His');
                File::copy($filePath, $backupFile);
                
                // Optionally remove the original file
                // File::delete($filePath);
                
                $this->info("Processed file: " . basename($filePath));
            }
            
            // Free memory
            unset($fileTranslations);
        } catch (\Exception $e) {
            $this->error("Error processing file {$filePath}: " . $e->getMessage());
        }
    }
    
    /**
     * Memory-optimized version of var_export for large arrays
     */
    protected function varExportOptimized($var)
    {
        if (is_array($var)) {
            $output = "[";
            $first = true;
            
            foreach ($var as $key => $value) {
                if (!$first) {
                    $output .= ",";
                }
                $first = false;
                
                $output .= PHP_EOL . "    " . var_export($key, true) . " => ";
                
                if (is_array($value)) {
                    $output .= $this->varExportOptimized($value);
                } else {
                    $output .= var_export($value, true);
                }
            }
            
            $output .= PHP_EOL . "]";
            return $output;
        } else {
            return var_export($var, true);
        }
    }
} 
