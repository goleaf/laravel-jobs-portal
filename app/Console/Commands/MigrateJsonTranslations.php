<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateJsonTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:migrate-json {--locale=all} {--backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate JSON translations to PHP files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        $backup = $this->option('backup');
        
        // Determine which locales to process
        $locales = [];
        if ($locale === 'all') {
            $jsonFiles = File::glob(resource_path('lang') . '/*.json');
            foreach ($jsonFiles as $file) {
                $locales[] = pathinfo($file, PATHINFO_FILENAME);
            }
        } else {
            $locales = [$locale];
        }
        
        if (empty($locales)) {
            $this->info('No JSON translation files found.');
            return 0;
        }
        
        $this->info('Migrating JSON translations for: ' . implode(', ', $locales));
        
        $totalCount = 0;
        
        foreach ($locales as $locale) {
            $jsonFile = resource_path("lang/{$locale}.json");
            
            if (!File::exists($jsonFile)) {
                $this->warn("No JSON file found for locale '{$locale}'");
                continue;
            }
            
            // Load JSON translations
            $jsonContent = File::get($jsonFile);
            $translations = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error parsing JSON file for locale '{$locale}': " . json_last_error_msg());
                continue;
            }
            
            $count = count($translations);
            $this->info("Found {$count} translations in JSON file for locale '{$locale}'");
            
            // Load existing PHP translations
            $phpFile = resource_path("lang/{$locale}.php");
            $existingTranslations = [];
            
            if (File::exists($phpFile)) {
                $existingTranslations = require $phpFile;
                $this->info("Found existing PHP translations file with " . count($existingTranslations) . " entries");
            }
            
            // Merge translations
            $mergedTranslations = array_merge($existingTranslations, $translations);
            
            // Backup original file if requested
            if ($backup && File::exists($phpFile)) {
                $backupPath = resource_path("lang/backup");
                
                if (!File::isDirectory($backupPath)) {
                    File::makeDirectory($backupPath, 0755, true);
                }
                
                $backupFile = "{$backupPath}/{$locale}.php.bak";
                File::copy($phpFile, $backupFile);
                $this->info("Backed up existing PHP translations to {$backupFile}");
            }
            
            // Write to PHP file
            $phpContent = "<?php\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
            File::put($phpFile, $phpContent);
            
            $this->info("Successfully migrated {$count} JSON translations to PHP file for locale '{$locale}'");
            $totalCount += $count;
            
            // Backup JSON file if requested
            if ($backup) {
                $backupPath = resource_path("lang/backup");
                
                if (!File::isDirectory($backupPath)) {
                    File::makeDirectory($backupPath, 0755, true);
                }
                
                $backupFile = "{$backupPath}/{$locale}.json.bak";
                File::copy($jsonFile, $backupFile);
                $this->info("Backed up JSON translations to {$backupFile}");
            }
            
            // Delete JSON file
            File::delete($jsonFile);
            $this->info("Deleted original JSON file");
        }
        
        $this->info("Migration completed! Total {$totalCount} translations migrated.");
        
        return 0;
    }
} 