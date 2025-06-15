<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function importantRoutesExist()
    {
        $importantRoutes = [
            '/',
            '/login',
            '/register',
        ];

        foreach ($importantRoutes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->isSuccessful() || $response->isRedirect(),
                "Route {$route} is not accessible"
            );
        }
    }

    /** @test */
    public function authenticatedRoutesRequireLogin()
    {
        $protectedRoutes = [
            '/dashboard',
            '/profile',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }
}
