<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

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
    protected $description = 'Find and add missing translations between languages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $referenceLocale = $this->option('reference') ?: 'en';
        $specificLocales = $this->option('locales');
        
        $localesToProcess = $specificLocales ? explode(',', $specificLocales) : $this->getAvailableLocales();
        
        // Remove reference locale from the list to process
        $localesToProcess = array_filter($localesToProcess, function ($locale) use ($referenceLocale) {
            return $locale !== $referenceLocale;
        });
        
        if (empty($localesToProcess)) {
            $this->error('No locales to process!');
            return 1;
        }
        
        // Get reference translations
        $referenceTranslations = $this->getLocaleTranslations($referenceLocale);
        if (empty($referenceTranslations)) {
            $this->error("Reference locale '{$referenceLocale}' has no translations!");
            return 1;
        }
        
        $this->info("Using '{$referenceLocale}' as reference locale.");
        $this->info("Processing locales: " . implode(', ', $localesToProcess));
        
        $statsAdded = 0;
        
        foreach ($localesToProcess as $locale) {
            $this->info("Processing locale: {$locale}");
            
            // Get current translations for this locale
            $currentTranslations = $this->getLocaleTranslations($locale);
            
            // Find missing keys
            $missingTranslations = $this->findMissingTranslations($referenceTranslations, $currentTranslations);
            
            if (empty($missingTranslations)) {
                $this->info("  No missing translations found.");
                continue;
            }
            
            $this->info("  Found " . count($missingTranslations) . " missing translations.");
            
            // Add missing translations with placeholder values
            $updatedTranslations = $this->addMissingTranslations($currentTranslations, $missingTranslations, $referenceTranslations);
            
            // Save the updated translations
            $this->saveTranslations($locale, $updatedTranslations);
            
            $statsAdded += count($missingTranslations);
            $this->info("  Updated {$locale} translations file.");
        }
        
        $this->info("Synchronization complete! Added {$statsAdded} missing translations.");
        
        return 0;
    }
    
    /**
     * Get all available locales in the project.
     */
    protected function getAvailableLocales()
    {
        $path = resource_path('lang');
        $locales = [];
        
        // Get all .php files directly in the lang directory
        foreach (File::files($path) as $file) {
            if ($file->getExtension() === 'php') {
                $locales[] = $file->getFilenameWithoutExtension();
            }
        }
        
        // Get all directories in the lang directory
        foreach (File::directories($path) as $directory) {
            $localeName = basename($directory);
            if ($localeName !== 'vendor') {
                $locales[] = $localeName;
            }
        }
        
        return array_unique($locales);
    }
    
    /**
     * Get all translations for a locale.
     */
    protected function getLocaleTranslations($locale)
    {
        $translations = [];
        
        // Check if we have a single file format (langname.php)
        $singleFilePath = resource_path("lang/{$locale}.php");
        if (File::exists($singleFilePath)) {
            $translations = require $singleFilePath;
        } else {
            // Check if we have a directory with multiple files
            $localeDir = resource_path("lang/{$locale}");
            if (File::isDirectory($localeDir)) {
                foreach (File::files($localeDir) as $file) {
                    if ($file->getExtension() === 'php') {
                        $group = $file->getFilenameWithoutExtension();
                        $groupTranslations = require $file->getPathname();
                        $translations[$group] = $groupTranslations;
                    }
                }
            }
        }
        
        return $translations;
    }
    
    /**
     * Find missing translations by comparing reference with current translations.
     */
    protected function findMissingTranslations($reference, $current)
    {
        $missing = [];
        
        // Flatten arrays for easier comparison
        $flatReference = Arr::dot($reference);
        $flatCurrent = Arr::dot($current);
        
        // Find keys in reference that don't exist in current
        foreach ($flatReference as $key => $value) {
            if (!isset($flatCurrent[$key])) {
                $missing[$key] = $value;
            }
        }
        
        return $missing;
    }
    
    /**
     * Add missing translations to the current translations array.
     */
    protected function addMissingTranslations($current, $missing, $reference)
    {
        foreach ($missing as $key => $value) {
            Arr::set($current, $key, $value . ' [MISSING]');
        }
        
        return $current;
    }
    
    /**
     * Save the updated translations back to the appropriate file.
     */
    protected function saveTranslations($locale, $translations)
    {
        $singleFilePath = resource_path("lang/{$locale}.php");
        
        if (File::exists($singleFilePath)) {
            // Single file format
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            File::put($singleFilePath, $content);
        } else {
            // Directory format with multiple files
            $localeDir = resource_path("lang/{$locale}");
            
            if (!File::isDirectory($localeDir)) {
                File::makeDirectory($localeDir, 0755, true);
            }
            
            foreach ($translations as $group => $groupTranslations) {
                $filePath = "{$localeDir}/{$group}.php";
                $content = "<?php\n\nreturn " . var_export($groupTranslations, true) . ";\n";
                File::put($filePath, $content);
            }
        }
    }
} 