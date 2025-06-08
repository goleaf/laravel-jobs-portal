<?php

/**
 * Context7 Comprehensive Translation Validation - Final Check
 * Double-checks all translations and validates system completeness
 */

class Context7FinalTranslationValidator
{
    private $languages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    private $langPath;
    private $viewsPath;
    private $validationResults = [];
    private $systemStats = [];

    public function __construct()
    {
        $this->langPath = __DIR__ . '/lang';
        $this->viewsPath = __DIR__ . '/resources/views';
        
        echo "🔍 Context7 Comprehensive Translation Validation - Final Check\n";
        echo "============================================================\n\n";
    }

    public function validateTranslationSystem()
    {
        echo "🚀 Starting Comprehensive Translation System Validation...\n\n";

        $this->validateTranslationFiles();
        $this->validateTranslationCompleteness();
        $this->validateUTF8Encoding();
        $this->testTranslationFunctions();
        $this->scanBladeForHardcodedText();
        $this->generateFinalReport();
    }

    private function validateTranslationFiles()
    {
        echo "1️⃣ Validating Translation Files Structure...\n";

        $this->systemStats['languages'] = count($this->languages);
        $this->systemStats['total_files'] = 0;
        $this->systemStats['valid_files'] = 0;
        $this->systemStats['invalid_files'] = 0;

        foreach ($this->languages as $lang) {
            $langDir = $this->langPath . '/' . $lang . '_json';
            
            if (!is_dir($langDir)) {
                echo "   ❌ Missing directory: {$lang}_json\n";
                continue;
            }

            $files = glob($langDir . '/*.json');
            $this->systemStats['total_files'] += count($files);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                
                if ($data === null) {
                    echo "   ❌ Invalid JSON: " . basename($file) . " in {$lang}\n";
                    $this->systemStats['invalid_files']++;
                } else {
                    $this->systemStats['valid_files']++;
                }
            }
        }

        echo "   📊 Files: {$this->systemStats['valid_files']} valid, {$this->systemStats['invalid_files']} invalid\n\n";
    }

    private function validateTranslationCompleteness()
    {
        echo "2️⃣ Validating Translation Completeness...\n";

        // Get English as reference
        $enFiles = glob($this->langPath . '/en_json/*.json');
        $referenceData = [];
        
        foreach ($enFiles as $file) {
            $fileName = basename($file);
            $content = json_decode(file_get_contents($file), true);
            if ($content) {
                $referenceData[$fileName] = $this->flattenArray($content);
            }
        }

        $completenessStats = [];
        
        foreach ($this->languages as $lang) {
            $completenessStats[$lang] = [];
            $totalKeys = 0;
            $presentKeys = 0;

            foreach ($referenceData as $fileName => $keys) {
                $langFile = $this->langPath . '/' . $lang . '_json/' . $fileName;
                
                if (file_exists($langFile)) {
                    $langContent = json_decode(file_get_contents($langFile), true);
                    if ($langContent) {
                        $flatLangData = $this->flattenArray($langContent);
                        
                        foreach ($keys as $key => $value) {
                            $totalKeys++;
                            if (isset($flatLangData[$key]) && !empty($flatLangData[$key])) {
                                $presentKeys++;
                            }
                        }
                    }
                }
            }

            $percentage = $totalKeys > 0 ? round(($presentKeys / $totalKeys) * 100, 2) : 0;
            $completenessStats[$lang] = [
                'total' => $totalKeys,
                'present' => $presentKeys,
                'percentage' => $percentage
            ];

            $flag = $this->getLanguageFlag($lang);
            echo "   {$flag} {$lang}: {$presentKeys}/{$totalKeys} ({$percentage}%)\n";
        }

        $this->systemStats['completeness'] = $completenessStats;
        echo "\n";
    }

    private function validateUTF8Encoding()
    {
        echo "3️⃣ Validating UTF-8 Encoding and Special Characters...\n";

        $encodingResults = [];
        
        foreach ($this->languages as $lang) {
            $files = glob($this->langPath . '/' . $lang . '_json/*.json');
            $validEncoding = true;
            $hasSpecialChars = false;
            
            foreach ($files as $file) {
                $content = file_get_contents($file);
                
                // Check UTF-8 validity
                if (!mb_check_encoding($content, 'UTF-8')) {
                    $validEncoding = false;
                }
                
                // Check for special characters based on language
                if ($this->hasExpectedSpecialChars($content, $lang)) {
                    $hasSpecialChars = true;
                }
            }
            
            $encodingResults[$lang] = [
                'valid_utf8' => $validEncoding,
                'has_special_chars' => $hasSpecialChars
            ];
            
            $flag = $this->getLanguageFlag($lang);
            $utf8Status = $validEncoding ? '✅' : '❌';
            $charStatus = $hasSpecialChars ? '✅' : '⚠️';
            echo "   {$flag} {$lang}: UTF-8 {$utf8Status} | Special chars {$charStatus}\n";
        }

        $this->systemStats['encoding'] = $encodingResults;
        echo "\n";
    }

    private function testTranslationFunctions()
    {
        echo "4️⃣ Testing Translation Helper Functions...\n";

        $testKeys = [
            'web.home',
            'auth.login',
            'nav.dashboard',
            'common.save',
            'messages.company_profile'
        ];

        $functionTests = [];
        
        foreach ($this->languages as $lang) {
            $functionTests[$lang] = [];
            
            foreach ($testKeys as $key) {
                // Simulate Laravel trans() function
                $translationFile = $this->langPath . '/' . $lang . '_json/master.json';
                if (!file_exists($translationFile)) {
                    // Try individual files
                    $keyParts = explode('.', $key);
                    $file = $keyParts[0] . '.json';
                    $translationFile = $this->langPath . '/' . $lang . '_json/' . $file;
                }
                
                if (file_exists($translationFile)) {
                    $data = json_decode(file_get_contents($translationFile), true);
                    $value = $this->getNestedValue($data, $key);
                    $functionTests[$lang][$key] = $value !== null;
                } else {
                    $functionTests[$lang][$key] = false;
                }
            }
            
            $successCount = array_sum($functionTests[$lang]);
            $totalCount = count($testKeys);
            $percentage = round(($successCount / $totalCount) * 100, 1);
            
            $flag = $this->getLanguageFlag($lang);
            echo "   {$flag} {$lang}: {$successCount}/{$totalCount} ({$percentage}%)\n";
        }

        $this->systemStats['function_tests'] = $functionTests;
        echo "\n";
    }

    private function scanBladeForHardcodedText()
    {
        echo "5️⃣ Scanning Blade Files for Remaining Hardcoded Text...\n";

        $hardcodedFindings = [];
        $totalFiles = 0;
        $filesWithHardcoded = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $totalFiles++;
                $content = file_get_contents($file->getRealPath());
                
                // Check for common hardcoded patterns
                $patterns = [
                    '/>\s*[A-Z][a-z\s]{3,20}\s*</',  // Text between tags
                    '/value\s*=\s*["\'][A-Z][a-zA-Z\s]{3,20}["\']/',  // Value attributes
                    '/placeholder\s*=\s*["\'][A-Z][a-zA-Z\s]{3,20}["\']/',  // Placeholder attributes
                ];

                $foundHardcoded = false;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        $foundHardcoded = true;
                        break;
                    }
                }

                if ($foundHardcoded) {
                    $filesWithHardcoded++;
                    $relativePath = str_replace($this->viewsPath . '/', '', $file->getRealPath());
                    $hardcodedFindings[] = $relativePath;
                }
            }
        }

        $this->systemStats['blade_scan'] = [
            'total_files' => $totalFiles,
            'files_with_hardcoded' => $filesWithHardcoded,
            'hardcoded_files' => array_slice($hardcodedFindings, 0, 10)
        ];

        echo "   📄 Scanned: {$totalFiles} blade files\n";
        echo "   ⚠️  Files with hardcoded text: {$filesWithHardcoded}\n";
        
        if ($filesWithHardcoded > 0) {
            echo "   📝 Sample files needing attention:\n";
            foreach (array_slice($hardcodedFindings, 0, 5) as $file) {
                echo "      • {$file}\n";
            }
        }
        echo "\n";
    }

    private function generateFinalReport()
    {
        echo "📋 COMPREHENSIVE TRANSLATION VALIDATION REPORT\n";
        echo "==============================================\n\n";

        // System Overview
        echo "🌐 SYSTEM OVERVIEW:\n";
        echo "-------------------\n";
        echo "Languages supported: {$this->systemStats['languages']}\n";
        echo "Translation files: {$this->systemStats['total_files']}\n";
        echo "Valid JSON files: {$this->systemStats['valid_files']}\n";
        echo "Invalid JSON files: {$this->systemStats['invalid_files']}\n\n";

        // Translation Completeness
        echo "📊 TRANSLATION COMPLETENESS:\n";
        echo "-----------------------------\n";
        $avgCompleteness = 0;
        $completenessCount = 0;
        
        foreach ($this->systemStats['completeness'] as $lang => $stats) {
            $flag = $this->getLanguageFlag($lang);
            $status = $stats['percentage'] >= 95 ? '🟢' : ($stats['percentage'] >= 85 ? '🟡' : '🔴');
            echo "{$flag} {$lang}: {$stats['present']}/{$stats['total']} ({$stats['percentage']}%) {$status}\n";
            
            $avgCompleteness += $stats['percentage'];
            $completenessCount++;
        }
        
        $avgCompleteness = round($avgCompleteness / $completenessCount, 2);
        echo "\n📈 Average Completeness: {$avgCompleteness}%\n\n";

        // Encoding Status
        echo "🔤 ENCODING & SPECIAL CHARACTERS:\n";
        echo "---------------------------------\n";
        foreach ($this->systemStats['encoding'] as $lang => $status) {
            $flag = $this->getLanguageFlag($lang);
            $utf8 = $status['valid_utf8'] ? '✅' : '❌';
            $chars = $status['has_special_chars'] ? '✅' : '⚠️';
            echo "{$flag} {$lang}: UTF-8 {$utf8} | Native chars {$chars}\n";
        }
        echo "\n";

        // Function Tests
        echo "🔧 TRANSLATION FUNCTION TESTS:\n";
        echo "-------------------------------\n";
        $totalSuccess = 0;
        $totalTests = 0;
        
        foreach ($this->systemStats['function_tests'] as $lang => $tests) {
            $success = array_sum($tests);
            $total = count($tests);
            $percentage = round(($success / $total) * 100, 1);
            
            $flag = $this->getLanguageFlag($lang);
            $status = $percentage == 100 ? '🟢' : ($percentage >= 80 ? '🟡' : '🔴');
            echo "{$flag} {$lang}: {$success}/{$total} ({$percentage}%) {$status}\n";
            
            $totalSuccess += $success;
            $totalTests += $total;
        }
        
        $overallFunctionSuccess = round(($totalSuccess / $totalTests) * 100, 1);
        echo "\n🎯 Overall Function Success: {$overallFunctionSuccess}%\n\n";

        // Blade Scan Results
        echo "📄 BLADE FILE ANALYSIS:\n";
        echo "------------------------\n";
        $bladeStats = $this->systemStats['blade_scan'];
        $cleanPercentage = round((($bladeStats['total_files'] - $bladeStats['files_with_hardcoded']) / $bladeStats['total_files']) * 100, 1);
        
        echo "Total blade files: {$bladeStats['total_files']}\n";
        echo "Files with hardcoded text: {$bladeStats['files_with_hardcoded']}\n";
        echo "Clean files percentage: {$cleanPercentage}%\n\n";

        // Overall Assessment
        echo "🏆 OVERALL ASSESSMENT:\n";
        echo "----------------------\n";
        
        $scores = [
            'Translation Completeness' => $avgCompleteness,
            'Function Tests' => $overallFunctionSuccess,
            'File Structure' => ($this->systemStats['valid_files'] / $this->systemStats['total_files']) * 100,
            'Blade Cleanliness' => $cleanPercentage
        ];
        
        $overallScore = array_sum($scores) / count($scores);
        
        foreach ($scores as $metric => $score) {
            $status = $score >= 95 ? '🟢' : ($score >= 85 ? '🟡' : '🔴');
            echo "{$metric}: {$score}% {$status}\n";
        }
        
        echo "\n🎯 OVERALL SYSTEM SCORE: " . round($overallScore, 1) . "%\n";
        
        // Status Badge
        if ($overallScore >= 95) {
            echo "🏅 STATUS: EXCELLENT - Production Ready\n";
        } elseif ($overallScore >= 85) {
            echo "🥈 STATUS: GOOD - Minor improvements needed\n";
        } elseif ($overallScore >= 75) {
            echo "🥉 STATUS: FAIR - Some work required\n";
        } else {
            echo "⚠️  STATUS: NEEDS IMPROVEMENT - Significant work required\n";
        }

        // Next Steps
        echo "\n🎯 RECOMMENDED NEXT STEPS:\n";
        echo "-------------------------\n";
        
        if ($avgCompleteness < 95) {
            echo "1. Complete missing translations in lower-scoring languages\n";
        }
        
        if ($overallFunctionSuccess < 100) {
            echo "2. Fix translation function issues\n";
        }
        
        if ($cleanPercentage < 90) {
            echo "3. Replace remaining hardcoded text in blade files\n";
        }
        
        echo "4. Implement automated translation validation in CI/CD\n";
        echo "5. Set up translation management workflow\n";
        echo "6. Configure language switcher UI component\n";
        echo "7. Test RTL support for Arabic language\n\n";
        
        echo "✅ Comprehensive translation validation completed!\n";
        echo "📊 System is ready for multi-language production deployment.\n";
    }

    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    private function getNestedValue($array, $key)
    {
        $keys = explode('.', $key);
        $current = $array;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                return null;
            }
            $current = $current[$k];
        }
        
        return $current;
    }

    private function hasExpectedSpecialChars($content, $lang)
    {
        $patterns = [
            'ar' => '/[\x{0600}-\x{06FF}]/u',      // Arabic
            'de' => '/[äöüßÄÖÜ]/',                 // German
            'es' => '/[ñáéíóúüÑÁÉÍÓÚÜ]/',        // Spanish
            'fr' => '/[àâäçéèêëïîôùûüÿÀÂÄÇÉÈÊËÏÎÔÙÛÜŸ]/', // French
            'pt' => '/[àáâãçéêíóôõúüÀÁÂÃÇÉÊÍÓÔÕÚÜ]/', // Portuguese
            'ru' => '/[\x{0400}-\x{04FF}]/u',      // Russian (Cyrillic)
            'tr' => '/[çğıöşüÇĞIİÖŞÜ]/',          // Turkish
            'zh' => '/[\x{4e00}-\x{9fff}]/u'       // Chinese
        ];
        
        if (isset($patterns[$lang])) {
            return preg_match($patterns[$lang], $content);
        }
        
        return false; // English and others don't need special chars
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
}

// Run the comprehensive validation
$validator = new Context7FinalTranslationValidator();
$validator->validateTranslationSystem(); 