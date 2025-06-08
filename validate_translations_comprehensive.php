<?php

/**
 * Comprehensive Translation System Validation
 * Universal Enhanced Translation Testing
 */

require_once 'vendor/autoload.php';

class UniversalTranslationValidator
{
    private $langPath;
    private $availableLanguages;
    private $statistics = [];
    private $issues = [];

    public function __construct()
    {
        $this->langPath = __DIR__ . '/lang';
        $this->availableLanguages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
        
        echo "🔍 Universal Translation System Validation\n";
        echo "=========================================\n\n";
    }

    public function runValidation()
    {
        echo "📊 Starting Comprehensive Translation Validation...\n\n";

        $this->validateDirectoryStructure();
        $this->validateJsonFiles();
        $this->validateTranslationCompleteness();
        $this->validateSpecialCharacters();
        $this->validateRTLSupport();
        $this->generateReport();
    }

    private function validateDirectoryStructure()
    {
        echo "1️⃣ Validating Directory Structure...\n";
        
        foreach ($this->availableLanguages as $lang) {
            $jsonDir = $this->langPath . '/' . $lang . '_json';
            $phpDir = $this->langPath . '/' . $lang;
            
            if (is_dir($jsonDir)) {
                echo "   ✅ {$lang}_json directory exists\n";
                $this->statistics[$lang]['json_dir'] = true;
                
                // Count JSON files
                $jsonFiles = glob($jsonDir . '/*.json');
                $this->statistics[$lang]['json_files'] = count($jsonFiles);
                echo "      📄 JSON files: " . count($jsonFiles) . "\n";
            } else {
                echo "   ❌ {$lang}_json directory missing\n";
                $this->issues[] = "Missing {$lang}_json directory";
                $this->statistics[$lang]['json_dir'] = false;
            }

            if (is_dir($phpDir)) {
                echo "   ✅ {$lang} PHP directory exists\n";
                $this->statistics[$lang]['php_dir'] = true;
            } else {
                echo "   ⚠️  {$lang} PHP directory missing\n";
                $this->statistics[$lang]['php_dir'] = false;
            }
        }
        echo "\n";
    }

    private function validateJsonFiles()
    {
        echo "2️⃣ Validating JSON File Structure...\n";
        
        $expectedFiles = ['master.json', 'messages.json', 'auth.json', 'validation.json', 'web.json', 'js.json'];
        
        foreach ($this->availableLanguages as $lang) {
            echo "   🔍 Checking {$lang} translations:\n";
            $jsonDir = $this->langPath . '/' . $lang . '_json';
            
            if (!is_dir($jsonDir)) {
                continue;
            }

            $existingFiles = [];
            foreach ($expectedFiles as $file) {
                $filePath = $jsonDir . '/' . $file;
                if (file_exists($filePath)) {
                    $fileSize = round(filesize($filePath) / 1024, 2);
                    echo "      ✅ {$file} ({$fileSize} KB)\n";
                    
                    // Validate JSON syntax
                    $content = file_get_contents($filePath);
                    $decoded = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        echo "         ✅ Valid JSON syntax\n";
                        $this->statistics[$lang]['files'][$file] = [
                            'exists' => true,
                            'size' => $fileSize,
                            'valid_json' => true,
                            'entries' => $this->countTranslations($decoded)
                        ];
                        echo "         📝 Translation entries: " . $this->countTranslations($decoded) . "\n";
                    } else {
                        echo "         ❌ Invalid JSON syntax: " . json_last_error_msg() . "\n";
                        $this->issues[] = "Invalid JSON in {$lang}/{$file}: " . json_last_error_msg();
                        $this->statistics[$lang]['files'][$file]['valid_json'] = false;
                    }
                    $existingFiles[] = $file;
                } else {
                    echo "      ❌ {$file} missing\n";
                    $this->issues[] = "Missing file: {$lang}/{$file}";
                    $this->statistics[$lang]['files'][$file]['exists'] = false;
                }
            }
            
            // Check for unexpected files
            $allFiles = glob($jsonDir . '/*.json');
            foreach ($allFiles as $filePath) {
                $fileName = basename($filePath);
                if (!in_array($fileName, $expectedFiles)) {
                    echo "      ℹ️  Additional file: {$fileName}\n";
                }
            }
            echo "\n";
        }
    }

    private function validateTranslationCompleteness()
    {
        echo "3️⃣ Validating Translation Completeness...\n";
        
        // Use English as reference
        $referenceLang = 'en';
        $referenceFile = $this->langPath . '/' . $referenceLang . '_json/master.json';
        
        if (!file_exists($referenceFile)) {
            echo "   ❌ Reference file not found: {$referenceFile}\n";
            return;
        }

        $referenceData = json_decode(file_get_contents($referenceFile), true);
        $referenceKeys = $this->extractAllKeys($referenceData);
        
        echo "   📋 Reference language ({$referenceLang}) has " . count($referenceKeys) . " translation keys\n\n";

        foreach ($this->availableLanguages as $lang) {
            if ($lang === $referenceLang) continue;
            
            echo "   🔍 Checking {$lang} completeness:\n";
            $langFile = $this->langPath . '/' . $lang . '_json/master.json';
            
            if (!file_exists($langFile)) {
                echo "      ❌ Master file missing\n";
                continue;
            }

            $langData = json_decode(file_get_contents($langFile), true);
            $langKeys = $this->extractAllKeys($langData);
            
            $missing = array_diff($referenceKeys, $langKeys);
            $extra = array_diff($langKeys, $referenceKeys);
            
            $completeness = round((count($langKeys) / count($referenceKeys)) * 100, 2);
            echo "      📊 Completeness: {$completeness}%\n";
            echo "      📝 Keys: " . count($langKeys) . "/" . count($referenceKeys) . "\n";
            
            if (count($missing) > 0) {
                echo "      ❌ Missing keys: " . count($missing) . "\n";
                if (count($missing) <= 10) {
                    foreach (array_slice($missing, 0, 5) as $key) {
                        echo "         - {$key}\n";
                    }
                    if (count($missing) > 5) {
                        echo "         ... and " . (count($missing) - 5) . " more\n";
                    }
                }
                $this->issues[] = "{$lang} missing " . count($missing) . " translation keys";
            }
            
            if (count($extra) > 0) {
                echo "      ℹ️  Extra keys: " . count($extra) . "\n";
            }
            
            $this->statistics[$lang]['completeness'] = $completeness;
            $this->statistics[$lang]['missing_keys'] = count($missing);
            $this->statistics[$lang]['extra_keys'] = count($extra);
            echo "\n";
        }
    }

    private function validateSpecialCharacters()
    {
        echo "4️⃣ Validating Special Characters and Encoding...\n";
        
        $specialChars = [
            'ar' => ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر'],
            'ru' => ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и'],
            'zh' => ['中', '文', '语', '言', '测', '试', '字', '符', '编', '码'],
            'de' => ['ä', 'ö', 'ü', 'ß'],
            'fr' => ['à', 'â', 'ç', 'è', 'é', 'ê', 'ë', 'î', 'ï', 'ô'],
            'es' => ['á', 'é', 'í', 'ó', 'ú', 'ñ', '¿', '¡'],
            'pt' => ['ã', 'à', 'á', 'â', 'ç', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú'],
            'tr' => ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü']
        ];

        foreach ($this->availableLanguages as $lang) {
            if (!isset($specialChars[$lang])) {
                continue;
            }

            echo "   🔍 Checking {$lang} special characters:\n";
            $jsonFile = $this->langPath . '/' . $lang . '_json/master.json';
            
            if (!file_exists($jsonFile)) {
                continue;
            }

            $content = file_get_contents($jsonFile);
            $foundChars = [];
            
            foreach ($specialChars[$lang] as $char) {
                if (strpos($content, $char) !== false) {
                    $foundChars[] = $char;
                }
            }
            
            if (count($foundChars) > 0) {
                echo "      ✅ Special characters found: " . implode(', ', array_slice($foundChars, 0, 5));
                if (count($foundChars) > 5) {
                    echo " (+" . (count($foundChars) - 5) . " more)";
                }
                echo "\n";
                $this->statistics[$lang]['special_chars'] = true;
            } else {
                echo "      ⚠️  No special characters found (may indicate encoding issues)\n";
                $this->statistics[$lang]['special_chars'] = false;
            }
            
            // Check UTF-8 encoding
            if (mb_check_encoding($content, 'UTF-8')) {
                echo "      ✅ UTF-8 encoding valid\n";
                $this->statistics[$lang]['utf8_valid'] = true;
            } else {
                echo "      ❌ UTF-8 encoding invalid\n";
                $this->issues[] = "{$lang} has invalid UTF-8 encoding";
                $this->statistics[$lang]['utf8_valid'] = false;
            }
            echo "\n";
        }
    }

    private function validateRTLSupport()
    {
        echo "5️⃣ Validating RTL Language Support...\n";
        
        $rtlLanguages = ['ar'];
        
        foreach ($rtlLanguages as $lang) {
            echo "   🔍 Checking {$lang} RTL support:\n";
            
            // Check CSS file
            $rtlCssPath = __DIR__ . '/resources/css/universal/rtl-support.scss';
            if (file_exists($rtlCssPath)) {
                echo "      ✅ RTL CSS file exists\n";
                
                $cssContent = file_get_contents($rtlCssPath);
                if (strpos($cssContent, '.rtl') !== false) {
                    echo "      ✅ RTL CSS classes found\n";
                    $this->statistics[$lang]['rtl_css'] = true;
                } else {
                    echo "      ❌ RTL CSS classes missing\n";
                    $this->statistics[$lang]['rtl_css'] = false;
                }
            } else {
                echo "      ❌ RTL CSS file missing\n";
                $this->issues[] = "RTL CSS file missing";
                $this->statistics[$lang]['rtl_css'] = false;
            }
            
            // Check language config
            $configPath = __DIR__ . '/config/languages.php';
            if (file_exists($configPath)) {
                $configContent = file_get_contents($configPath);
                if (strpos($configContent, "'rtl_languages'") !== false && strpos($configContent, "'ar'") !== false) {
                    echo "      ✅ RTL language configuration found\n";
                    $this->statistics[$lang]['rtl_config'] = true;
                } else {
                    echo "      ❌ RTL language configuration missing\n";
                    $this->statistics[$lang]['rtl_config'] = false;
                }
            }
            echo "\n";
        }
    }

    private function countTranslations($array, $prefix = '')
    {
        $count = 0;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $count += $this->countTranslations($value, $prefix . $key . '.');
            } else {
                $count++;
            }
        }
        return $count;
    }

    private function extractAllKeys($array, $prefix = '')
    {
        $keys = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix . $key;
            if (is_array($value)) {
                $keys = array_merge($keys, $this->extractAllKeys($value, $fullKey . '.'));
            } else {
                $keys[] = $fullKey;
            }
        }
        return $keys;
    }

    private function generateReport()
    {
        echo "📋 COMPREHENSIVE TRANSLATION REPORT\n";
        echo "===================================\n\n";

        // Summary Statistics
        echo "📊 SUMMARY STATISTICS:\n";
        echo "-----------------------\n";
        
        $totalLanguages = count($this->availableLanguages);
        $activeLanguages = 0;
        $totalTranslations = 0;
        $avgCompleteness = 0;

        foreach ($this->availableLanguages as $lang) {
            if (isset($this->statistics[$lang]['json_dir']) && $this->statistics[$lang]['json_dir']) {
                $activeLanguages++;
                if (isset($this->statistics[$lang]['files']['master.json']['entries'])) {
                    $totalTranslations += $this->statistics[$lang]['files']['master.json']['entries'];
                }
                if (isset($this->statistics[$lang]['completeness'])) {
                    $avgCompleteness += $this->statistics[$lang]['completeness'];
                }
            }
        }

        echo "Languages configured: {$totalLanguages}\n";
        echo "Languages active: {$activeLanguages}\n";
        echo "Total translation entries: {$totalTranslations}\n";
        echo "Average completeness: " . round($avgCompleteness / $activeLanguages, 2) . "%\n\n";

        // Language Details
        echo "🔤 LANGUAGE DETAILS:\n";
        echo "--------------------\n";
        
        foreach ($this->availableLanguages as $lang) {
            $flag = $this->getLanguageFlag($lang);
            $name = $this->getLanguageName($lang);
            
            echo "{$flag} {$name} ({$lang}):\n";
            
            if (isset($this->statistics[$lang]['json_dir']) && $this->statistics[$lang]['json_dir']) {
                echo "   📁 JSON Directory: ✅\n";
                echo "   📄 JSON Files: " . ($this->statistics[$lang]['json_files'] ?? 0) . "\n";
                
                if (isset($this->statistics[$lang]['files']['master.json']['entries'])) {
                    echo "   📝 Translations: " . $this->statistics[$lang]['files']['master.json']['entries'] . "\n";
                }
                
                if (isset($this->statistics[$lang]['completeness'])) {
                    echo "   📊 Completeness: " . $this->statistics[$lang]['completeness'] . "%\n";
                }
                
                if (isset($this->statistics[$lang]['special_chars'])) {
                    echo "   🔤 Special Chars: " . ($this->statistics[$lang]['special_chars'] ? '✅' : '⚠️') . "\n";
                }
                
                if (isset($this->statistics[$lang]['utf8_valid'])) {
                    echo "   🗂️  UTF-8 Encoding: " . ($this->statistics[$lang]['utf8_valid'] ? '✅' : '❌') . "\n";
                }
                
                if ($lang === 'ar' && isset($this->statistics[$lang]['rtl_css'])) {
                    echo "   ↔️  RTL Support: " . ($this->statistics[$lang]['rtl_css'] ? '✅' : '❌') . "\n";
                }
            } else {
                echo "   📁 JSON Directory: ❌\n";
            }
            echo "\n";
        }

        // Issues Summary
        if (count($this->issues) > 0) {
            echo "⚠️  ISSUES FOUND:\n";
            echo "-----------------\n";
            foreach ($this->issues as $issue) {
                echo "❌ {$issue}\n";
            }
            echo "\n";
        } else {
            echo "✅ NO CRITICAL ISSUES FOUND!\n\n";
        }

        // Recommendations
        echo "💡 RECOMMENDATIONS:\n";
        echo "-------------------\n";
        
        $recommendations = [];
        
        // Check completeness
        foreach ($this->statistics as $lang => $stats) {
            if (isset($stats['completeness']) && $stats['completeness'] < 90) {
                $recommendations[] = "Improve {$lang} translation completeness (currently {$stats['completeness']}%)";
            }
        }
        
        // Check for missing special characters
        foreach ($this->statistics as $lang => $stats) {
            if (isset($stats['special_chars']) && !$stats['special_chars']) {
                $recommendations[] = "Review {$lang} translations for proper special characters";
            }
        }
        
        if (empty($recommendations)) {
            echo "🎉 All translations look good! Keep up the excellent work!\n";
        } else {
            foreach ($recommendations as $rec) {
                echo "💡 {$rec}\n";
            }
        }
        
        echo "\n";
        echo "🏁 Validation Complete!\n";
        echo "Total issues found: " . count($this->issues) . "\n";
        echo "Translation system status: " . (count($this->issues) === 0 ? "✅ EXCELLENT" : (count($this->issues) < 5 ? "⚠️ GOOD" : "❌ NEEDS ATTENTION")) . "\n";
    }

    private function getLanguageFlag($lang)
    {
        $flags = [
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'tr' => '🇹🇷',
            'zh' => '🇨🇳'
        ];
        return $flags[$lang] ?? '🏳️';
    }

    private function getLanguageName($lang)
    {
        $names = [
            'en' => 'English',
            'ar' => 'Arabic (العربية)',
            'de' => 'German (Deutsch)',
            'es' => 'Spanish (Español)',
            'fr' => 'French (Français)',
            'pt' => 'Portuguese (Português)',
            'ru' => 'Russian (Русский)',
            'tr' => 'Turkish (Türkçe)',
            'zh' => 'Chinese (中文)'
        ];
        return $names[$lang] ?? $lang;
    }
}

// Run validation
$validator = new UniversalTranslationValidator();
$validator->runValidation();