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
    protected $description = 'Consolidate all translation files into a single file per language';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        
        // Determine which locales to process
        $locales = [];
        if ($locale === 'all') {
            // Get all directories in lang folder (each represents a locale)
            $directories = File::directories(resource_path('lang'));
            foreach ($directories as $directory) {
                $localeName = basename($directory);
                if ($localeName !== 'vendor') {
                    $locales[] = $localeName;
                }
            }
        } else {
            $locales = [$locale];
        }
        
        if (empty($locales)) {
            $this->info('No locale directories found to consolidate.');
            return 0;
        }
        
        $this->info('Consolidating translations for: ' . implode(', ', $locales));
        
        foreach ($locales as $locale) {
            $this->info("Processing locale: {$locale}");
            
            $localeDir = resource_path("lang/{$locale}");
            
            if (!File::isDirectory($localeDir)) {
                $this->warn("Locale directory not found: {$localeDir}");
                continue;
            }
            
            // Create a consolidated array for all translations
            $consolidatedTranslations = [];
            
            // Process each PHP file in the locale directory
            $phpFiles = File::glob("{$localeDir}/*.php");
            
            if (empty($phpFiles)) {
                $this->warn("No PHP translation files found for locale '{$locale}'");
                continue;
            }
            
            $this->info("Found " . count($phpFiles) . " translation files");
            
            // Create backup directory
            $backupDir = resource_path("lang/backup/{$locale}");
            if (!File::isDirectory($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            // Process each file
            foreach ($phpFiles as $phpFile) {
                $filename = basename($phpFile);
                $groupName = pathinfo($phpFile, PATHINFO_FILENAME);
                
                // Load translations from this file
                $translations = require $phpFile;
                
                // Add to consolidated array under the group name
                $consolidatedTranslations[$groupName] = $translations;
                
                // Backup the file
                File::copy($phpFile, "{$backupDir}/{$filename}");
                
                // Remove the original file
                File::delete($phpFile);
                
                $this->info("Processed and backed up: {$filename}");
            }
            
            // Check for JSON file
            $jsonFile = resource_path("lang/{$locale}.json");
            if (File::exists($jsonFile)) {
                $jsonContent = File::get($jsonFile);
                $jsonTranslations = json_decode($jsonContent, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Add JSON translations to the consolidated array
                    foreach ($jsonTranslations as $key => $value) {
                        $consolidatedTranslations[$key] = $value;
                    }
                    
                    // Backup the JSON file
                    File::copy($jsonFile, "{$backupDir}/" . basename($jsonFile) . ".bak");
                    
                    // Remove the original JSON file
                    File::delete($jsonFile);
                    
                    $this->info("Processed and backed up JSON translations");
                } else {
                    $this->error("Error parsing JSON file: " . json_last_error_msg());
                }
            }
            
            // Write the consolidated file
            $consolidatedFile = resource_path("lang/{$locale}.php");
            $content = "<?php\n\nreturn " . var_export($consolidatedTranslations, true) . ";\n";
            File::put($consolidatedFile, $content);
            
            $this->info("Created consolidated translation file: {$consolidatedFile}");
            
            // Remove the original directory since we've backed up all files
            File::deleteDirectory($localeDir);
            $this->info("Removed original locale directory: {$localeDir}");
        }
        
        $this->info("Consolidation complete!");
        
        return 0;
    }
} 
