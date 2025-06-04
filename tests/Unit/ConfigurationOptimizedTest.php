<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class ConfigurationOptimizedTest extends UnitTestCase
{
    /** @test */
    public function it_has_basic_environment_variables()
    {
        // Test that basic environment variables or server variables are accessible
        $envExists = !empty($_ENV) || !empty($_SERVER);
        $this->assertTrue($envExists, 'Either $_ENV or $_SERVER should have values');
        
        // Test basic PHP configuration
        $this->assertIsString(php_sapi_name());
        $this->assertIsString(phpversion());
    }

    /** @test */
    public function it_has_memory_configuration()
    {
        $memoryLimit = ini_get('memory_limit');
        $this->assertNotEmpty($memoryLimit);
        $this->assertIsString($memoryLimit);
    }

    /** @test */
    public function it_has_timezone_configuration()
    {
        $timezone = date_default_timezone_get();
        $this->assertNotEmpty($timezone);
        $this->assertIsString($timezone);
    }

    /** @test */
    public function it_has_error_reporting_configuration()
    {
        $errorReporting = error_reporting();
        $this->assertIsInt($errorReporting);
    }

    /** @test */
    public function it_has_basic_php_extensions()
    {
        // Test common PHP extensions that Laravel requires
        $this->assertTrue(extension_loaded('json'));
        $this->assertTrue(extension_loaded('mbstring'));
        $this->assertTrue(function_exists('openssl_encrypt'));
    }

    /** @test */
    public function it_can_handle_basic_filesystem_operations()
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