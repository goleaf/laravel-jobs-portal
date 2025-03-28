<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SynchronizeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync {--locales=} {--reference=en}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and add missing translations between languages with memory optimization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $referenceLocale = $this->option('reference') ?: 'en';
        $localesOption = $this->option('locales');
        
        // Get available locales
        $locales = $this->getAvailableLocales($localesOption, $referenceLocale);
        
        if (empty($locales)) {
            $this->info('No locales to synchronize.');
            return;
        }
        
        $this->info('Synchronizing translations for locales: ' . implode(', ', $locales));
        $this->info("Using reference locale: {$referenceLocale}");
        
        // Get reference translations
        $referenceTranslations = $this->getLocaleTranslations($referenceLocale);
        
        if (empty($referenceTranslations)) {
            $this->error("Reference locale '{$referenceLocale}' has no translations.");
            return;
        }
        
        $this->info("Loaded " . $this->countTranslations($referenceTranslations) . " keys from reference locale");
        
        // Process each locale
        foreach ($locales as $locale) {
            $this->syncLocale($locale, $referenceTranslations);
            
            // Free memory
            gc_collect_cycles();
        }
        
        $this->info('Translation synchronization completed successfully.');
    }
    
    protected function syncLocale($locale, $referenceTranslations)
    {
        $this->info("Processing locale: {$locale}");
        
        // Get current translations for the locale
        $localeTranslations = $this->getLocaleTranslations($locale);
        
        if (empty($localeTranslations)) {
            $this->warn("Locale '{$locale}' has no translations yet. Creating from reference.");
            $localeTranslations = $this->createEmptyTranslations($referenceTranslations);
        }
        
        // Find missing translations
        $missingCount = 0;
        $missingTranslations = $this->findMissingTranslations(
            $referenceTranslations, 
            $localeTranslations, 
            '', 
            $missingCount
        );
        
        $this->info("Found {$missingCount} missing translations for locale '{$locale}'");
        
        if ($missingCount > 0) {
            // Add missing translations
            $updatedTranslations = $this->addMissingTranslations(
                $localeTranslations, 
                $missingTranslations
            );
            
            // Save updated translations
            $this->saveTranslations($locale, $updatedTranslations);
            $this->info("Added {$missingCount} missing translations to locale '{$locale}'");
            
            // Free memory
            unset($updatedTranslations);
        }
        
        // Free memory
        unset($localeTranslations);
        unset($missingTranslations);
        gc_collect_cycles();
    }
    
    protected function getAvailableLocales($localesOption, $referenceLocale)
    {
        $locales = [];
        $langPath = resource_path('lang');
        
        if (!empty($localesOption)) {
            $locales = explode(',', $localesOption);
        } else {
            // Get all locale directories and files
            if (File::exists($langPath)) {
                // Get directories (old structure)
                foreach (File::directories($langPath) as $directory) {
                    if (basename($directory) !== 'backup') {
                        $locales[] = basename($directory);
                    }
                }
                
                // Get PHP files (new consolidated structure)
                foreach (File::files($langPath) as $file) {
                    if ($file->getExtension() === 'php') {
                        $locales[] = $file->getFilenameWithoutExtension();
                    }
                }
            }
            
            $locales = array_unique($locales);
        }
        
        // Remove reference locale from the list
        return array_filter($locales, function($locale) use ($referenceLocale) {
            return $locale !== $referenceLocale;
        });
    }
    
    protected function getLocaleTranslations($locale)
    {
        $langPath = resource_path('lang');
        $localePath = "{$langPath}/{$locale}";
        $localeFile = "{$langPath}/{$locale}.php";
        
        $translations = [];
        
        // Try to load from consolidated file first
        if (File::exists($localeFile)) {
            try {
                $translations = require $localeFile;
            } catch (\Exception $e) {
                $this->error("Error loading translations from {$localeFile}: " . $e->getMessage());
            }
        }
        // Then try to load from directory structure
        elseif (File::isDirectory($localePath)) {
            foreach (File::files($localePath) as $file) {
                if ($file->getExtension() === 'php') {
                    try {
                        $domain = $file->getFilenameWithoutExtension();
                        $domainTranslations = require $file->getPathname();
                        $translations[$domain] = $domainTranslations;
                    } catch (\Exception $e) {
                        $this->error("Error loading translations from {$file->getPathname()}: " . $e->getMessage());
                    }
                }
            }
        }
        
        return $translations;
    }
    
    protected function createEmptyTranslations($referenceTranslations)
    {
        $emptyTranslations = [];
        
        foreach ($referenceTranslations as $key => $value) {
            if (is_array($value)) {
                $emptyTranslations[$key] = $this->createEmptyTranslations($value);
            } else {
                // For new empty translations, we'll use the reference as a placeholder
                // prefixed with [MISSING] to make it easy to identify
                $emptyTranslations[$key] = "[MISSING] {$value}";
            }
        }
        
        return $emptyTranslations;
    }
    
    protected function findMissingTranslations($reference, $translations, $prefix = '', &$count = 0)
    {
        $missing = [];
        
        foreach ($reference as $key => $value) {
            $currentKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                // For nested arrays, recurse
                if (!isset($translations[$key]) || !is_array($translations[$key])) {
                    // The entire section is missing
                    $missing[$key] = $this->createEmptyTranslations($value);
                    $count += $this->countTranslations($value);
                } else {
                    // Check each item in the section
                    $sectionMissing = $this->findMissingTranslations(
                        $value, 
                        $translations[$key], 
                        $currentKey,
                        $count
                    );
                    
                    if (!empty($sectionMissing)) {
                        $missing[$key] = $sectionMissing;
                    }
                }
            } else {
                // For scalar values, check if the key exists
                if (!isset($translations[$key])) {
                    $missing[$key] = "[MISSING] {$value}";
                    $count++;
                }
            }
        }
        
        return $missing;
    }
    
    protected function addMissingTranslations($translations, $missing)
    {
        foreach ($missing as $key => $value) {
            if (!isset($translations[$key])) {
                $translations[$key] = $value;
            } elseif (is_array($value) && is_array($translations[$key])) {
                $translations[$key] = $this->addMissingTranslations($translations[$key], $value);
            }
        }
        
        return $translations;
    }
    
    protected function saveTranslations($locale, $translations)
    {
        $langPath = resource_path('lang');
        $localeFile = "{$langPath}/{$locale}.php";
        
        // Create backup
        if (File::exists($localeFile)) {
            $backupDir = "{$langPath}/backup";
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            $backupFile = "{$backupDir}/{$locale}_" . date('Y-m-d_His') . '.php';
            File::copy($localeFile, $backupFile);
            $this->info("Created backup at {$backupFile}");
        }
        
        // Sort translations to help with diffs
        if (is_array($translations)) {
            ksort($translations);
            foreach ($translations as $key => $value) {
                if (is_array($value)) {
                    ksort($translations[$key]);
                }
            }
        }
        
        // Save to file using the memory-optimized export function
        $content = "<?php\n\nreturn " . $this->varExportOptimized($translations) . ";\n";
        File::put($localeFile, $content);
        
        $this->info("Saved synchronized translations to {$localeFile}");
    }
    
    protected function countTranslations($array, $count = 0)
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                $count = $this->countTranslations($value, $count);
            } else {
                $count++;
            }
        }
        
        return $count;
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