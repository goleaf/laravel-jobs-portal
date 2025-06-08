<?php

/**
 * Universal Translation System Testing
 * Test all translation functionality and page coverage
 */

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class UniversalTranslationTester
{
    private $availableLanguages;
    private $testResults = [];
    
    public function __construct()
    {
        $this->availableLanguages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
        
        echo "🧪 Universal Translation System Testing\n";
        echo "=====================================\n\n";
    }

    public function runTests()
    {
        echo "🚀 Starting Translation System Tests...\n\n";

        $this->testTranslationHelper();
        $this->testLanguageConfiguration();
        $this->testTranslationLoading();
        $this->testRTLLanguageSwitch();
        $this->testPageTranslationCoverage();
        $this->generateTestReport();
    }

    private function testTranslationHelper()
    {
        echo "1️⃣ Testing Translation Helper Functions...\n";
        
        try {
            // Test basic translation function
            $testKey = 'messages.dashboard';
            
            foreach ($this->availableLanguages as $lang) {
                app()->setLocale($lang);
                $translation = trans($testKey);
                
                if ($translation !== $testKey) {
                    echo "   ✅ {$lang}: {$testKey} = '{$translation}'\n";
                    $this->testResults['helper'][$lang] = true;
                } else {
                    echo "   ❌ {$lang}: Translation missing for '{$testKey}'\n";
                    $this->testResults['helper'][$lang] = false;
                }
            }
            
            // Test pluralization
            echo "\n   🔢 Testing Pluralization:\n";
            app()->setLocale('en');
            $single = trans_choice('item|items', 1);
            $plural = trans_choice('item|items', 5);
            echo "   ✅ Singular: {$single}, Plural: {$plural}\n";
            
        } catch (Exception $e) {
            echo "   ❌ Translation helper error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    private function testLanguageConfiguration()
    {
        echo "2️⃣ Testing Language Configuration...\n";
        
        try {
            // Test language config
            $languages = config('languages.available_languages', []);
            $rtlLanguages = config('languages.rtl_languages', []);
            
            echo "   📋 Available languages: " . implode(', ', array_keys($languages)) . "\n";
            echo "   ↔️  RTL languages: " . implode(', ', $rtlLanguages) . "\n";
            
            foreach ($this->availableLanguages as $lang) {
                if (isset($languages[$lang])) {
                    echo "   ✅ {$lang}: " . $languages[$lang]['name'] . " (" . $languages[$lang]['native'] . ")\n";
                    $this->testResults['config'][$lang] = true;
                } else {
                    echo "   ❌ {$lang}: Missing in configuration\n";
                    $this->testResults['config'][$lang] = false;
                }
            }
            
        } catch (Exception $e) {
            echo "   ❌ Configuration error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    private function testTranslationLoading()
    {
        echo "3️⃣ Testing Translation File Loading...\n";
        
        $testKeys = [
            'messages.dashboard',
            'messages.jobs',
            'messages.candidates',
            'auth.failed',
            'validation.required',
            'web.welcome',
            'js.confirm'
        ];

        foreach ($this->availableLanguages as $lang) {
            echo "   🔍 Testing {$lang} translations:\n";
            app()->setLocale($lang);
            
            $loadedCount = 0;
            foreach ($testKeys as $key) {
                $translation = trans($key);
                if ($translation !== $key) {
                    $loadedCount++;
                }
            }
            
            $percentage = round(($loadedCount / count($testKeys)) * 100, 1);
            echo "      📊 Loaded: {$loadedCount}/" . count($testKeys) . " ({$percentage}%)\n";
            
            if ($loadedCount === count($testKeys)) {
                echo "      ✅ All key translations loaded\n";
                $this->testResults['loading'][$lang] = true;
            } else {
                echo "      ⚠️  Some translations missing\n";
                $this->testResults['loading'][$lang] = false;
            }
            echo "\n";
        }
    }

    private function testRTLLanguageSwitch()
    {
        echo "4️⃣ Testing RTL Language Switch...\n";
        
        try {
            // Test Arabic (RTL) language
            app()->setLocale('ar');
            $isRtl = in_array('ar', config('languages.rtl_languages', []));
            
            if ($isRtl) {
                echo "   ✅ Arabic detected as RTL language\n";
                echo "   📝 Sample Arabic text: " . trans('messages.dashboard') . "\n";
                $this->testResults['rtl']['ar'] = true;
            } else {
                echo "   ❌ Arabic not configured as RTL\n";
                $this->testResults['rtl']['ar'] = false;
            }
            
            // Test LTR language
            app()->setLocale('en');
            $isRtl = in_array('en', config('languages.rtl_languages', []));
            
            if (!$isRtl) {
                echo "   ✅ English correctly configured as LTR\n";
                $this->testResults['rtl']['en'] = true;
            } else {
                echo "   ❌ English incorrectly configured as RTL\n";
                $this->testResults['rtl']['en'] = false;
            }
            
        } catch (Exception $e) {
            echo "   ❌ RTL test error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    private function testPageTranslationCoverage()
    {
        echo "5️⃣ Testing Page Translation Coverage...\n";
        
        // Test key pages and their translation requirements
        $keyPages = [
            'home' => ['web.welcome', 'web.find_job', 'web.post_job'],
            'jobs' => ['messages.jobs', 'messages.job_categories', 'messages.job_types'],
            'candidates' => ['messages.candidates', 'messages.candidate_profile'],
            'companies' => ['messages.companies', 'messages.company_profile'],
            'dashboard' => ['messages.dashboard', 'messages.profile', 'messages.settings'],
            'auth' => ['auth.login', 'auth.register', 'auth.password']
        ];

        foreach ($keyPages as $page => $requiredKeys) {
            echo "   📄 Testing {$page} page translations:\n";
            
            foreach ($this->availableLanguages as $lang) {
                app()->setLocale($lang);
                $translatedCount = 0;
                
                foreach ($requiredKeys as $key) {
                    $translation = trans($key);
                    if ($translation !== $key) {
                        $translatedCount++;
                    }
                }
                
                $coverage = round(($translatedCount / count($requiredKeys)) * 100, 1);
                
                if ($coverage >= 90) {
                    echo "      ✅ {$lang}: {$coverage}% coverage\n";
                } elseif ($coverage >= 70) {
                    echo "      ⚠️  {$lang}: {$coverage}% coverage\n";
                } else {
                    echo "      ❌ {$lang}: {$coverage}% coverage\n";
                }
                
                $this->testResults['pages'][$page][$lang] = $coverage;
            }
            echo "\n";
        }
    }

    private function generateTestReport()
    {
        echo "📋 TRANSLATION SYSTEM TEST REPORT\n";
        echo "=================================\n\n";

        // Summary
        echo "📊 SUMMARY:\n";
        echo "-----------\n";
        
        $totalTests = 0;
        $passedTests = 0;
        
        // Helper tests
        if (isset($this->testResults['helper'])) {
            foreach ($this->testResults['helper'] as $result) {
                $totalTests++;
                if ($result) $passedTests++;
            }
        }
        
        // Loading tests
        if (isset($this->testResults['loading'])) {
            foreach ($this->testResults['loading'] as $result) {
                $totalTests++;
                if ($result) $passedTests++;
            }
        }
        
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
        
        echo "Total tests: {$totalTests}\n";
        echo "Passed tests: {$passedTests}\n";
        echo "Success rate: {$successRate}%\n\n";

        // Language Performance
        echo "🔤 LANGUAGE PERFORMANCE:\n";
        echo "------------------------\n";
        
        foreach ($this->availableLanguages as $lang) {
            $flag = $this->getLanguageFlag($lang);
            $name = $this->getLanguageName($lang);
            
            echo "{$flag} {$name} ({$lang}):\n";
            
            // Helper function test
            if (isset($this->testResults['helper'][$lang])) {
                echo "   🔧 Helper Functions: " . ($this->testResults['helper'][$lang] ? '✅' : '❌') . "\n";
            }
            
            // Loading test
            if (isset($this->testResults['loading'][$lang])) {
                echo "   📂 File Loading: " . ($this->testResults['loading'][$lang] ? '✅' : '❌') . "\n";
            }
            
            // RTL test (for Arabic)
            if ($lang === 'ar' && isset($this->testResults['rtl'][$lang])) {
                echo "   ↔️  RTL Support: " . ($this->testResults['rtl'][$lang] ? '✅' : '❌') . "\n";
            }
            
            // Page coverage average
            if (isset($this->testResults['pages'])) {
                $totalCoverage = 0;
                $pageCount = 0;
                
                foreach ($this->testResults['pages'] as $page => $languages) {
                    if (isset($languages[$lang])) {
                        $totalCoverage += $languages[$lang];
                        $pageCount++;
                    }
                }
                
                if ($pageCount > 0) {
                    $avgCoverage = round($totalCoverage / $pageCount, 1);
                    echo "   📄 Page Coverage: {$avgCoverage}%\n";
                }
            }
            echo "\n";
        }

        // Page Coverage Details
        if (isset($this->testResults['pages'])) {
            echo "📄 PAGE TRANSLATION COVERAGE:\n";
            echo "------------------------------\n";
            
            foreach ($this->testResults['pages'] as $page => $languages) {
                echo "📄 {$page} page:\n";
                
                foreach ($languages as $lang => $coverage) {
                    $flag = $this->getLanguageFlag($lang);
                    $status = $coverage >= 90 ? '✅' : ($coverage >= 70 ? '⚠️' : '❌');
                    echo "   {$flag} {$lang}: {$coverage}% {$status}\n";
                }
                echo "\n";
            }
        }

        // Final Status
        echo "🏁 FINAL STATUS:\n";
        echo "----------------\n";
        
        if ($successRate >= 90) {
            echo "🎉 EXCELLENT: Translation system is working perfectly!\n";
        } elseif ($successRate >= 70) {
            echo "👍 GOOD: Translation system is working well with minor issues.\n";
        } elseif ($successRate >= 50) {
            echo "⚠️  WARNING: Translation system has some issues that need attention.\n";
        } else {
            echo "❌ CRITICAL: Translation system has significant issues.\n";
        }
        
        echo "\n🔧 Translation system functionality: " . ($successRate >= 80 ? "OPERATIONAL" : "NEEDS ATTENTION") . "\n";
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
            'ar' => 'Arabic',
            'de' => 'German',
            'es' => 'Spanish',
            'fr' => 'French',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'tr' => 'Turkish',
            'zh' => 'Chinese'
        ];
        return $names[$lang] ?? $lang;
    }
}

// Run tests
try {
    $tester = new UniversalTranslationTester();
    $tester->runTests();
} catch (Exception $e) {
    echo "❌ Test execution error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}