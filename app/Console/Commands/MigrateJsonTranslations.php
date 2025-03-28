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
    protected $signature = 'translations:migrate 
                            {--from=resources/lang : Path to JSON translation files}
                            {--to=resources/lang : Path to store PHP translation files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate JSON translations to Laravel\'s PHP-based translation system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fromPath = $this->option('from');
        $toPath = $this->option('to');
        
        $this->info("Migrating translations from {$fromPath} to {$toPath}");
        
        // Get all JSON translation files
        $jsonFiles = File::glob("{$fromPath}/*.json");
        
        if (empty($jsonFiles)) {
            $this->error("No JSON translation files found in {$fromPath}");
            return Command::FAILURE;
        }
        
        $this->info("Found " . count($jsonFiles) . " JSON translation files");
        
        foreach ($jsonFiles as $jsonFile) {
            $locale = pathinfo($jsonFile, PATHINFO_FILENAME);
            $this->info("Processing translations for locale: {$locale}");
            
            // Skip if locale is already a directory (has PHP translations)
            if (File::isDirectory("{$toPath}/{$locale}")) {
                $this->comment("Locale {$locale} already has PHP translations, will merge");
            } else {
                // Create directory for locale
                File::makeDirectory("{$toPath}/{$locale}", 0755, true, true);
                $this->info("Created directory for locale: {$locale}");
            }
            
            // Read JSON translations
            $jsonContent = File::get($jsonFile);
            $translations = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error parsing JSON file {$jsonFile}: " . json_last_error_msg());
                continue;
            }
            
            // Organize translations by category
            $categorized = $this->categorizeTranslations($translations);
            
            // Create or update PHP files for each category
            foreach ($categorized as $category => $items) {
                $phpFile = "{$toPath}/{$locale}/{$category}.php";
                
                // If file exists, merge with existing translations
                if (File::exists($phpFile)) {
                    $existingCode = File::get($phpFile);
                    $existingTranslations = $this->evaluatePhpArray($existingCode);
                    
                    if (is_array($existingTranslations)) {
                        $items = array_merge($existingTranslations, $items);
                    }
                }
                
                // Create PHP file content
                $content = "<?php\n\nreturn " . $this->arrayToPhpCode($items, 1) . ";\n";
                
                // Save PHP file
                File::put($phpFile, $content);
                $this->info("Created/updated PHP translation file: {$phpFile}");
            }
            
            // Backup the JSON file by renaming it
            $backupFile = $jsonFile . '.bak';
            File::move($jsonFile, $backupFile);
            $this->info("Backed up JSON file to: {$backupFile}");
        }
        
        $this->info("Translation migration completed successfully!");
        
        return Command::SUCCESS;
    }
    
    /**
     * Categorize translations by prefix (e.g., "auth.failed" goes to "auth" category)
     *
     * @param array $translations
     * @return array
     */
    protected function categorizeTranslations(array $translations): array
    {
        $result = [];
        
        foreach ($translations as $key => $value) {
            $parts = explode('.', $key, 2);
            
            if (count($parts) === 2) {
                // Has category prefix
                $category = $parts[0];
                $itemKey = $parts[1];
                
                if (!isset($result[$category])) {
                    $result[$category] = [];
                }
                
                // Handle nested keys
                $this->setNestedValue($result[$category], $itemKey, $value);
            } else {
                // No category prefix, put in "messages"
                if (!isset($result['messages'])) {
                    $result['messages'] = [];
                }
                
                $result['messages'][$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Set a nested value in an array using dot notation
     *
     * @param array &$array
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setNestedValue(array &$array, string $key, $value): void
    {
        $parts = explode('.', $key);
        
        // If it's a simple key, just set it
        if (count($parts) === 1) {
            $array[$key] = $value;
            return;
        }
        
        // Handle nested keys
        $current = &$array;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                // Last part, set the value
                $current[$part] = $value;
            } else {
                // Not the last part, navigate deeper
                if (!isset($current[$part]) || !is_array($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }
    }
    
    /**
     * Convert a PHP array to formatted PHP code
     *
     * @param array $array
     * @param int $indentLevel
     * @return string
     */
    protected function arrayToPhpCode(array $array, int $indentLevel = 0): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $result = "[\n";
        
        foreach ($array as $key => $value) {
            $result .= $indent . '    ' . var_export($key, true) . ' => ';
            
            if (is_array($value)) {
                $result .= $this->arrayToPhpCode($value, $indentLevel + 1);
            } else {
                $result .= var_export($value, true);
            }
            
            $result .= ",\n";
        }
        
        $result .= $indent . ']';
        
        return $result;
    }
    
    /**
     * Evaluate a PHP array from code string (for merging with existing translations)
     *
     * @param string $code
     * @return array|null
     */
    protected function evaluatePhpArray(string $code): ?array
    {
        // Extract the array part from PHP code
        if (preg_match('/return\s+(\[.+\]);/s', $code, $matches)) {
            $arrayCode = $matches[1];
            
            // Make it a valid PHP expression
            $evalCode = "return {$arrayCode};";
            
            // Evaluate the code in a safe context
            try {
                $result = eval($evalCode);
                return is_array($result) ? $result : null;
            } catch (\Throwable $e) {
                $this->error("Error evaluating PHP code: " . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }
} 