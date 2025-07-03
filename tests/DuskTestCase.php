<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Enhanced pattern: Prepare test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Enhanced pattern: Ensure screenshots directory exists
        $screenshotsPath = base_path('tests/Browser/screenshots');
        if (! is_dir($screenshotsPath)) {
            mkdir($screenshotsPath, 0755, true);
        }

        // Enhanced pattern: Ensure console logs directory exists
        $consolePath = base_path('tests/Browser/console');
        if (! is_dir($consolePath)) {
            mkdir($consolePath, 0755, true);
        }
    }

    /**
     * Enhanced pattern: Clean up after tests.
     */
    protected function tearDown(): void
    {
        // Enhanced pattern: Clean up temporary Chrome user data directories
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $tempDir = sys_get_temp_dir();
            $chromeDirs = glob($tempDir.DIRECTORY_SEPARATOR.'chrome-dusk-*');
            foreach ($chromeDirs as $dir) {
                if (is_dir($dir) && filemtime($dir) < time() - 3600) { // Clean up dirs older than 1 hour
                    $this->removeDirectory($dir);
                }
            }
        }

        parent::tearDown();
    }

    /**
     * Prepare for Dusk test execution.
     */
    public static function prepare(): void
    {
        // Enhanced pattern: Increase memory limit for tests
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', '600');

        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Enhanced pattern: Override browse method with better error handling.
     */
    public function browse(\Closure $callback)
    {
        try {
            return parent::browse($callback);
        } catch (\Exception $e) {
            // Enhanced pattern: Enhanced error reporting for debugging
            $message = 'Dusk test failed: '.$e->getMessage();

            if (getenv('CI') || getenv('GITHUB_ACTIONS')) {
                $message .= "\n\nDebugging information:";
                $message .= "\n- PHP Version: ".PHP_VERSION;
                $message .= "\n- OS: ".PHP_OS;
                $message .= "\n- Memory Limit: ".ini_get('memory_limit');
                $message .= "\n- Driver URL: ".($_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515');
            }

            throw new \Exception($message, $e->getCode(), $e);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless=new',  // Use new headless mode
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1920,1080',
            '--disable-web-security',
            '--disable-features=VizDisplayCompositor',
            '--ignore-certificate-errors',
            '--ignore-ssl-errors',
            '--ignore-certificate-errors-spki-list',
            '--disable-background-timer-throttling',
            '--disable-renderer-backgrounding',
            '--disable-backgrounding-occluded-windows',
            '--disable-ipc-flooding-protection',
            '--disable-hang-monitor',
            '--disable-client-side-phishing-detection',
            '--disable-popup-blocking',
            '--disable-default-apps',
            '--disable-prompt-on-repost',
            '--disable-sync',
            '--disable-translate',
            '--disable-logging',
            '--disable-extensions',
            '--no-first-run',
            '--no-default-browser-check',
            '--remote-debugging-port=9222',
            '--disable-blink-features=AutomationControlled',
            '--disable-features=VizDisplayCompositor,VizServiceDisplayCompositor',
        ]);

        // Enhanced pattern: Add platform-specific configurations
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $options->addArguments([
                '--disable-features=VizDisplayCompositor',
                '--log-level=3',  // Reduce logging on Windows
                '--silent',
                '--user-data-dir='.sys_get_temp_dir().DIRECTORY_SEPARATOR.'chrome-dusk-'.time(),
                '--disable-software-rasterizer',
                '--disable-background-mode',
            ]);
        } else {
            $options->addArguments([
                '--disable-setuid-sandbox',
                '--single-process',  // Better for containers
            ]);
        }

        // Enhanced pattern: CI environment optimizations
        if (getenv('CI') || getenv('GITHUB_ACTIONS')) {
            $options->addArguments([
                '--disable-dev-shm-usage',
                '--no-zygote',
                '--single-process',
                '--disable-gpu-sandbox',
            ]);
        }

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        // Enhanced pattern: Enhanced timeouts for CI environments
        $capabilities->setCapability('timeouts', [
            'script' => 60000,      // 60 seconds for scripts
            'pageLoad' => 60000,    // 60 seconds for page loads
            'implicit' => 15000,    // 15 seconds for implicit waits
        ]);

        // Enhanced pattern: Improved logging for debugging
        $capabilities->setCapability('loggingPrefs', [
            'browser' => 'INFO',
            'driver' => 'INFO',
        ]);

        // Enhanced pattern: Add Chrome options for better stability
        $capabilities->setCapability('chrome.switches', [
            '--disable-blink-features=AutomationControlled',
            '--disable-extensions',
        ]);

        $driverUrl = $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515';

        try {
            return RemoteWebDriver::create($driverUrl, $capabilities);
        } catch (\Exception $e) {
            // Enhanced pattern: Better error reporting
            throw new \Exception(
                "Failed to create ChromeDriver connection to {$driverUrl}. "
                .'Error: '.$e->getMessage().'. '
                .'Please ensure ChromeDriver is running on the correct port.'
            );
        }
    }

    /**
     * Determine whether the tests are running in Sail.
     */
    protected static function runningInSail(): bool
    {
        return isset($_ENV['LARAVEL_SAIL']) && $_ENV['LARAVEL_SAIL'] === '1';
    }

    /**
     * Enhanced pattern: Helper method to remove directory recursively.
     */
    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        try {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir.DIRECTORY_SEPARATOR.$file;
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
            rmdir($dir);
        } catch (\Exception $e) {
            // Ignore cleanup errors in CI environments
            if (! getenv('CI')) {
                throw $e;
            }
        }
    }
}
