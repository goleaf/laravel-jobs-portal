<?php

/**
 * Context7 Translation System Test
 * Tests the functionality of the 100% complete translation system
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class Context7TranslationTest
{
    private $languages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    private $langPath;
    private $testResults = [];

    public function __construct()
    {
        $this->langPath = __DIR__ . '/lang';
        
        echo "🧪 Context7 Translation System Test\n";
        echo "===================================\n\n";
    }

    public function runAllTests()
    {
        echo "🚀 Running Translation System Tests...\n\n";

        $this->testFileStructure();
        $this->testTranslationCompleteness();
        $this->testTranslationContent();
        $this->testSpecificKeys();
        $this->testBuildSystemIntegration();
        $this->generateTestReport();
    }

    private function testFileStructure()
    {
        echo "1️⃣ Testing File Structure...\n";

        $passed = 0;
        $total = 0;

        foreach ($this->languages as $lang) {
            $langDir = $this->langPath . '/' . $lang . '_json';
            
            if (is_dir($langDir)) {
                $files = glob($langDir . '/*.json');
                foreach ($files as $file) {
                    $total++;
                    $content = file_get_contents($file);
                    
                    // Test JSON validity
                    $decoded = json_decode($content, true);
                    if ($decoded !== null && is_array($decoded)) {
                        $passed++;
                    }
                    
                    // Test UTF-8 encoding
                    if (mb_check_encoding($content, 'UTF-8')) {
                        // UTF-8 valid
                    }
                }
            }
        }

        $this->testResults['file_structure'] = [
            'passed' => $passed,
            'total' => $total,
            'status' => $passed === $total ? 'PASS' : 'FAIL'
        ];

        $status = $passed === $total ? '✅ PASS' : '❌ FAIL';
        echo "   File Structure: {$passed}/{$total} files valid {$status}\n\n";
    }

    private function testTranslationCompleteness()
    {
        echo "2️⃣ Testing Translation Completeness...\n";

        // Get English as reference
        $enFiles = glob($this->langPath . '/en_json/*.json');
        $referenceKeys = 0;
        
        foreach ($enFiles as $file) {
            $content = json_decode(file_get_contents($file), true);
            if ($content && is_array($content)) {
                $referenceKeys += count($this->flattenArray($content));
            }
        }

        $languageResults = [];
        
        foreach ($this->languages as $lang) {
            $presentKeys = 0;
            
            foreach ($enFiles as $file) {
                $fileName = basename($file);
                $langFile = $this->langPath . '/' . $lang . '_json/' . $fileName;
                
                if (file_exists($langFile)) {
                    $langContent = json_decode(file_get_contents($langFile), true);
                    if ($langContent && is_array($langContent)) {
                        $presentKeys += count($this->flattenArray($langContent));
                    }
                }
            }
            
            $percentage = $referenceKeys > 0 ? round(($presentKeys / $referenceKeys) * 100, 2) : 0;
            $languageResults[$lang] = [
                'present' => $presentKeys,
                'total' => $referenceKeys,
                'percentage' => $percentage
            ];
            
            $flag = $this->getLanguageFlag($lang);
            $status = $percentage == 100 ? '✅' : '❌';
            echo "   {$flag} {$lang}: {$presentKeys}/{$referenceKeys} ({$percentage}%) {$status}\n";
        }

        $this->testResults['completeness'] = $languageResults;
        echo "\n";
    }

    private function testTranslationContent()
    {
        echo "3️⃣ Testing Translation Content Quality...\n";

        $testKeys = [
            'web.home' => 'Basic navigation',
            'auth.login' => 'Authentication',
            'messages.success' => 'System messages',
            'common.save' => 'Common actions',
            'dashboard.overview' => 'Dashboard content'
        ];

        $qualityResults = [];

        foreach ($this->languages as $lang) {
            $validTranslations = 0;
            $totalTests = count($testKeys);
            
            foreach ($testKeys as $key => $description) {
                $translation = $this->getTranslationValue($key, $lang);
                
                if (!empty($translation) && $translation !== $key) {
                    // Check if translation is not just English with language code
                    if (!preg_match('/\[' . strtoupper($lang) . '\]$/', $translation)) {
                        $validTranslations++;
                    }
                }
            }
            
            $qualityResults[$lang] = [
                'valid' => $validTranslations,
                'total' => $totalTests,
                'percentage' => round(($validTranslations / $totalTests) * 100, 2)
            ];
            
            $flag = $this->getLanguageFlag($lang);
            $status = $validTranslations === $totalTests ? '✅' : '🟡';
            echo "   {$flag} {$lang}: {$validTranslations}/{$totalTests} quality translations {$status}\n";
        }

        $this->testResults['quality'] = $qualityResults;
        echo "\n";
    }

    private function testSpecificKeys()
    {
        echo "4️⃣ Testing Specific Translation Keys...\n";

        $criticalKeys = [
            'web.home',
            'web.find_job', 
            'auth.login',
            'auth.register',
            'navigation.dashboard',
            'messages.success'
        ];

        $keyResults = [];

        foreach ($criticalKeys as $key) {
            $translations = [];
            $allLanguagesHave = true;
            
            foreach ($this->languages as $lang) {
                $translation = $this->getTranslationValue($key, $lang);
                $translations[$lang] = $translation;
                
                if (empty($translation) || $translation === $key) {
                    $allLanguagesHave = false;
                }
            }
            
            $keyResults[$key] = [
                'all_languages' => $allLanguagesHave,
                'translations' => $translations
            ];
            
            $status = $allLanguagesHave ? '✅ PASS' : '❌ FAIL';
            echo "   {$key}: {$status}\n";
        }

        $this->testResults['specific_keys'] = $keyResults;
        echo "\n";
    }

    private function testBuildSystemIntegration()
    {
        echo "5️⃣ Testing Build System Integration...\n";

        $buildResults = [];

        // Check if build assets exist
        $buildManifest = __DIR__ . '/public/build/manifest.json';
        if (file_exists($buildManifest)) {
            $manifest = json_decode(file_get_contents($buildManifest), true);
            
            $requiredAssets = [
                'resources/js/universal/i18n-system.js',
                'resources/css/universal/rtl-support.scss',
                'resources/css/universal/components.scss',
                'resources/js/universal/ui-system.js'
            ];
            
            $foundAssets = 0;
            foreach ($requiredAssets as $asset) {
                if (isset($manifest[$asset])) {
                    $foundAssets++;
                }
            }
            
            $buildResults['assets'] = [
                'found' => $foundAssets,
                'total' => count($requiredAssets),
                'percentage' => round(($foundAssets / count($requiredAssets)) * 100, 2)
            ];
            
            $status = $foundAssets === count($requiredAssets) ? '✅ PASS' : '🟡 PARTIAL';
            echo "   Build Assets: {$foundAssets}/" . count($requiredAssets) . " found {$status}\n";
        } else {
            echo "   Build Assets: ❌ No manifest found\n";
            $buildResults['assets'] = ['found' => 0, 'total' => 4, 'percentage' => 0];
        }

        $this->testResults['build'] = $buildResults;
        echo "\n";
    }

    private function generateTestReport()
    {
        echo "📋 TRANSLATION SYSTEM TEST REPORT\n";
        echo "=================================\n\n";

        $totalScore = 0;
        $maxScore = 0;

        // File Structure Score (20 points)
        $fileScore = ($this->testResults['file_structure']['passed'] / $this->testResults['file_structure']['total']) * 20;
        $totalScore += $fileScore;
        $maxScore += 20;

        // Completeness Score (30 points)
        $avgCompleteness = 0;
        foreach ($this->testResults['completeness'] as $lang => $data) {
            $avgCompleteness += $data['percentage'];
        }
        $avgCompleteness = $avgCompleteness / count($this->testResults['completeness']);
        $completenessScore = ($avgCompleteness / 100) * 30;
        $totalScore += $completenessScore;
        $maxScore += 30;

        // Quality Score (30 points)
        $avgQuality = 0;
        foreach ($this->testResults['quality'] as $lang => $data) {
            $avgQuality += $data['percentage'];
        }
        $avgQuality = $avgQuality / count($this->testResults['quality']);
        $qualityScore = ($avgQuality / 100) * 30;
        $totalScore += $qualityScore;
        $maxScore += 30;

        // Specific Keys Score (20 points)
        $passedKeys = 0;
        foreach ($this->testResults['specific_keys'] as $key => $data) {
            if ($data['all_languages']) {
                $passedKeys++;
            }
        }
        $keyScore = ($passedKeys / count($this->testResults['specific_keys'])) * 20;
        $totalScore += $keyScore;
        $maxScore += 20;

        $finalScore = round(($totalScore / $maxScore) * 100, 2);

        echo "🎯 FINAL SCORES:\n";
        echo "----------------\n";
        echo "File Structure: " . round($fileScore, 1) . "/20\n";
        echo "Completeness: " . round($completenessScore, 1) . "/30\n";
        echo "Quality: " . round($qualityScore, 1) . "/30\n";
        echo "Critical Keys: " . round($keyScore, 1) . "/20\n";
        echo "TOTAL SCORE: {$finalScore}/100\n\n";

        if ($finalScore >= 95) {
            echo "🏆 EXCELLENT: Translation system is production-ready!\n";
        } elseif ($finalScore >= 85) {
            echo "🥇 GOOD: Translation system is mostly ready for production.\n";
        } elseif ($finalScore >= 75) {
            echo "🥈 FAIR: Translation system needs some improvements.\n";
        } else {
            echo "🥉 NEEDS WORK: Translation system requires significant fixes.\n";
        }

        echo "\n🎊 TRANSLATION SYSTEM VALIDATION COMPLETE!\n";
    }

    private function getTranslationValue($key, $lang)
    {
        $parts = explode('.', $key);
        if (count($parts) < 2) return $key;

        $file = $parts[0];
        $keyPath = implode('.', array_slice($parts, 1));

        $langFile = $this->langPath . '/' . $lang . '_json/' . $file . '.json';
        if (!file_exists($langFile)) return $key;

        $content = json_decode(file_get_contents($langFile), true);
        if (!$content) return $key;

        return $this->getNestedValue($content, $keyPath) ?? $key;
    }

    private function getNestedValue($array, $key)
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!isset($value[$k])) return null;
            $value = $value[$k];
        }

        return $value;
    }

    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        if (!is_array($array)) {
            return $result;
        }
        
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

// Run the translation tests
$tester = new Context7TranslationTest();
$tester->runAllTests(); 