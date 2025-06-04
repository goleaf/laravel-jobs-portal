<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperFunctionsTest extends TestCase
{
    public function test_settings_function_exists(): void
    {
        $this->assertTrue(function_exists('settings'));
    }

    public function test_settings_function_returns_collection(): void
    {
        $settings = settings();
        $this->assertIsObject($settings);
        $this->assertTrue(method_exists($settings, 'get'));
    }

    public function test_get_app_name_function(): void
    {
        $this->assertTrue(function_exists('getAppName'));
        $appName = getAppName();
        $this->assertIsString($appName);
        $this->assertNotEmpty($appName);
    }

    public function test_format_currency_function(): void
    {
        $this->assertTrue(function_exists('formatCurrency'));
        $this->assertEquals('$10.00', formatCurrency(10));
        $this->assertEquals('$0.00', formatCurrency(null));
        $this->assertEquals('$123.45', formatCurrency(123.45));
    }

    public function test_time_ago_function(): void
    {
        $this->assertTrue(function_exists('timeAgo'));
        $timeAgo = timeAgo(now()->subHour());
        $this->assertIsString($timeAgo);
        $this->assertStringContainsString('ago', $timeAgo);
    }

    public function test_truncate_text_function(): void
    {
        $this->assertTrue(function_exists('truncateText'));
        $longText = str_repeat('a', 200);
        $truncated = truncateText($longText, 50);
        $this->assertEquals(53, strlen($truncated)); // 50 + "..."
        $this->assertStringEndsWith('...', $truncated);
    }

    public function test_google_job_schema_function(): void
    {
        $this->assertTrue(function_exists('googleJobSchema'));
        $job = [
            'title' => 'Software Developer',
            'description' => 'Great job opportunity',
        ];
        $schema = googleJobSchema($job);
        $this->assertIsArray($schema);
        $this->assertEquals('JobPosting', $schema['@type']);
        $this->assertEquals('Software Developer', $schema['title']);
    }
}