<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider publicRoutes
     */
    public function public_routes_are_accessible($route)
    {
        $response = $this->get($route);
        
        $response->assertStatus(200);
    }

    /**
     * @test
     * @dataProvider protectedRoutes
     */
    public function protected_routes_redirect_guests_to_login($route)
    {
        $response = $this->get($route);
        
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider protectedRoutes
     */
    public function protected_routes_are_accessible_to_authenticated_users($route)
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get($route);
        
        // The route should either return 200 or redirect to another route,
        // but not redirect to login
        $this->assertTrue(
            $response->status() === 200 || 
            $response->isRedirect() && !$response->isRedirect(route('login'))
        );
    }

    /**
     * @test
     * @dataProvider adminRoutes
     */
    public function admin_routes_are_not_accessible_to_regular_users($route)
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        
        $response = $this->actingAs($user)->get($route);
        
        // Should redirect or return 403
        $this->assertTrue(
            $response->status() === 403 ||
            $response->isRedirect()
        );
    }

    /**
     * @test
     * @dataProvider adminRoutes
     */
    public function admin_routes_are_accessible_to_admin_users($route)
    {
        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        
        // Simulate admin role - this might need to be adjusted based on how role is stored
        $admin->is_admin = true;
        $admin->save();
        
        $response = $this->actingAs($admin)->get($route);
        
        // The route should not redirect to login
        $this->assertTrue(
            $response->status() === 200 || 
            $response->isRedirect() && !$response->isRedirect(route('login'))
        );
    }

    public function publicRoutes()
    {
        return [
            ['/'],
            [route('contact')],
            [route('forms.validation')],
            [route('forms.alpine')],
            [route('forms.binding')],
            [route('forms.errors')],
            [route('forms.methods')],
            // Add more public routes as needed
        ];
    }

    public function protectedRoutes()
    {
        return [
            ['/user/profile'],
            // Add more protected routes as needed
        ];
    }

    public function adminRoutes()
    {
        return [
            [route('admin.dashboard')],
            [route('subscribers.index')],
            [route('job-categories.index')],
            // Add more admin routes as needed
        ];
    }
} 