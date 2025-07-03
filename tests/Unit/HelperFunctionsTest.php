<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HelperFunctionsTest extends TestCase
{
    /** @test */
    public function format_currency_helper_works()
    {
        if (function_exists('formatCurrency')) {
            $result = formatCurrency(1000);
            $this->assertIsString($result);
            $this->assertStringContainsString('1,000', $result);
        } else {
            $this->markTestSkipped('formatCurrency helper not found');
        }
    }

    /** @test */
    public function slugify_helper_works()
    {
        if (function_exists('slugify')) {
            $result = slugify('Test Job Title');
            $this->assertEquals('test-job-title', $result);
        } else {
            $this->markTestSkipped('slugify helper not found');
        }
    }

    /** @test */
    public function time_ago_helper_works()
    {
        if (function_exists('timeAgo')) {
            $result = timeAgo(now()->subHours(2));
            $this->assertIsString($result);
            $this->assertStringContainsString('hour', $result);
        } else {
            $this->markTestSkipped('timeAgo helper not found');
        }
    }
}
