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
    protected $signature = 'translations:sync {--base=en} {--add-missing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize translations between languages, ensuring all have the same keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseLocale = $this->option('base');
        $addMissing = $this->option('add-missing');
        
        $this->info("Starting translation synchronization using {$baseLocale} as the base locale");
        
        // Get available locales
        $availableLocales = array_keys(config('app.available_locales', []));
        $availableLocales = array_filter($availableLocales, fn($locale) => $locale !== $baseLocale);
        
        if (empty($availableLocales)) {
            $this->error('No locales found in config. Please check your app.available_locales configuration.');
            return Command::FAILURE;
        }
        
        // Get base locale files
        $basePath = resource_path("lang/{$baseLocale}");
        if (!File::exists($basePath)) {
            $this->error("Base locale directory not found: {$basePath}");
            return Command::FAILURE;
        }
        
        $baseFiles = File::files($basePath);
        
        // Process each locale
        foreach ($availableLocales as $locale) {
            $this->info("Processing locale: {$locale}");
            
            $localePath = resource_path("lang/{$locale}");
            if (!File::exists($localePath)) {
                $this->warn("Creating locale directory: {$localePath}");
                File::makeDirectory($localePath, 0755, true);
            }
            
            // Process each translation file from base
            foreach ($baseFiles as $baseFile) {
                $filename = $baseFile->getFilename();
                $baseTranslations = require $baseFile->getPathname();
                
                $localeFilePath = "{$localePath}/{$filename}";
                
                if (File::exists($localeFilePath)) {
                    $localeTranslations = require $localeFilePath;
                    
                    // Find missing keys
                    $flatBase = Arr::dot($baseTranslations);
                    $flatLocale = Arr::dot($localeTranslations);
                    
                    $missingKeys = array_diff_key($flatBase, $flatLocale);
                    
                    if (count($missingKeys) > 0) {
                        $this->info("Found " . count($missingKeys) . " missing keys in {$locale}/{$filename}");
                        
                        if ($addMissing) {
                            // Add missing keys to locale translations
                            foreach ($missingKeys as $key => $value) {
                                Arr::set($localeTranslations, $key, "[UNTRANSLATED] " . $value);
                            }
                            
                            // Write back to file
                            $content = "<?php\n\nreturn " . var_export($localeTranslations, true) . ";\n";
                            File::put($localeFilePath, $content);
                            
                            $this->info("Added missing keys to {$localeFilePath}");
                        }
                    } else {
                        $this->info("No missing keys in {$locale}/{$filename}");
                    }
                } else {
                    $this->warn("Translation file does not exist: {$localeFilePath}");
                    
                    if ($addMissing) {
                        // Mark all translations as untranslated
                        $markedTranslations = $this->markAsUntranslated($baseTranslations);
                        
                        // Create the file
                        $content = "<?php\n\nreturn " . var_export($markedTranslations, true) . ";\n";
                        File::put($localeFilePath, $content);
                        
                        $this->info("Created new translation file: {$localeFilePath}");
                    }
                }
            }
        }
        
        $this->info('Translation synchronization completed successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Mark all translations as untranslated
     *
     * @param array $translations
     * @return array
     */
    private function markAsUntranslated(array $translations): array
    {
        $marked = [];
        
        $flatTranslations = Arr::dot($translations);
        
        foreach ($flatTranslations as $key => $value) {
            Arr::set($marked, $key, "[UNTRANSLATED] " . $value);
        }
        
        return $marked;
    }
} 