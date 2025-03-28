<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConsolidateTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:consolidate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidate all translations into a single standardized format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation consolidation...');
        
        // Languages to process
        $languages = ['en', 'lt'];
        
        foreach ($languages as $language) {
            $this->info("Processing {$language} translations...");
            
            // Output path for the consolidated translation file
            $outputPath = resource_path("lang/{$language}.php");
            
            // Initialize an empty translations array
            $translations = [];
            
            // First, process any existing language files in the old format
            $oldFormatPath = resource_path("lang/{$language}");
            if (File::isDirectory($oldFormatPath)) {
                $files = File::files($oldFormatPath);
                
                foreach ($files as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $this->info("Processing file: {$filename}.php");
                    
                    // Load the translations from the file
                    $fileTranslations = require $file->getPathname();
                    
                    // Add to consolidated translations
                    $translations[$filename] = $fileTranslations;
                }
            }
            
            // Process backup translations if they exist
            $backupPath = resource_path("lang/backup/{$language}");
            if (File::isDirectory($backupPath)) {
                $files = File::files($backupPath);
                
                foreach ($files as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $this->info("Processing backup file: {$filename}.php");
                    
                    // Load the translations from the file
                    $fileTranslations = require $file->getPathname();
                    
                    // Add to consolidated translations if key doesn't already exist
                    if (!isset($translations[$filename])) {
                        $translations[$filename] = $fileTranslations;
                    } else {
                        // Merge with existing translations
                        $translations[$filename] = array_merge($translations[$filename], $fileTranslations);
                    }
                }
            }
            
            // Process any JSON translations
            $jsonPath = resource_path("lang/{$language}.json");
            if (File::exists($jsonPath)) {
                $this->info("Processing JSON translations");
                
                // Load the JSON translations
                $jsonTranslations = json_decode(File::get($jsonPath), true);
                
                if (!empty($jsonTranslations)) {
                    // Add to consolidated translations under the 'json' key
                    if (!isset($translations['json'])) {
                        $translations['json'] = [];
                    }
                    
                    foreach ($jsonTranslations as $key => $value) {
                        $translations['json'][$key] = $value;
                    }
                }
            }
            
            // Now check if we already have a consolidated file and merge with it
            if (File::exists($outputPath)) {
                $this->info("Merging with existing consolidated file");
                
                $existingTranslations = require $outputPath;
                $translations = $this->mergeTranslationsRecursively($existingTranslations, $translations);
            }
            
            // Write the consolidated translations to file
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            File::put($outputPath, $content);
            
            $this->info("{$language} translations consolidated successfully!");
        }
        
        $this->info('All translations consolidated successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Merge translations recursively.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function mergeTranslationsRecursively(array $array1, array $array2): array
    {
        $merged = $array1;
        
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeTranslationsRecursively($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        
        return $merged;
    }
} 
