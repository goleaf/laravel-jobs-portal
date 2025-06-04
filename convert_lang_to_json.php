<?php

/**
 * Language Conversion Script
 * 
 * Converts all PHP language files to JSON format for the multilingual system
 */

require_once __DIR__ . '/vendor/autoload.php';

class LanguageConverter
{
    private $langPath;
    private $supportedLanguages = ['ar', 'de', 'en', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    private $processedLanguages = [];
    private $errors = [];

    public function __construct()
    {
        $this->langPath = __DIR__ . '/lang';
    }

    /**
     * Convert all language files
     */
    public function convertAll()
    {
        echo "=== LANGUAGE CONVERSION TO JSON ===\n";
        echo "Converting PHP language files to JSON format...\n\n";

        foreach ($this->supportedLanguages as $lang) {
            $this->convertLanguage($lang);
        }

        $this->generateReport();
    }

    /**
     * Convert a specific language
     */
    private function convertLanguage($lang)
    {
        echo "Converting language: $lang\n";
        
        $langDir = $this->langPath . "/$lang";
        $jsonFile = $this->langPath . "/$lang.json";

        if (!is_dir($langDir)) {
            echo "  Language directory not found: $langDir\n";
            return;
        }

        $translations = [];

        // Find all PHP files in the language directory
        $phpFiles = glob($langDir . '/*.php');
        
        foreach ($phpFiles as $phpFile) {
            $this->processPhpFile($phpFile, $translations, $lang);
        }

        // Save as JSON
        if (!empty($translations)) {
            $jsonContent = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($jsonFile, $jsonContent);
            
            $this->processedLanguages[$lang] = [
                'php_files_count' => count($phpFiles),
                'translation_keys' => count($translations, COUNT_RECURSIVE),
                'json_file' => $jsonFile
            ];
            
            echo "  ✓ Converted to: $jsonFile\n";
            echo "  ✓ Translation keys: " . count($translations, COUNT_RECURSIVE) . "\n";
        } else {
            echo "  ✗ No translations found\n";
        }
        
        echo "\n";
    }

    /**
     * Process a single PHP language file
     */
    private function processPhpFile($phpFile, &$translations, $lang)
    {
        $filename = basename($phpFile, '.php');
        echo "  Processing: $filename.php\n";

        try {
            $content = include $phpFile;
            
            if (is_array($content)) {
                if ($filename === 'messages') {
                    // Main messages file - merge at root level
                    $translations = array_merge($translations, $content);
                } else {
                    // Other files - nest under filename
                    $translations[$filename] = $content;
                }
            } else {
                echo "    ✗ Invalid format (not an array)\n";
                $this->errors[] = "Invalid format in $phpFile";
            }
        } catch (Exception $e) {
            echo "    ✗ Error: " . $e->getMessage() . "\n";
            $this->errors[] = "Error processing $phpFile: " . $e->getMessage();
        }
    }

    /**
     * Generate conversion report
     */
    private function generateReport()
    {
        echo "=== CONVERSION REPORT ===\n";
        
        foreach ($this->processedLanguages as $lang => $stats) {
            echo "Language: $lang\n";
            echo "  PHP files: {$stats['php_files_count']}\n";
            echo "  Translation keys: {$stats['translation_keys']}\n";
            echo "  JSON file: {$stats['json_file']}\n\n";
        }

        if (!empty($this->errors)) {
            echo "=== ERRORS ===\n";
            foreach ($this->errors as $error) {
                echo "  ✗ $error\n";
            }
            echo "\n";
        }

        echo "Total languages processed: " . count($this->processedLanguages) . "\n";
        echo "Conversion complete!\n";

        $this->createMigrationGuide();
    }

    /**
     * Create migration guide for developers
     */
    private function createMigrationGuide()
    {
        $guide = <<<'GUIDE'
# Language Migration Guide

## Overview
The language system has been converted from PHP arrays to JSON files for better performance and easier management.

## Changes Made
- All PHP language files (messages.php, web.php, js.php, etc.) have been converted to JSON format
- Each language now has a single JSON file: `lang/{language}.json`
- Translation keys are now flat or nested as needed

## Usage in Blade Templates

### Old Way (PHP arrays):
```blade
{{ __('messages.welcome') }}
{{ __('web.home') }}
```

### New Way (JSON):
```blade
{{ __('welcome') }}
{{ __('home') }}
```

## Usage in Controllers

### Old Way:
```php
$message = __('messages.success');
```

### New Way:
```php
$message = __('success');
```

## Adding New Translations

1. Edit the appropriate language JSON file: `lang/{language}.json`
2. Add your key-value pair:
```json
{
  "new_key": "New Translation",
  "nested": {
    "key": "Nested Translation"
  }
}
```

## Nested Translations
For nested translations, use dot notation:
```blade
{{ __('nested.key') }}
```

## Pluralization
JSON format supports pluralization:
```json
{
  "items": "{0} No items|{1} One item|[2,*] :count items"
}
```

Usage:
```blade
{{ trans_choice('items', $count) }}
```

## Next Steps
1. Update all Blade files to use the new translation keys
2. Remove old PHP language files after testing
3. Update language switching logic if needed
4. Test all translations thoroughly

GUIDE;

        file_put_contents(__DIR__ . '/LANGUAGE_MIGRATION_GUIDE.md', $guide);
        echo "Migration guide created: LANGUAGE_MIGRATION_GUIDE.md\n";
    }
}

/**
 * Translation Service Class
 */
class TranslationService
{
    /**
     * Create the TranslationService class
     */
    public function createService()
    {
        $serviceContent = <<<'PHP'
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

/**
 * Translation Service for JSON-based translations
 */
class TranslationService
{
    private $defaultLocale = 'en';
    private $fallbackLocale = 'en';
    private $translations = [];

    /**
     * Load translations for a specific locale
     */
    public function loadTranslations($locale = null)
    {
        $locale = $locale ?: App::getLocale();
        
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }

        $cacheKey = "translations.$locale";
        
        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            return $this->loadTranslationsFromFile($locale);
        });
    }

    /**
     * Load translations from JSON file
     */
    private function loadTranslationsFromFile($locale)
    {
        $filePath = lang_path("$locale.json");
        
        if (!File::exists($filePath)) {
            // Fallback to default locale
            $filePath = lang_path("{$this->fallbackLocale}.json");
        }

        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $translations = json_decode($content, true);

        return $translations ?: [];
    }

    /**
     * Get a translation by key
     */
    public function get($key, $replace = [], $locale = null)
    {
        $translations = $this->loadTranslations($locale);
        
        $value = data_get($translations, $key, $key);
        
        if (!empty($replace)) {
            foreach ($replace as $placeholder => $replacement) {
                $value = str_replace(":$placeholder", $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Check if a translation exists
     */
    public function has($key, $locale = null)
    {
        $translations = $this->loadTranslations($locale);
        return data_get($translations, $key) !== null;
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        $cacheKeys = [];
        foreach (['ar', 'de', 'en', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'] as $locale) {
            $cacheKeys[] = "translations.$locale";
        }
        
        Cache::forget($cacheKeys);
    }

    /**
     * Get all available locales
     */
    public function getAvailableLocales()
    {
        $locales = [];
        $langPath = lang_path();
        
        foreach (glob($langPath . '/*.json') as $file) {
            $locale = basename($file, '.json');
            $locales[] = $locale;
        }

        return $locales;
    }
}
PHP;

        $servicePath = __DIR__ . '/app/Services/TranslationService.php';
        $serviceDir = dirname($servicePath);
        
        if (!is_dir($serviceDir)) {
            mkdir($serviceDir, 0755, true);
        }
        
        file_put_contents($servicePath, $serviceContent);
        echo "TranslationService created: $servicePath\n";
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    $converter = new LanguageConverter();
    $converter->convertAll();
    
    $translationService = new TranslationService();
    $translationService->createService();
    
    echo "\n=== NEXT STEPS ===\n";
    echo "1. Review the generated JSON files in lang/ directory\n";
    echo "2. Read LANGUAGE_MIGRATION_GUIDE.md for implementation details\n";
    echo "3. Update blade templates to use new translation keys\n";
    echo "4. Test the TranslationService\n";
    echo "5. Update language switching middleware\n";
} 