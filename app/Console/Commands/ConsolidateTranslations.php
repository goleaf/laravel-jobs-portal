<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

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
    protected $description = 'Consolidate all translation files into a single file per language';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $localeOption = $this->option('locale');
        
        // Determine locales to process
        if ($localeOption === 'all') {
            $locales = array_keys(config('app.available_locales', []));
        } else {
            $locales = [$localeOption];
        }
        
        if (empty($locales)) {
            $this->error('No locales found in config. Please check your app.available_locales configuration.');
            return Command::FAILURE;
        }
        
        foreach ($locales as $locale) {
            $this->info("Processing locale: {$locale}");
            
            $localePath = resource_path("lang/{$locale}");
            if (!File::exists($localePath)) {
                $this->warn("Locale directory not found: {$localePath}. Creating it.");
                File::makeDirectory($localePath, 0755, true);
                continue;
            }
            
            $translations = [];
            
            // Process each PHP file
            $files = File::files($localePath);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                
                $filename = $file->getFilename();
                $moduleName = str_replace('.php', '', $filename);
                
                $this->info("Reading translations from {$filename}");
                $fileTranslations = require $file->getPathname();
                
                if (!isset($translations[$moduleName])) {
                    $translations[$moduleName] = [];
                }
                
                // Add translations to the consolidated array
                $translations[$moduleName] = array_merge($translations[$moduleName], $fileTranslations);
            }
            
            // Process JSON file if it exists
            $jsonPath = resource_path("lang/{$locale}.json");
            if (File::exists($jsonPath)) {
                $this->info("Reading translations from {$locale}.json");
                $jsonTranslations = json_decode(File::get($jsonPath), true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!isset($translations['json'])) {
                        $translations['json'] = [];
                    }
                    
                    foreach ($jsonTranslations as $key => $value) {
                        $translations['json'][$key] = $value;
                    }
                } else {
                    $this->error("Error parsing {$locale}.json: " . json_last_error_msg());
                }
            }
            
            // Create consolidated translation file
            $consolidatedPath = resource_path("lang/{$locale}.php");
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            File::put($consolidatedPath, $content);
            
            $this->info("Created consolidated translation file: {$consolidatedPath}");
            
            // Backup original files
            $backupDir = resource_path("lang/backup/{$locale}");
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            foreach ($files as $file) {
                $backupPath = "{$backupDir}/" . $file->getFilename();
                File::copy($file->getPathname(), $backupPath);
            }
            
            if (File::exists($jsonPath)) {
                $backupJsonPath = resource_path("lang/backup/{$locale}.json");
                File::copy($jsonPath, $backupJsonPath);
            }
            
            $this->info("Backed up original translation files to {$backupDir}");
            
            // Remove original files
            foreach ($files as $file) {
                File::delete($file->getPathname());
            }
            
            if (File::exists($jsonPath)) {
                File::delete($jsonPath);
            }
            
            $this->info("Removed original translation files");
            $this->info("Consolidated translations for {$locale} completed successfully");
        }
        
        $this->info("Translation consolidation completed!");
        
        return Command::SUCCESS;
    }
} 