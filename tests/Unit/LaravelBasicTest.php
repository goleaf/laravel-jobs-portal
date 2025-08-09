<?php

namespace Tests\Unit;

// Users/auth removed
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LaravelBasicTest extends TestCase
{
    /** @test */
    public function it_can_access_config()
    {
        $appName = Config::get('app.name');
        $this->assertNotNull($appName);
        $this->assertIsString($appName);
    }

    /** @test */
    public function it_has_testing_environment()
    {
        $this->assertEquals('testing', app()->environment());
    }

    /** @test */
    public function it_can_use_helper_functions()
    {
        $this->assertTrue(function_exists('config'));
        $this->assertTrue(function_exists('app'));
        $this->assertTrue(function_exists('env'));
    }

    /** @test */
    public function it_can_create_basic_objects()
    {
        $this->markTestSkipped('Users/auth removed.');
    }
}
