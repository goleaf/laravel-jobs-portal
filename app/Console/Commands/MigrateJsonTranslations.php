<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class MigrateJsonTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:migrate-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate JSON translations to PHP files and merge with existing translations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting JSON translations migration...');
        
        // Get available locales
        $availableLocales = array_keys(config('app.available_locales', []));
        
        if (empty($availableLocales)) {
            $this->error('No locales found in config. Please check your app.available_locales configuration.');
            return Command::FAILURE;
        }
        
        foreach ($availableLocales as $locale) {
            $this->info("Processing locale: {$locale}");
            
            // Path to JSON translation file
            $jsonPath = resource_path("lang/{$locale}.json");
            
            // Skip if JSON file doesn't exist
            if (!File::exists($jsonPath)) {
                $this->warn("No JSON translation file found for {$locale}. Skipping.");
                continue;
            }
            
            // Load JSON translations
            $jsonTranslations = json_decode(File::get($jsonPath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error parsing JSON file for {$locale}: " . json_last_error_msg());
                continue;
            }
            
            $this->info("Found " . count($jsonTranslations) . " JSON translations for {$locale}");
            
            // Organize translations by category
            $organizedTranslations = $this->organizeTranslations($jsonTranslations);
            
            // Ensure the locale directory exists
            $localePath = resource_path("lang/{$locale}");
            if (!File::exists($localePath)) {
                File::makeDirectory($localePath, 0755, true);
                $this->info("Created directory: {$localePath}");
            }
            
            // Process each category of translations
            foreach ($organizedTranslations as $category => $translations) {
                $phpFilePath = "{$localePath}/{$category}.php";
                
                // Merge with existing translations if the file exists
                if (File::exists($phpFilePath)) {
                    $existingTranslations = require $phpFilePath;
                    $translations = array_merge($existingTranslations, $translations);
                    $this->info("Merged with existing translations for {$category}");
                }
                
                // Write the translations to the PHP file
                $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
                File::put($phpFilePath, $content);
                
                $this->info("Saved translations to: {$phpFilePath}");
            }
            
            // Backup and delete the original JSON file
            File::copy($jsonPath, $jsonPath . '.bak');
            File::delete($jsonPath);
            $this->info("Deleted original JSON file: {$jsonPath} (backup created)");
        }
        
        $this->info('Migration completed successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Organize flat JSON translations into categories
     *
     * @param array $translations
     * @return array
     */
    private function organizeTranslations(array $translations): array
    {
        $organized = [
            'messages' => [], // Default category
            'validation' => [],
            'auth' => [],
            'pagination' => [],
            'passwords' => [],
        ];
        
        foreach ($translations as $key => $value) {
            // Try to determine category based on key or content
            if (strpos($key, 'validation.') === 0 || preg_match('/^The .+ field is required\.$/i', $value)) {
                $category = 'validation';
                $newKey = str_replace('validation.', '', $key);
            } elseif (strpos($key, 'auth.') === 0 || preg_match('/password|login|register|email/i', $key)) {
                $category = 'auth';
                $newKey = str_replace('auth.', '', $key);
            } elseif (strpos($key, 'pagination.') === 0 || in_array($key, ['previous', 'next', 'showing', 'to', 'of', 'results'])) {
                $category = 'pagination';
                $newKey = str_replace('pagination.', '', $key);
            } elseif (strpos($key, 'passwords.') === 0) {
                $category = 'passwords';
                $newKey = str_replace('passwords.', '', $key);
            } else {
                $category = 'messages';
                $newKey = $key;
            }
            
            // Place into appropriate category, organizing into dot notation
            Arr::set($organized[$category], $newKey, $value);
        }
        
        // Remove empty categories
        foreach ($organized as $category => $translations) {
            if (empty($translations)) {
                unset($organized[$category]);
            }
        }
        
        return $organized;
    }
} 