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
     * Prepare for Dusk test execution.
     */
    public static function prepare(): void
    {
        // Context7 pattern: Increase memory limit for tests
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', '600');
        
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
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
        ]);

        // Context7 pattern: Add Windows-specific configurations
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $options->addArguments([
                '--disable-features=VizDisplayCompositor',
                '--enable-logging',
                '--log-level=0',
                '--user-data-dir=' . sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'chrome-dusk-' . time(),
            ]);
        }

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        
        // Context7 pattern: Enhanced timeouts for CI environments
        $capabilities->setCapability('timeouts', [
            'script' => 30000,
            'pageLoad' => 30000,
            'implicit' => 10000,
        ]);

        // Context7 pattern: Improved logging for debugging
        $capabilities->setCapability('loggingPrefs', [
            'browser' => 'ALL',
            'driver' => 'ALL',
        ]);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            $capabilities
        );
    }

    /**
     * Determine whether the tests are running in Sail.
     */
    protected static function runningInSail(): bool
    {
        return isset($_ENV['LARAVEL_SAIL']) && $_ENV['LARAVEL_SAIL'] === '1';
    }

    /**
     * Context7 pattern: Prepare test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Context7 pattern: Ensure screenshots directory exists
        $screenshotsPath = base_path('tests/Browser/screenshots');
        if (!is_dir($screenshotsPath)) {
            mkdir($screenshotsPath, 0755, true);
        }
        
        // Context7 pattern: Ensure console logs directory exists
        $consolePath = base_path('tests/Browser/console');
        if (!is_dir($consolePath)) {
            mkdir($consolePath, 0755, true);
        }
    }

    /**
     * Context7 pattern: Clean up after tests
     */
    protected function tearDown(): void
    {
        // Context7 pattern: Clean up temporary Chrome user data directories
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $tempDir = sys_get_temp_dir();
            $chromeDirs = glob($tempDir . DIRECTORY_SEPARATOR . 'chrome-dusk-*');
            foreach ($chromeDirs as $dir) {
                if (is_dir($dir) && filemtime($dir) < time() - 3600) { // Clean up dirs older than 1 hour
                    $this->removeDirectory($dir);
                }
            }
        }
        
        parent::tearDown();
    }

    /**
     * Context7 pattern: Helper method to remove directory recursively
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
} 