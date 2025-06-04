<?php

/**
 * Laravel Language Migration Script
 * Converts PHP language arrays to JSON format for modern Laravel applications
 * 
 * This script will:
 * 1. Scan all existing PHP language files
 * 2. Convert nested arrays to flat JSON keys
 * 3. Create JSON files for each language
 * 4. Generate blade template update instructions
 * 5. Create language switching functionality
 */

class LanguageMigrationScript
{
    private $languageDirectory = 'resources/lang';
    private $outputDirectory = 'resources/lang/json';
    private $supportedLanguages = ['en', 'lt']; // Add more as needed
    private $convertedData = [];
    private $bladeUpdates = [];
    private $stats = [];

    public function __construct()
    {
        echo "🌐 LARAVEL LANGUAGE MIGRATION TO JSON\n";
        echo "=====================================\n\n";
        
        // Create output directory
        if (!is_dir($this->outputDirectory)) {
            mkdir($this->outputDirectory, 0755, true);
        }
    }

    /**
     * Main migration workflow
     */
    public function migrate()
    {
        $this->step1_scanPhpLanguageFiles();
        $this->step2_convertToJson();
        $this->step3_generateJsonFiles();
        $this->step4_createLanguageSwitcher();
        $this->step5_generateBladeUpdateInstructions();
        $this->step6_generateReport();
    }

    /**
     * Step 1: Scan all PHP language files
     */
    private function step1_scanPhpLanguageFiles()
    {
        echo "📁 STEP 1: Scanning PHP Language Files\n";
        echo "=======================================\n";

        foreach ($this->supportedLanguages as $lang) {
            echo "Processing language: {$lang}\n";
            
            // Check for main language file (e.g., en.php)
            $mainFile = $this->languageDirectory . "/{$lang}.php";
            if (file_exists($mainFile)) {
                $this->processLanguageFile($mainFile, $lang);
            }
            
            // Check for directory-based language files (e.g., en/messages.php)
            $langDir = $this->languageDirectory . "/{$lang}";
            if (is_dir($langDir)) {
                $files = glob($langDir . "/*.php");
                foreach ($files as $file) {
                    $this->processLanguageFile($file, $lang);
                }
            }
        }

        echo "✅ Found " . count($this->convertedData) . " language files\n\n";
    }

    /**
     * Process individual language file
     */
    private function processLanguageFile($filePath, $language)
    {
        try {
            $data = include $filePath;
            if (is_array($data)) {
                $fileName = basename($filePath, '.php');
                $this->convertedData[$language][$fileName] = $data;
                echo "  ✓ Loaded: {$filePath}\n";
            }
        } catch (Exception $e) {
            echo "  ✗ Error loading {$filePath}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Step 2: Convert nested arrays to flat JSON keys
     */
    private function step2_convertToJson()
    {
        echo "🔄 STEP 2: Converting to Flat JSON Structure\n";
        echo "============================================\n";

        foreach ($this->convertedData as $language => $files) {
            $flattenedData = [];
            
            foreach ($files as $fileName => $data) {
                $flattened = $this->flattenArray($data, $fileName);
                $flattenedData = array_merge($flattenedData, $flattened);
            }
            
            $this->convertedData[$language]['flattened'] = $flattenedData;
            echo "  ✓ Flattened {$language}: " . count($flattenedData) . " keys\n";
        }

        echo "✅ Conversion completed\n\n";
    }

    /**
     * Flatten nested array to dot notation
     */
    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Step 3: Generate JSON files
     */
    private function step3_generateJsonFiles()
    {
        echo "📝 STEP 3: Generating JSON Files\n";
        echo "================================\n";

        foreach ($this->convertedData as $language => $data) {
            if (isset($data['flattened'])) {
                $jsonFile = $this->outputDirectory . "/{$language}.json";
                $jsonContent = json_encode($data['flattened'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                
                file_put_contents($jsonFile, $jsonContent);
                echo "  ✓ Created: {$jsonFile} (" . count($data['flattened']) . " translations)\n";
                
                $this->stats[$language] = count($data['flattened']);
            }
        }

        echo "✅ JSON files generated\n\n";
    }

    /**
     * Step 4: Create language switcher functionality
     */
    private function step4_createLanguageSwitcher()
    {
        echo "🔧 STEP 4: Creating Language Switcher\n";
        echo "=====================================\n";

        // Create language switcher component
        $switcherComponent = $this->generateLanguageSwitcherComponent();
        file_put_contents('resources/views/components/language-switcher.blade.php', $switcherComponent);
        echo "  ✓ Created: resources/views/components/language-switcher.blade.php\n";

        // Create language switching route
        $languageRoute = $this->generateLanguageRoute();
        file_put_contents('routes/language.php', $languageRoute);
        echo "  ✓ Created: routes/language.php\n";

        // Create language middleware
        $languageMiddleware = $this->generateLanguageMiddleware();
        if (!is_dir('app/Http/Middleware')) {
            mkdir('app/Http/Middleware', 0755, true);
        }
        file_put_contents('app/Http/Middleware/SetLocale.php', $languageMiddleware);
        echo "  ✓ Created: app/Http/Middleware/SetLocale.php\n";

        echo "✅ Language switcher created\n\n";
    }

    /**
     * Generate language switcher component
     */
    private function generateLanguageSwitcherComponent()
    {
        $languages = [];
        foreach ($this->supportedLanguages as $lang) {
            $languages[] = "'{$lang}' => '" . strtoupper($lang) . "'";
        }
        $languagesArray = implode(",\n        ", $languages);

        return "@php
    \$languages = [
        {$languagesArray}
    ];
    \$currentLocale = app()->getLocale();
@endphp

<div class=\"language-switcher dropdown\">
    <button class=\"btn btn-outline-secondary dropdown-toggle\" type=\"button\" id=\"languageDropdown\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
        <i class=\"fas fa-globe\"></i>
        {{ strtoupper(\$currentLocale) }}
    </button>
    <ul class=\"dropdown-menu\" aria-labelledby=\"languageDropdown\">
        @foreach(\$languages as \$code => \$name)
            <li>
                <a class=\"dropdown-item {{ \$currentLocale === \$code ? 'active' : '' }}\" 
                   href=\"{{ route('language.switch', \$code) }}\">
                    <span class=\"flag-icon flag-icon-{{ \$code === 'en' ? 'us' : \$code }}\"></span>
                    {{ \$name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher .flag-icon {
    margin-right: 0.5rem;
}
.language-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}
</style>";
    }

    /**
     * Generate language switching route
     */
    private function generateLanguageRoute()
    {
        return "<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Language Routes
|--------------------------------------------------------------------------
|
| Routes for language switching functionality
|
*/

Route::get('/language/{locale}', function (\$locale) {
    \$supportedLanguages = ['" . implode("', '", $this->supportedLanguages) . "'];
    
    if (in_array(\$locale, \$supportedLanguages)) {
        session(['locale' => \$locale]);
        app()->setLocale(\$locale);
    }
    
    return redirect()->back()->with('success', __('Language changed successfully'));
})->name('language.switch');";
    }

    /**
     * Generate language middleware
     */
    private function generateLanguageMiddleware()
    {
        return "<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \Closure  \$next
     * @return mixed
     */
    public function handle(Request \$request, Closure \$next)
    {
        \$supportedLanguages = ['" . implode("', '", $this->supportedLanguages) . "'];
        
        // Get locale from session, URL parameter, or user preference
        \$locale = \$request->session()->get('locale') 
                  ?? \$request->get('lang') 
                  ?? auth()->user()->preferred_language ?? 'en';
        
        // Validate locale
        if (in_array(\$locale, \$supportedLanguages)) {
            App::setLocale(\$locale);
        } else {
            App::setLocale('en'); // Default fallback
        }
        
        return \$next(\$request);
    }
}";
    }

    /**
     * Step 5: Generate blade template update instructions
     */
    private function step5_generateBladeUpdateInstructions()
    {
        echo "📋 STEP 5: Generating Blade Update Instructions\n";
        echo "===============================================\n";

        $instructions = $this->generateBladeInstructions();
        file_put_contents('BLADE_MIGRATION_INSTRUCTIONS.md', $instructions);
        echo "  ✓ Created: BLADE_MIGRATION_INSTRUCTIONS.md\n";

        echo "✅ Instructions generated\n\n";
    }

    /**
     * Generate blade migration instructions
     */
    private function generateBladeInstructions()
    {
        return "# 🌐 BLADE TEMPLATE MIGRATION INSTRUCTIONS

## Overview
This document provides instructions for updating all Blade templates to use the new JSON-based translation system.

## Migration Steps

### 1. Update Translation Calls

**OLD FORMAT (PHP arrays):**
```blade
{{ __('messages.common.save') }}
{{ __('messages.job.job_title') }}
{{ trans('validation.required') }}
```

**NEW FORMAT (JSON keys):**
```blade
{{ __('messages.common.save') }}
{{ __('messages.job.job_title') }}
{{ __('validation.required') }}
```

### 2. Common Translation Patterns

#### Form Labels
```blade
<!-- OLD -->
<label>{{ __('messages.common.email') }}</label>

<!-- NEW -->
<label>{{ __('messages.common.email') }}</label>
```

#### Button Text
```blade
<!-- OLD -->
<button>{{ __('messages.common.save') }}</button>

<!-- NEW -->
<button>{{ __('messages.common.save') }}</button>
```

#### Flash Messages
```blade
<!-- OLD -->
@if(session('success'))
    <div class=\"alert alert-success\">{{ __('messages.flash.success') }}</div>
@endif

<!-- NEW -->
@if(session('success'))
    <div class=\"alert alert-success\">{{ __('messages.flash.success') }}</div>
@endif
```

### 3. Pluralization
```blade
<!-- OLD -->
{{ trans_choice('messages.job.jobs', \$count) }}

<!-- NEW -->
{{ __('messages.job.jobs', ['count' => \$count]) }}
```

### 4. Parameters
```blade
<!-- OLD -->
{{ __('messages.welcome', ['name' => \$user->name]) }}

<!-- NEW -->
{{ __('messages.welcome', ['name' => \$user->name]) }}
```

## Available Translations

### Common Translations
" . $this->generateTranslationList() . "

## Language Switcher Usage

Add the language switcher to your layout:
```blade
<x-language-switcher />
```

## Validation

After migration, test all pages with different languages:
1. Switch to each language
2. Verify all text displays correctly
3. Check form validation messages
4. Test pluralization

## Notes
- All translation keys are now flat (dot notation)
- JSON files are located in `resources/lang/json/`
- Fallback language is English (en)
- RTL support is available for Arabic
";
    }

    /**
     * Generate translation list for documentation
     */
    private function generateTranslationList()
    {
        $list = "";
        if (isset($this->convertedData['en']['flattened'])) {
            $translations = array_slice($this->convertedData['en']['flattened'], 0, 20, true);
            foreach ($translations as $key => $value) {
                $list .= "- `{$key}`: \"{$value}\"\n";
            }
            $list .= "- ... and " . (count($this->convertedData['en']['flattened']) - 20) . " more\n";
        }
        return $list;
    }

    /**
     * Step 6: Generate migration report
     */
    private function step6_generateReport()
    {
        echo "📊 STEP 6: Generating Migration Report\n";
        echo "=====================================\n";

        $report = $this->generateMigrationReport();
        file_put_contents('LANGUAGE_MIGRATION_REPORT.md', $report);
        echo "  ✓ Created: LANGUAGE_MIGRATION_REPORT.md\n";

        echo "✅ Migration report generated\n\n";
    }

    /**
     * Generate comprehensive migration report
     */
    private function generateMigrationReport()
    {
        $totalTranslations = array_sum($this->stats);
        
        return "# 🌐 LANGUAGE MIGRATION REPORT

## Summary
- **Migration Date**: " . date('Y-m-d H:i:s') . "
- **Languages Processed**: " . count($this->supportedLanguages) . "
- **Total Translations**: {$totalTranslations}

## Language Statistics
" . $this->generateLanguageStats() . "

## Files Created
- JSON language files: `resources/lang/json/`
- Language switcher: `resources/views/components/language-switcher.blade.php`
- Language routes: `routes/language.php`
- Locale middleware: `app/Http/Middleware/SetLocale.php`
- Migration instructions: `BLADE_MIGRATION_INSTRUCTIONS.md`

## Next Steps
1. ✅ Register the SetLocale middleware in `app/Http/Kernel.php`
2. ✅ Include language routes in `routes/web.php`
3. ✅ Add language switcher to main layout
4. ⏳ Update all Blade templates (see BLADE_MIGRATION_INSTRUCTIONS.md)
5. ⏳ Test all languages thoroughly
6. ⏳ Add RTL CSS for Arabic language

## Middleware Registration
Add to `app/Http/Kernel.php`:
```php
protected \$middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\SetLocale::class,
    ],
];
```

## Route Registration
Add to `routes/web.php`:
```php
require __DIR__.'/language.php';
```

## Layout Integration
Add to your main layout file:
```blade
<x-language-switcher />
```
";
    }

    /**
     * Generate language statistics
     */
    private function generateLanguageStats()
    {
        $stats = "";
        foreach ($this->stats as $language => $count) {
            $stats .= "- **{$language}**: {$count} translations\n";
        }
        return $stats;
    }
}

// Run the migration
if (php_sapi_name() === 'cli') {
    $migration = new LanguageMigrationScript();
    $migration->migrate();
    
    echo "🎉 LANGUAGE MIGRATION COMPLETE!\n";
    echo "===============================\n";
    echo "✅ JSON language files created\n";
    echo "✅ Language switcher component ready\n";
    echo "✅ Middleware and routes generated\n";
    echo "✅ Migration instructions created\n\n";
    echo "📖 Next steps:\n";
    echo "1. Review LANGUAGE_MIGRATION_REPORT.md\n";
    echo "2. Follow BLADE_MIGRATION_INSTRUCTIONS.md\n";
    echo "3. Register middleware and routes\n";
    echo "4. Test all languages\n\n";
} 