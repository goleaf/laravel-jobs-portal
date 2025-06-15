<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RouteTest extends TestCase
{
    /** @test */
    public function itHasWelcomeRoute()
    {
        $routes = Route::getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }

        // Test that key routes exist
        $this->assertContains('login', $routeNames);
        $this->assertContains('register', $routeNames);
        $this->assertContains('jobs.index', $routeNames);
        $this->assertContains('companies.index', $routeNames);
    }

    /** @test */
    public function itHasAuthRoutes()
    {
        $routes = Route::getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }

        // Test auth-related routes
        $this->assertContains('login', $routeNames);
        $this->assertContains('password.reset', $routeNames);
        $this->assertContains('logout', $routeNames);
    }

    /** @test */
    public function itHasCandidateRoutes()
    {
        $routes = Route::getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }

        // Test candidate routes
        $this->assertContains('candidate.profile', $routeNames);
        $this->assertContains('candidate.profile.edit', $routeNames);
        $this->assertContains('candidate.applied-jobs', $routeNames);
        $this->assertContains('candidate.favorite-jobs', $routeNames);
        $this->assertContains('candidate.job-alerts', $routeNames);
    }

    /** @test */
    public function itHasEmployerRoutes()
    {
        $routes = Route::getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }

        // Test employer routes
        $this->assertContains('employer.company', $routeNames);
        $this->assertContains('employer.company.edit', $routeNames);
        $this->assertContains('employer.jobs.index', $routeNames);
        $this->assertContains('employer.jobs.create', $routeNames);
        $this->assertContains('employer.jobs.applications', $routeNames);
    }
}
