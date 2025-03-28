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
    protected $signature = 'translations:consolidate {--locale=all} {--chunk-size=10}';

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
        $chunkSize = $this->option('chunk-size');
        
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
        
        // Process each locale in chunks
        foreach (array_chunk($locales, intval($chunkSize)) as $localeChunk) {
            foreach ($localeChunk as $currentLocale) {
                $this->consolidateLocale($currentLocale, $langPath, $backupDir);
            }
            
            // Free memory after each chunk
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
                // Process each PHP file in chunks to save memory
                $files = collect(File::files($localeDir))->filter(function($file) {
                    return $file->getExtension() === 'php';
                });
                
                foreach ($files->chunk(5) as $fileChunk) {
                    foreach ($fileChunk as $file) {
                        $this->processPhpFile($file->getPathname(), $allTranslations, $file->getFilenameWithoutExtension());
                    }
                    gc_collect_cycles();
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
                // Read JSON file in chunks to save memory
                $handle = fopen($jsonFile, 'r');
                $contents = '';
                $chunkSize = 1024 * 1024; // 1MB chunks
                
                while (!feof($handle)) {
                    $contents .= fread($handle, $chunkSize);
                }
                
                fclose($handle);
                
                $jsonTranslations = json_decode($contents, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Error parsing JSON file {$jsonFile}: " . json_last_error_msg());
                } else {
                    // Process JSON translations in chunks
                    $jsonChunks = array_chunk($jsonTranslations, 100, true);
                    
                    foreach ($jsonChunks as $chunk) {
                        foreach ($chunk as $key => $value) {
                            $this->processJsonTranslation($key, $value, $allTranslations);
                        }
                        gc_collect_cycles();
                    }
                    
                    // Back up the JSON file
                    $backupFile = "{$backupDir}/{$locale}_" . date('Y-m-d_His') . '.json';
                    File::copy($jsonFile, $backupFile);
                    
                    // Remove the original JSON file after successful processing
                    if (count($jsonTranslations) > 0) {
                        File::delete($jsonFile);
                        $this->info("Removed original JSON file after successful consolidation.");
                    }
                }
                
                // Free memory
                unset($contents);
                unset($jsonTranslations);
                gc_collect_cycles();
            } catch (\Exception $e) {
                $this->error("Error processing JSON file {$jsonFile}: " . $e->getMessage());
            }
        }
        
        // Save consolidated translations
        if (!empty($allTranslations)) {
            try {
                // Sort keys
                ksort($allTranslations);
                
                // Create content in chunks
                $this->writeTranslationsToFile($outputFile, $allTranslations);
                
                // Backup existing file if it exists
                if (File::exists($outputFile)) {
                    $backupFile = "{$backupDir}/{$locale}_" . date('Y-m-d_His') . '.php';
                    File::copy($outputFile, $backupFile);
                }
                
                $this->info("Successfully consolidated translations for locale: {$locale}");
                
                // Free memory
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
                
                $this->info("Processed file: " . basename($filePath));
            }
            
            // Free memory
            unset($fileTranslations);
        } catch (\Exception $e) {
            $this->error("Error processing file {$filePath}: " . $e->getMessage());
        }
    }
    
    protected function processJsonTranslation($key, $value, &$translations)
    {
        // Process keys with dot notation (e.g., "auth.password")
        if (strpos($key, '.') !== false) {
            list($group, $item) = explode('.', $key, 2);
            
            if (!isset($translations[$group])) {
                $translations[$group] = [];
            }
            
            if (strpos($item, '.') !== false) {
                // Handle nested keys (e.g., "auth.password.reset")
                $parts = explode('.', $item);
                $current = &$translations[$group];
                
                foreach ($parts as $index => $part) {
                    if ($index === count($parts) - 1) {
                        $current[$part] = $value;
                    } else {
                        if (!isset($current[$part])) {
                            $current[$part] = [];
                        }
                        $current = &$current[$part];
                    }
                }
            } else {
                // Simple group.key format
                $translations[$group][$item] = $value;
            }
        } else {
            // No dot notation, add to general group
            if (!isset($translations['general'])) {
                $translations['general'] = [];
            }
            
            $translations['general'][$key] = $value;
        }
    }
    
    protected function writeTranslationsToFile($filePath, $translations)
    {
        try {
            // Start file with PHP opening
            $fileContent = "<?php\n\nreturn [";
            
            // Write translations
            foreach ($translations as $key => $value) {
                $fileContent .= "\n    '{$key}' => ";
                $fileContent .= $this->arrayAsString($value, 1);
                $fileContent .= ",";
            }
            
            // Close array and file
            $fileContent .= "\n];\n";
            
            // Write to file
            file_put_contents($filePath, $fileContent);
        } catch (\Exception $e) {
            $this->error("Error writing to file {$filePath}: " . $e->getMessage());
        }
    }
    
    protected function arrayAsString($array, $depth = 0)
    {
        $indent = str_repeat('    ', $depth);
        $output = "[\n";
        
        foreach ($array as $key => $value) {
            $output .= $indent . "    '" . addslashes($key) . "' => ";
            
            if (is_array($value)) {
                $output .= $this->arrayAsString($value, $depth + 1);
            } else {
                $output .= "'" . addslashes($value) . "'";
            }
            
            $output .= ",\n";
        }
        
        $output .= $indent . "]";
        return $output;
    }
} 
