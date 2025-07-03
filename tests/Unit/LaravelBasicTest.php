<?php

namespace Tests\Unit;

use App\Models\User;
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
        $user = new User([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('User', $user->last_name);
        $this->assertEquals('Test User', $user->full_name);
    }
}
