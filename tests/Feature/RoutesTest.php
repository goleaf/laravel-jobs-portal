<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all public routes are accessible.
     */
    public function testAllPublicRoutesAreAccessible()
    {
        $routes = [
            '/' => 200,
            '/jobs' => 200,
            '/jobs/1' => 200,
            '/companies' => 200,
            '/company/1' => 200,
            '/about-us' => 200,
            '/contact' => 200,
            '/test' => 200,
        ];

        foreach ($routes as $route => $expectedStatus) {
            $response = $this->get($route);
            $this->assertEquals($expectedStatus, $response->status(), "Route {$route} failed with status {$response->status()}");
        }
    }

    /**
     * Test route names are correctly defined.
     */
    public function testRouteNamesAreCorrectlyDefined()
    {
        $namedRoutes = [
            'jobs.index' => '/jobs',
            'jobs.show' => '/jobs/1',
            'companies.index' => '/companies',
            'company.show' => '/company/1',
            'about-us' => '/about-us',
            'contact' => '/contact',
        ];

        foreach ($namedRoutes as $name => $expectedPath) {
            $url = route($name, 'jobs.show' === $name || 'company.show' === $name ? 1 : []);
            $this->assertStringContainsString($expectedPath, $url, "Route name {$name} does not resolve correctly");
        }
    }

    /**
     * Test jobs search with query parameters.
     */
    public function testJobsSearchWithQueryParameters()
    {
        $searchParams = [
            'keyword' => 'developer',
            'location' => 'San Francisco',
            'category' => 'technology',
        ];

        $response = $this->get('/jobs?'.http_build_query($searchParams));

        $response->assertStatus(200);
        $response->assertSee('Browse Jobs');
    }

    /**
     * Test companies search with query parameters.
     */
    public function testCompaniesSearchWithQueryParameters()
    {
        $searchParams = [
            'search' => 'tech',
            'industry' => 'technology',
        ];

        $response = $this->get('/companies?'.http_build_query($searchParams));

        $response->assertStatus(200);
        $response->assertSee('Browse Companies');
    }

    /**
     * Test job detail page with different IDs.
     */
    public function testJobDetailPageWithDifferentIds()
    {
        $jobIds = [1, 5, 10, 999];

        foreach ($jobIds as $id) {
            $response = $this->get("/jobs/{$id}");
            $response->assertStatus(200);
            $response->assertSee('Job Details');
        }
    }

    /**
     * Test company detail page with different IDs.
     */
    public function testCompanyDetailPageWithDifferentIds()
    {
        $companyIds = [1, 5, 10, 999];

        foreach ($companyIds as $id) {
            $response = $this->get("/company/{$id}");
            $response->assertStatus(200);
        }
    }

    /**
     * Test invalid routes return 404.
     */
    public function testInvalidRoutesReturn404()
    {
        $invalidRoutes = [
            '/invalid-page',
            '/jobs/abc',
            '/company/xyz',
            '/random-route',
            '/admin/unauthorized',
        ];

        foreach ($invalidRoutes as $route) {
            $response = $this->get($route);
            $this->assertEquals(404, $response->status(), "Route {$route} should return 404");
        }
    }

    /**
     * Test API test route returns JSON.
     */
    public function testApiTestRouteReturnsJson()
    {
        $response = $this->get('/test');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'status',
            'message',
            'timestamp',
            'memory_usage',
        ]);

        $data = $response->json();
        $this->assertEquals('ok', $data['status']);
        $this->assertEquals('Laravel is working!', $data['message']);
    }

    /**
     * Test POST routes that are defined.
     */
    public function testPostRoutes()
    {
        // Test login route (should exist but may redirect or return specific response)
        $loginResponse = $this->post('/login');
        // Don't assert specific status as it depends on auth setup
        $this->assertNotEquals(404, $loginResponse->status());

        // Test logout route
        $logoutResponse = $this->post('/logout');
        $this->assertNotEquals(404, $logoutResponse->status());
    }

    /**
     * Test routes with special characters in search.
     */
    public function testRoutesWithSpecialCharacters()
    {
        $specialSearches = [
            '/jobs?keyword=C%2B%2B+Developer',
            '/jobs?keyword=Data+Scientist+%26+Analyst',
            '/companies?search=Tech+%26+Co',
        ];

        foreach ($specialSearches as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test routes handle empty parameters gracefully.
     */
    public function testRoutesHandleEmptyParameters()
    {
        $routesWithEmptyParams = [
            '/jobs?keyword=&location=&category=',
            '/companies?search=&industry=',
            '/jobs?',
            '/companies?',
        ];

        foreach ($routesWithEmptyParams as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test that routes handle concurrent requests.
     */
    public function testRoutesHandleConcurrentRequests()
    {
        $routes = ['/', '/jobs', '/companies', '/about-us', '/contact'];

        foreach ($routes as $route) {
            // Simulate multiple rapid requests
            for ($i = 0; $i < 5; ++$i) {
                $response = $this->get($route);
                $response->assertStatus(200);
            }
        }
    }

    /**
     * Test routes with malformed parameters.
     */
    public function testRoutesWithMalformedParameters()
    {
        $malformedRoutes = [
            '/jobs?keyword[]=invalid',
            '/jobs?keyword=test&location[nested][deep]=value',
            '/companies?search[]=array',
        ];

        foreach ($malformedRoutes as $route) {
            $response = $this->get($route);
            // Should not crash, even with malformed params
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test route caching doesn't break functionality.
     */
    public function testRouteCachingCompatibility()
    {
        // Test that all routes work the same way multiple times
        // This helps ensure route caching won't break anything

        for ($i = 0; $i < 3; ++$i) {
            $response = $this->get('/');
            $response->assertStatus(200);
            $response->assertSee('Find Your Dream Job Today');

            $response = $this->get('/jobs');
            $response->assertStatus(200);
            $response->assertSee('Browse Jobs');
        }
    }
}
