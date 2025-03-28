<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrateJsonTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:migrate {--json= : JSON file to import} {--locale=en : Locale to migrate to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate JSON translations to Laravel standard format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        $jsonFile = $this->option('json');

        if ($jsonFile && !File::exists($jsonFile)) {
            $this->error("JSON file {$jsonFile} does not exist!");
            return Command::FAILURE;
        }

        $this->info("Migrating translations for locale: {$locale}");

        // Path to the standard language file
        $langFile = resource_path("lang/{$locale}.php");

        // Load existing translations
        $existingTranslations = [];
        if (File::exists($langFile)) {
            $existingTranslations = include $langFile;
            $this->info("Loaded existing translations from {$langFile}");
        } else {
            $this->warn("No existing translations found in {$langFile}, creating new file");
        }

        // If JSON file is provided, load and merge it
        if ($jsonFile) {
            $this->info("Loading JSON translations from {$jsonFile}");
            $jsonContent = File::get($jsonFile);
            $jsonTranslations = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error parsing JSON file: " . json_last_error_msg());
                return Command::FAILURE;
            }

            // Convert flat JSON to nested array
            $nestedTranslations = $this->convertToNested($jsonTranslations);
            
            // Merge the translations
            $existingTranslations = $this->mergeTranslations($existingTranslations, $nestedTranslations);
            
            $this->info("Merged JSON translations with existing translations");
        }

        // Save the updated translations
        $content = "<?php\n\nreturn " . var_export($existingTranslations, true) . ";\n";
        File::put($langFile, $content);

        $this->info("Translations migrated successfully to {$langFile}");

        return Command::SUCCESS;
    }

    /**
     * Convert flat JSON translations to nested array
     */
    protected function convertToNested(array $flatArray): array
    {
        $result = [];

        foreach ($flatArray as $key => $value) {
            $parts = explode('.', $key);
            $current = &$result;

            foreach ($parts as $i => $part) {
                if ($i === count($parts) - 1) {
                    $current[$part] = $value;
                } else {
                    if (!isset($current[$part]) || !is_array($current[$part])) {
                        $current[$part] = [];
                    }
                    $current = &$current[$part];
                }
            }
        }

        return $result;
    }

    /**
     * Merge two translations arrays
     */
    protected function mergeTranslations(array $existingTranslations, array $newTranslations): array
    {
        foreach ($newTranslations as $key => $value) {
            if (is_array($value) && isset($existingTranslations[$key]) && is_array($existingTranslations[$key])) {
                $existingTranslations[$key] = $this->mergeTranslations($existingTranslations[$key], $value);
            } else {
                $existingTranslations[$key] = $value;
            }
        }

        return $existingTranslations;
    }
} 