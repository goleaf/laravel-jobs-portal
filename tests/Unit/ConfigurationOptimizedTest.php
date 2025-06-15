<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ConfigurationOptimizedTest extends UnitTestCase
{
    /** @test */
    public function itHasBasicEnvironmentVariables()
    {
        // Test that basic environment variables or server variables are accessible
        $envExists = !empty($_ENV) || !empty($_SERVER);
        $this->assertTrue($envExists, 'Either $_ENV or $_SERVER should have values');

        // Test basic PHP configuration
        $this->assertIsString(php_sapi_name());
        $this->assertIsString(phpversion());
    }

    /** @test */
    public function itHasMemoryConfiguration()
    {
        $memoryLimit = ini_get('memory_limit');
        $this->assertNotEmpty($memoryLimit);
        $this->assertIsString($memoryLimit);
    }

    /** @test */
    public function itHasTimezoneConfiguration()
    {
        $timezone = date_default_timezone_get();
        $this->assertNotEmpty($timezone);
        $this->assertIsString($timezone);
    }

    /** @test */
    public function itHasErrorReportingConfiguration()
    {
        $errorReporting = error_reporting();
        $this->assertIsInt($errorReporting);
    }

    /** @test */
    public function itHasBasicPhpExtensions()
    {
        // Test common PHP extensions that Laravel requires
        $this->assertTrue(extension_loaded('json'));
        $this->assertTrue(extension_loaded('mbstring'));
        $this->assertTrue(function_exists('openssl_encrypt'));
    }

    /** @test */
    public function itCanHandleBasicFilesystemOperations()
    {
        // Test basic file operations without Laravel
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $this->assertNotFalse($tempFile);

        file_put_contents($tempFile, 'test content');
        $content = file_get_contents($tempFile);
        $this->assertEquals('test content', $content);

        unlink($tempFile);
        $this->assertFileDoesNotExist($tempFile);
    }
}
