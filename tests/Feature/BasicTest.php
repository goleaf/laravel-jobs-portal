<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BasicTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function public_can_view_homepage()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function public_can_view_login_page()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
} 