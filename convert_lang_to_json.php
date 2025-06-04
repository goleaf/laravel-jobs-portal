<?php

/**
 * Laravel Language Files to JSON Converter
 * 
 * This script converts PHP array-based language files to JSON format
 * for better performance and easier management in a multilingual application.
 * 
 * Based on TODO.md Priority 2: Multilingual System Enhancement
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== CONVERTING LANGUAGE FILES TO JSON ===\n\n";

$langDirectory = __DIR__ . '/lang';
$resourcesLangDirectory = __DIR__ . '/resources/lang';

// Check both possible language directories
$directories = [];
if (is_dir($langDirectory)) {
    $directories[] = $langDirectory;
}
if (is_dir($resourcesLangDirectory)) {
    $directories[] = $resourcesLangDirectory;
}

if (empty($directories)) {
    echo "No language directories found!\n";
    exit(1);
}

$convertedFiles = 0;
$totalFiles = 0;
$languages = [];

foreach ($directories as $baseDir) {
    echo "Processing directory: $baseDir\n";
    
    // Get all language directories
    $langDirs = array_filter(glob($baseDir . '/*'), 'is_dir');
    
    foreach ($langDirs as $langDir) {
        $langCode = basename($langDir);
        
        // Skip vendor directory
        if ($langCode === 'vendor') {
            continue;
        }
        
        echo "Processing language: $langCode\n";
        $languages[] = $langCode;
        
        // Create JSON directory if it doesn't exist
        $jsonDir = $baseDir . '/' . $langCode . '_json';
        if (!is_dir($jsonDir)) {
            mkdir($jsonDir, 0755, true);
            echo "  Created JSON directory: $jsonDir\n";
        }
        
        // Get all PHP files in the language directory
        $phpFiles = glob($langDir . '/*.php');
        
        foreach ($phpFiles as $phpFile) {
            $totalFiles++;
            $fileName = basename($phpFile, '.php');
            $jsonFile = $jsonDir . '/' . $fileName . '.json';
            
            echo "  Converting: $fileName.php -> $fileName.json\n";
            
            try {
                // Include the PHP file to get the array
                $translations = include $phpFile;
                
                if (!is_array($translations)) {
                    echo "    Warning: $phpFile does not return an array\n";
                    continue;
                }
                
                // Convert to JSON with pretty printing
                $jsonContent = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                
                if ($jsonContent === false) {
                    echo "    Error: Failed to encode $phpFile to JSON\n";
                    continue;
                }
                
                // Write JSON file
                file_put_contents($jsonFile, $jsonContent);
                $convertedFiles++;
                
                echo "    ✅ Converted successfully\n";
                
            } catch (Exception $e) {
                echo "    ❌ Error converting $phpFile: " . $e->getMessage() . "\n";
            }
        }
        
        // Create a master language file combining all translations
        $masterFile = $jsonDir . '/master.json';
        $masterTranslations = [];
        
        foreach (glob($jsonDir . '/*.json') as $jsonFile) {
            if (basename($jsonFile) === 'master.json') {
                continue;
            }
            
            $fileKey = basename($jsonFile, '.json');
            $content = json_decode(file_get_contents($jsonFile), true);
            
            if ($content) {
                $masterTranslations[$fileKey] = $content;
            }
        }
        
        if (!empty($masterTranslations)) {
            file_put_contents($masterFile, json_encode($masterTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo "  ✅ Created master file: $masterFile\n";
        }
    }
}

// Create a language configuration file
$configFile = __DIR__ . '/config/languages.php';
$configContent = "<?php

return [
    'default' => 'en',
    'fallback' => 'en',
    'available' => " . var_export(array_unique($languages), true) . ",
    'rtl_languages' => ['ar', 'fa', 'he', 'ur'],
    'json_path' => resource_path('lang/{locale}_json'),
];
";

file_put_contents($configFile, $configContent);
echo "\n✅ Created language configuration: $configFile\n";

// Create a language helper class
$helperFile = __DIR__ . '/app/Helpers/LanguageHelper.php';
$helperContent = "<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LanguageHelper
{
    /**
     * Get all available languages
     */
    public static function getAvailableLanguages(): array
    {
        return config('languages.available', ['en']);
    }

    /**
     * Get translations for a specific language
     */
    public static function getTranslations(string \$locale): array
    {
        return Cache::remember(\"translations.{\$locale}\", 3600, function () use (\$locale) {
            \$jsonPath = str_replace('{locale}', \$locale, config('languages.json_path'));
            \$masterFile = \$jsonPath . '/master.json';
            
            if (File::exists(\$masterFile)) {
                return json_decode(File::get(\$masterFile), true) ?: [];
            }
            
            return [];
        });
    }

    /**
     * Get a specific translation
     */
    public static function get(string \$key, string \$locale = null, array \$replace = []): string
    {
        \$locale = \$locale ?: app()->getLocale();
        \$translations = self::getTranslations(\$locale);
        
        \$keys = explode('.', \$key);
        \$value = \$translations;
        
        foreach (\$keys as \$k) {
            if (!isset(\$value[\$k])) {
                return \$key; // Return key if translation not found
            }
            \$value = \$value[\$k];
        }
        
        // Replace placeholders
        foreach (\$replace as \$search => \$replacement) {
            \$value = str_replace(\":\$search\", \$replacement, \$value);
        }
        
        return \$value;
    }

    /**
     * Check if language is RTL
     */
    public static function isRtl(string \$locale = null): bool
    {
        \$locale = \$locale ?: app()->getLocale();
        return in_array(\$locale, config('languages.rtl_languages', []));
    }

    /**
     * Clear translation cache
     */
    public static function clearCache(): void
    {
        \$languages = self::getAvailableLanguages();
        
        foreach (\$languages as \$language) {
            Cache::forget(\"translations.{\$language}\");
        }
    }

    /**
     * Get language direction
     */
    public static function getDirection(string \$locale = null): string
    {
        return self::isRtl(\$locale) ? 'rtl' : 'ltr';
    }
}
";

// Create helpers directory if it doesn't exist
$helpersDir = dirname($helperFile);
if (!is_dir($helpersDir)) {
    mkdir($helpersDir, 0755, true);
}

file_put_contents($helperFile, $helperContent);
echo "✅ Created language helper: $helperFile\n";

// Create middleware for language switching
$middlewareFile = __DIR__ . '/app/Http/Middleware/SetLocale.php';
$middlewareContent = "<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request \$request, Closure \$next)
    {
        // Get locale from various sources
        \$locale = \$this->getLocale(\$request);
        
        // Validate locale
        if (!\$this->isValidLocale(\$locale)) {
            \$locale = config('languages.default', 'en');
        }
        
        // Set application locale
        App::setLocale(\$locale);
        
        // Store in session for persistence
        Session::put('locale', \$locale);
        
        return \$next(\$request);
    }

    /**
     * Get locale from request
     */
    private function getLocale(Request \$request): string
    {
        // 1. Check URL parameter
        if (\$request->has('lang')) {
            return \$request->get('lang');
        }
        
        // 2. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // 3. Check user preference (if authenticated)
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }
        
        // 4. Check Accept-Language header
        \$acceptLanguage = \$request->header('Accept-Language');
        if (\$acceptLanguage) {
            \$preferredLanguage = \$request->getPreferredLanguage(config('languages.available', ['en']));
            if (\$preferredLanguage) {
                return \$preferredLanguage;
            }
        }
        
        // 5. Default locale
        return config('languages.default', 'en');
    }

    /**
     * Check if locale is valid
     */
    private function isValidLocale(string \$locale): bool
    {
        return in_array(\$locale, config('languages.available', ['en']));
    }
}
";

file_put_contents($middlewareFile, $middlewareContent);
echo "✅ Created locale middleware: $middlewareFile\n";

// Create language switcher component
$componentFile = __DIR__ . '/resources/views/components/language-switcher.blade.php';
$componentContent = '<div class="language-switcher dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        @foreach(config(\'languages.available\', [\'en\']) as $locale)
            <li>
                <a class="dropdown-item {{ app()->getLocale() === $locale ? \'active\' : \'\' }}" 
                   href="{{ route(\'language.change\', $locale) }}">
                    <span class="flag-icon flag-icon-{{ $locale === \'en\' ? \'us\' : $locale }}"></span>
                    {{ __("languages.{$locale}") }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}

.flag-icon {
    width: 20px;
    height: 15px;
    margin-right: 8px;
}

[dir="rtl"] .language-switcher {
    direction: rtl;
}
</style>';

file_put_contents($componentFile, $componentContent);
echo "✅ Created language switcher component: $componentFile\n";

// Create translation helper function
$helperFunctionFile = __DIR__ . '/app/helpers.php';
$helperFunctionContent = "<?php

if (!function_exists('trans_json')) {
    /**
     * Get translation from JSON files
     */
    function trans_json(string \$key, array \$replace = [], string \$locale = null): string
    {
        return App\Helpers\LanguageHelper::get(\$key, \$locale, \$replace);
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL
     */
    function is_rtl(string \$locale = null): bool
    {
        return App\Helpers\LanguageHelper::isRtl(\$locale);
    }
}

if (!function_exists('lang_direction')) {
    /**
     * Get language direction
     */
    function lang_direction(string \$locale = null): string
    {
        return App\Helpers\LanguageHelper::getDirection(\$locale);
    }
}
";

file_put_contents($helperFunctionFile, $helperFunctionContent);
echo "✅ Created helper functions: $helperFunctionFile\n";

echo "\n=== CONVERSION SUMMARY ===\n";
echo "Total files processed: $totalFiles\n";
echo "Successfully converted: $convertedFiles\n";
echo "Languages found: " . implode(', ', array_unique($languages)) . "\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Add SetLocale middleware to app/Http/Kernel.php\n";
echo "2. Include helpers.php in composer.json autoload files\n";
echo "3. Update views to use trans_json() function\n";
echo "4. Add language switcher component to layouts\n";
echo "5. Test language switching functionality\n";

echo "\n✅ Language conversion complete!\n"; 