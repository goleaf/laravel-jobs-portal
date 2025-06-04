<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Models\User;

class ComprehensiveRouteTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    protected $candidate;
    protected $company;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->candidate = User::factory()->create(['role' => 'candidate']);
        $this->company = User::factory()->create(['role' => 'company']);
    }
    
    /** @test */
    public function all_public_routes_are_accessible()
    {
        $publicRoutes = [
            '/',
            '/about',
            '/contact',
            '/login',
            '/register',
        ];
        
        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $this->assertNotEquals(404, $response->status(), "Route $route returned 404");
            $this->assertNotEquals(500, $response->status(), "Route $route returned 500");
        }
    }
    
    /** @test */
    public function admin_routes_require_authentication()
    {
        $adminRoutes = [
            '/admin',
            '/admin/users',
            '/admin/dashboard',
        ];
        
        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $this->assertIn($response->status(), [302, 401, 403], 
                "Admin route $route should require authentication");
        }
    }
    
    /** @test */
    public function admin_routes_work_with_admin_user()
    {
        $adminRoutes = [
            '/admin' => 200,
            '/admin/dashboard' => 200,
        ];
        
        foreach ($adminRoutes as $route => $expectedStatus) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertEquals($expectedStatus, $response->status(), 
                "Admin route $route failed with admin user");
        }
    }
    
    /** @test */
    public function api_routes_return_json()
    {
        $apiRoutes = [
            '/api/jobs',
            '/api/companies',
            '/api/candidates',
        ];
        
        foreach ($apiRoutes as $route) {
            $response = $this->get($route);
            if ($response->status() !== 404) {
                $this->assertJson($response->content(), 
                    "API route $route should return JSON");
            }
        }
    }
    
    /** @test */
    public function protected_routes_redirect_unauthenticated_users()
    {
        $protectedRoutes = [
            '/dashboard',
            '/profile',
            '/jobs/create',
        ];
        
        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $this->assertIn($response->status(), [302, 401], 
                "Protected route $route should redirect unauthenticated users");
        }
    }
    
    /** @test */
    public function all_named_routes_exist()
    {
        $namedRoutes = [
            'home',
            'login',
            'register',
            'admin.dashboard',
            'jobs.index',
            'companies.index',
        ];
        
        foreach ($namedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), 
                "Named route '$routeName' does not exist");
        }
    }
}