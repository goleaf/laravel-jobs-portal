<?php

/**
 * Translation System Test Script.
 *
 * This script validates the complete translation system implementation
 * to ensure all components are working correctly before deployment.
 */

require_once __DIR__.'/vendor/autoload.php';

/**
 * Translation System Tester.
 */
class TranslationSystemTester
{
    private array $results = [];
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;

    public function runAllTests(): void
    {
        echo "🌍 Translation System Validation Starting...\n\n";

        $this->testConfigurationSetup();
        $this->testLocaleAvailability();
        $this->testTranslationFiles();
        $this->testHelperFunctions();
        $this->testRTLSupport();
        $this->testCacheConfiguration();
        $this->testAPIEndpoints();
        $this->testCLICommands();
        $this->testFrontendIntegration();
        $this->testPerformance();

        $this->displayResults();
    }

    private function test(string $name, callable $test): void
    {
        ++$this->totalTests;
        echo "Testing: {$name}... ";

        try {
            $result = $test();
            if ($result) {
                echo "✅ PASS\n";
                ++$this->passedTests;
                $this->results[$name] = ['status' => 'PASS', 'message' => 'Success'];
            } else {
                echo "❌ FAIL\n";
                ++$this->failedTests;
                $this->results[$name] = ['status' => 'FAIL', 'message' => 'Test returned false'];
            }
        } catch (Exception $e) {
            echo "❌ ERROR: {$e->getMessage()}\n";
            ++$this->failedTests;
            $this->results[$name] = ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function testConfigurationSetup(): void
    {
        echo "📋 Testing Configuration Setup\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('App configuration has available_locales', function () {
            $locales = config('app.available_locales', []);

            return !empty($locales) && count($locales) >= 9;
        });

        $this->test('Translation configuration exists', function () {
            return file_exists(__DIR__.'/config/translation.php');
        });

        $this->test('Default locale is set', function () {
            $locale = config('app.locale');

            return !empty($locale) && 2 === strlen($locale);
        });

        $this->test('Fallback locale is configured', function () {
            $fallback = config('app.fallback_locale');

            return !empty($fallback) && 2 === strlen($fallback);
        });

        echo "\n";
    }

    private function testLocaleAvailability(): void
    {
        echo "🌐 Testing Locale Availability\n";
        echo '='.str_repeat('=', 50)."\n";

        $expectedLocales = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];

        foreach ($expectedLocales as $locale) {
            $this->test("Locale '{$locale}' is configured", function () use ($locale) {
                $availableLocales = config('app.available_locales', []);

                return isset($availableLocales[$locale]);
            });
        }

        $this->test('RTL configuration for Arabic', function () {
            $arConfig = config('app.available_locales.ar', []);

            return isset($arConfig['rtl']) && true === $arConfig['rtl'];
        });

        echo "\n";
    }

    private function testTranslationFiles(): void
    {
        echo "📁 Testing Translation Files\n";
        echo '='.str_repeat('=', 50)."\n";

        $locales = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];

        foreach ($locales as $locale) {
            $this->test("Translation directory for '{$locale}' exists", function () use ($locale) {
                return is_dir(__DIR__."/lang/{$locale}");
            });

            $this->test("JSON translation file for '{$locale}' exists", function () use ($locale) {
                return file_exists(__DIR__."/lang/{$locale}.json");
            });

            $this->test("Locale translation file for '{$locale}' exists", function () use ($locale) {
                return file_exists(__DIR__."/lang/{$locale}/locale.php");
            });

            $this->test("Messages translation file for '{$locale}' exists", function () use ($locale) {
                return file_exists(__DIR__."/lang/{$locale}/messages.php");
            });
        }

        echo "\n";
    }

    private function testHelperFunctions(): void
    {
        echo "🔧 Testing Helper Functions\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('trans_json function exists', function () {
            return function_exists('trans_json');
        });

        $this->test('is_rtl function exists', function () {
            return function_exists('is_rtl');
        });

        $this->test('lang_direction function exists', function () {
            return function_exists('lang_direction');
        });

        $this->test('locale_flag function exists', function () {
            return function_exists('locale_flag');
        });

        $this->test('trans_has function exists', function () {
            return function_exists('trans_has');
        });

        echo "\n";
    }

    private function testRTLSupport(): void
    {
        echo "↩️ Testing RTL Support\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('Arabic is detected as RTL', function () {
            return in_array('ar', config('translation.rtl_languages', []));
        });

        $this->test('English is not RTL', function () {
            return !in_array('en', config('translation.rtl_languages', []));
        });

        $this->test('RTL detection works correctly', function () {
            if (function_exists('is_rtl')) {
                return true === is_rtl('ar') && false === is_rtl('en');
            }

            return false;
        });

        echo "\n";
    }

    private function testCacheConfiguration(): void
    {
        echo "⚡ Testing Cache Configuration\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('Cache configuration exists', function () {
            $cacheConfig = config('translation.cache', []);

            return !empty($cacheConfig);
        });

        $this->test('Cache is enabled', function () {
            return true === config('translation.cache.enabled', false);
        });

        $this->test('Cache TTL is set', function () {
            $ttl = config('translation.cache.ttl', 0);

            return $ttl > 0;
        });

        $this->test('Redis cache store is configured', function () {
            return null !== config('cache.stores.redis');
        });

        echo "\n";
    }

    private function testAPIEndpoints(): void
    {
        echo "🔗 Testing API Structure\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('LocaleController exists', function () {
            return file_exists(__DIR__.'/app/Http/Controllers/LocaleController.php');
        });

        $this->test('TranslationManagerController exists', function () {
            return file_exists(__DIR__.'/app/Http/Controllers/TranslationManagerController.php');
        });

        $this->test('Routes are defined', function () {
            return file_exists(__DIR__.'/routes/web.php');
        });

        $this->test('API routes are defined', function () {
            return file_exists(__DIR__.'/routes/api.php');
        });

        echo "\n";
    }

    private function testCLICommands(): void
    {
        echo "⌨️ Testing CLI Commands\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('TranslationCommand exists', function () {
            return file_exists(__DIR__.'/app/Console/Commands/TranslationCommand.php');
        });

        $this->test('Command signature is properly defined', function () {
            $content = file_get_contents(__DIR__.'/app/Console/Commands/TranslationCommand.php');

            return false !== strpos($content, 'translation:manage');
        });

        echo "\n";
    }

    private function testFrontendIntegration(): void
    {
        echo "🌐 Testing Frontend Integration\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('Frontend translation.js exists', function () {
            return file_exists(__DIR__.'/resources/js/translation.js');
        });

        $this->test('Language switcher component exists', function () {
            return file_exists(__DIR__.'/resources/views/components/ui/language-switcher.blade.php');
        });

        $this->test('Translation JavaScript contains TranslationManager', function () {
            if (file_exists(__DIR__.'/resources/js/translation.js')) {
                $content = file_get_contents(__DIR__.'/resources/js/translation.js');

                return false !== strpos($content, 'class TranslationManager');
            }

            return false;
        });

        echo "\n";
    }

    private function testPerformance(): void
    {
        echo "🚀 Testing Performance Configuration\n";
        echo '='.str_repeat('=', 50)."\n";

        $this->test('Lazy loading is enabled', function () {
            return true === config('translation.loading.lazy', false);
        });

        $this->test('Critical namespaces are defined for preloading', function () {
            $namespaces = config('translation.loading.preload_namespaces', []);

            return !empty($namespaces) && in_array('common', $namespaces);
        });

        $this->test('Performance settings are optimized', function () {
            return true === config('translation.performance.prefetch_translations', false);
        });

        echo "\n";
    }

    private function displayResults(): void
    {
        echo '='.str_repeat('=', 60)."\n";
        echo "🎯 TRANSLATION SYSTEM TEST RESULTS\n";
        echo '='.str_repeat('=', 60)."\n\n";

        echo "📊 SUMMARY:\n";
        echo "   Total Tests: {$this->totalTests}\n";
        echo "   ✅ Passed: {$this->passedTests}\n";
        echo "   ❌ Failed: {$this->failedTests}\n";
        echo '   📈 Success Rate: '.round(($this->passedTests / $this->totalTests) * 100, 2)."%\n\n";

        if ($this->failedTests > 0) {
            echo "❌ FAILED TESTS:\n";
            echo '-'.str_repeat('-', 50)."\n";
            foreach ($this->results as $test => $result) {
                if ('PASS' !== $result['status']) {
                    echo "   • {$test}: {$result['message']}\n";
                }
            }
            echo "\n";
        }

        if (0 === $this->failedTests) {
            echo "🎉 ALL TESTS PASSED! Your translation system is ready for deployment.\n\n";

            echo "✅ DEPLOYMENT CHECKLIST:\n";
            echo "   □ Set REDIS_HOST and REDIS_PORT in .env\n";
            echo "   □ Configure TRANSLATION_CACHE_ENABLED=true\n";
            echo "   □ Set APP_LOCALE to your default language\n";
            echo "   □ Run: php artisan config:cache\n";
            echo "   □ Run: php artisan route:cache\n";
            echo "   □ Test language switching in browser\n";
            echo "   □ Verify RTL layout for Arabic\n";
            echo "   □ Monitor translation cache performance\n\n";

            echo "🚀 Your job portal is now fully internationalized!\n";
        } else {
            echo "⚠️ Some tests failed. Please fix the issues before deployment.\n\n";
        }

        echo "📚 For detailed usage instructions, see: TRANSLATION_SYSTEM_GUIDE.md\n";
        echo "🔧 For troubleshooting, see: MASTER_TRANSLATION_IMPLEMENTATION.php\n\n";
    }
}

// Check if running from command line
if ('cli' === php_sapi_name()) {
    $tester = new TranslationSystemTester();
    $tester->runAllTests();
} else {
    echo "This script should be run from the command line.\n";
    echo "Usage: php test_translation_system.php\n";
}
