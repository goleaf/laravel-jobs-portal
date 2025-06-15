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
    public function itCanAccessConfig()
    {
        $appName = Config::get('app.name');
        $this->assertNotNull($appName);
        $this->assertIsString($appName);
    }

    /** @test */
    public function itHasTestingEnvironment()
    {
        $this->assertEquals('testing', app()->environment());
    }

    /** @test */
    public function itCanUseHelperFunctions()
    {
        $this->assertTrue(function_exists('config'));
        $this->assertTrue(function_exists('app'));
        $this->assertTrue(function_exists('env'));
    }

    /** @test */
    public function itCanCreateBasicObjects()
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
