<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class HelperFunctionsTest extends TestCase
{
    /**
     * Test settings() function returns expected structure
     */
    public function test_settings_function_returns_expected_structure()
    {
        $settings = settings();
        
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('app_name', $settings);
        $this->assertArrayHasKey('favicon', $settings);
        $this->assertArrayHasKey('default_country_code', $settings);
        $this->assertArrayHasKey('logo', $settings);
    }

    /**
     * Test getAppName() function returns string
     */
    public function test_get_app_name_function_returns_string()
    {
        $appName = getAppName();
        
        $this->assertIsString($appName);
        $this->assertNotEmpty($appName);
    }

    /**
     * Test getSettingValue() function with existing key
     */
    public function test_get_setting_value_function_with_existing_key()
    {
        $favicon = getSettingValue('favicon');
        $appName = getSettingValue('app_name');
        $countryCode = getSettingValue('default_country_code');
        
        $this->assertEquals('/favicon.ico', $favicon);
        $this->assertIsString($appName);
        $this->assertEquals('US', $countryCode);
    }

    /**
     * Test getSettingValue() function with non-existing key
     */
    public function test_get_setting_value_function_with_non_existing_key()
    {
        $nonExistent = getSettingValue('non_existent_key');
        $withDefault = getSettingValue('non_existent_key', 'default_value');
        
        $this->assertNull($nonExistent);
        $this->assertEquals('default_value', $withDefault);
    }

    /**
     * Test googleJobSchema() function returns array
     */
    public function test_google_job_schema_function_returns_array()
    {
        $schema = googleJobSchema();
        
        $this->assertIsArray($schema);
        // For now, it should return empty array
        $this->assertEmpty($schema);
    }

    /**
     * Test formatCurrency() function
     */
    public function test_format_currency_function()
    {
        $formatted1 = formatCurrency(1000);
        $formatted2 = formatCurrency(1000.50);
        $formatted3 = formatCurrency(1000, 'EUR');
        
        $this->assertEquals('$1,000.00', $formatted1);
        $this->assertEquals('$1,000.50', $formatted2);
        // Function currently ignores currency parameter
        $this->assertEquals('$1,000.00', $formatted3);
    }

    /**
     * Test timeAgo() function with Carbon instance
     */
    public function test_time_ago_function_with_carbon()
    {
        $pastDate = Carbon::now()->subDays(2);
        $result = timeAgo($pastDate);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('days ago', $result);
    }

    /**
     * Test timeAgo() function with string date
     */
    public function test_time_ago_function_with_string()
    {
        $dateString = '2024-01-01 12:00:00';
        $result = timeAgo($dateString);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('ago', $result);
    }

    /**
     * Test truncateText() function
     */
    public function test_truncate_text_function()
    {
        $longText = 'This is a very long text that should be truncated when it exceeds the specified length limit.';
        
        $truncated1 = truncateText($longText, 50);
        $truncated2 = truncateText($longText, 20);
        $shortText = truncateText('Short text', 100);
        
        $this->assertLessThanOrEqual(53, strlen($truncated1)); // 50 + '...'
        $this->assertLessThanOrEqual(23, strlen($truncated2)); // 20 + '...'
        $this->assertEquals('Short text', $shortText);
    }

    /**
     * Test truncateText() function with default length
     */
    public function test_truncate_text_function_with_default_length()
    {
        $longText = str_repeat('A', 200);
        $truncated = truncateText($longText);
        
        $this->assertLessThanOrEqual(103, strlen($truncated)); // 100 + '...'
    }

    /**
     * Test that all helper functions exist
     */
    public function test_all_helper_functions_exist()
    {
        $this->assertTrue(function_exists('settings'));
        $this->assertTrue(function_exists('getAppName'));
        $this->assertTrue(function_exists('getSettingValue'));
        $this->assertTrue(function_exists('googleJobSchema'));
        $this->assertTrue(function_exists('formatCurrency'));
        $this->assertTrue(function_exists('timeAgo'));
        $this->assertTrue(function_exists('isActiveRoute'));
        $this->assertTrue(function_exists('truncateText'));
    }

    /**
     * Test formatCurrency with edge cases
     */
    public function test_format_currency_edge_cases()
    {
        $zero = formatCurrency(0);
        $negative = formatCurrency(-500);
        $large = formatCurrency(1000000);
        
        $this->assertEquals('$0.00', $zero);
        $this->assertEquals('$-500.00', $negative);
        $this->assertEquals('$1,000,000.00', $large);
    }

    /**
     * Test settings consistency
     */
    public function test_settings_consistency()
    {
        $settings1 = settings();
        $settings2 = settings();
        
        $this->assertEquals($settings1, $settings2);
        
        // Test that getAppName returns same as settings app_name
        $this->assertEquals($settings1['app_name'], getAppName());
        
        // Test that getSettingValue returns same as settings array
        $this->assertEquals($settings1['favicon'], getSettingValue('favicon'));
        $this->assertEquals($settings1['default_country_code'], getSettingValue('default_country_code'));
    }

    /**
     * Test helper functions with various data types
     */
    public function test_helper_functions_with_various_data_types()
    {
        // Test formatCurrency with float
        $float = formatCurrency(123.456);
        $this->assertEquals('$123.46', $float);
        
        // Test getSettingValue with different default types
        $stringDefault = getSettingValue('non_existent', 'string');
        $arrayDefault = getSettingValue('non_existent', []);
        $intDefault = getSettingValue('non_existent', 42);
        
        $this->assertEquals('string', $stringDefault);
        $this->assertEquals([], $arrayDefault);
        $this->assertEquals(42, $intDefault);
    }

    /**
     * Test performance of helper functions
     */
    public function test_helper_functions_performance()
    {
        $start = microtime(true);
        
        // Call functions multiple times
        for ($i = 0; $i < 1000; $i++) {
            settings();
            getAppName();
            getSettingValue('app_name');
            formatCurrency(100);
        }
        
        $end = microtime(true);
        $duration = $end - $start;
        
        // Should complete within reasonable time (1 second)
        $this->assertLessThan(1.0, $duration);
    }
} 