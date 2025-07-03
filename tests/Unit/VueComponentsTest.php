<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class VueComponentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vue_app_container_renders_correctly()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('app');
        $response->assertSee('<div id="app">', false);
    }

    /** @test */
    public function vue_app_has_proper_meta_tags()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('<meta name="csrf-token"', false);
    }

    /** @test */
    public function vue_app_loads_necessary_scripts()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Vue.js SPA should load the main app script
        $response->assertSee('app.js', false);
    }

    /** @test */
    public function api_routes_are_accessible()
    {
        // Test that API routes are properly configured for Vue.js SPA
        $response = $this->getJson('/api/v1/health');

        // Expect JSON response for SPA architecture
        $response->assertStatus(200);
    }

    /** @test */
    public function spa_routing_returns_app_view()
    {
        // All SPA routes should return the main app view
        $routes = ['/admin', '/admin/dashboard', '/jobs', '/companies'];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $response->assertViewIs('app');
        }
    }

    /** @test */
    public function vue_components_use_tailwind_classes()
    {
        // Test that TailwindCSS is properly integrated
        $response = $this->get('/');

        $response->assertStatus(200);
        // Should include TailwindCSS
        $response->assertSee('app.css', false);
    }
}
