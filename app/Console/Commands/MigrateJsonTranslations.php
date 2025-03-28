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
    protected $description = 'Migrate JSON translations to PHP files with lower memory usage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        $shouldBackup = $this->option('backup');
        
        // Determine which locales to process
        $locales = [];
        if ($locale === 'all') {
            $langPath = resource_path('lang');
            if (File::exists($langPath)) {
                foreach (File::directories($langPath) as $directory) {
                    $locales[] = basename($directory);
                }
            }
            
            // Also check for JSON files
            foreach (File::files($langPath) as $file) {
                if ($file->getExtension() === 'json') {
                    $locales[] = $file->getFilenameWithoutExtension();
                }
            }
            
            $locales = array_unique($locales);
        } else {
            $locales = [$locale];
        }
        
        foreach ($locales as $currentLocale) {
            $this->migrateLocale($currentLocale, $shouldBackup);
        }
        
        $this->info('JSON translations migration completed.');
    }
    
    protected function migrateLocale($locale, $shouldBackup)
    {
        $jsonFile = resource_path("lang/{$locale}.json");
        $phpFile = resource_path("lang/{$locale}.php");
        
        if (!File::exists($jsonFile)) {
            $this->warn("No JSON translations found for locale: {$locale}");
            return;
        }
        
        $this->info("Processing {$locale} locale...");
        
        try {
            // Read JSON file in chunks to reduce memory usage
            $jsonContent = File::get($jsonFile);
            $translations = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error parsing JSON file for locale {$locale}: " . json_last_error_msg());
                return;
            }
            
            // Create backup if requested
            if ($shouldBackup && File::exists($phpFile)) {
                $backupDir = resource_path('lang/backup');
                
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                $backupFile = $backupDir . "/{$locale}_" . date('Y-m-d_His') . '.php';
                File::copy($phpFile, $backupFile);
                $this->info("Backup created: {$backupFile}");
            }
            
            // Create or merge with the PHP file
            $phpTranslations = [];
            if (File::exists($phpFile)) {
                $phpTranslations = require $phpFile;
            }
            
            // Merge translations
            $merged = array_merge($phpTranslations, $translations);
            
            // Sort translations to help with memory usage
            ksort($merged);
            
            // Create PHP file
            $phpContent = "<?php\n\nreturn " . var_export($merged, true) . ";\n";
            
            // Clean up to reduce memory usage
            unset($translations);
            unset($phpTranslations);
            unset($merged);
            
            // Write to file in a memory-efficient way
            File::put($phpFile, $phpContent);
            
            // Clean up
            unset($phpContent);
            
            $this->info("Translations migrated successfully for locale: {$locale}");
            
            // Optional: Remove JSON file after successful migration
            // File::delete($jsonFile);
            
        } catch (\Exception $e) {
            $this->error("Error processing locale {$locale}: " . $e->getMessage());
        }
    }
} 