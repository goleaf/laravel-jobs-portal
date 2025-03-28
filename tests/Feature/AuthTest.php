<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_register_as_candidate()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ];

        $response = $this->post('/register', $userData);
        
        $response->assertRedirect('/candidate/profile/edit');
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'user_type' => User::CANDIDATE,
        ]);
    }

    /** @test */
    public function users_can_register_as_employer()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => User::EMPLOYER,
            'agree_terms_policy' => '1',
        ];

        $response = $this->post('/register', $userData);
        
        $response->assertRedirect('/employer/company/edit');
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'user_type' => User::EMPLOYER,
        ]);
    }

    /** @test */
    public function users_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function users_cannot_login_with_incorrect_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function inactive_users_cannot_login()
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
                         ->post('/logout');
        
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'is_active' => true,
        ]);

        $response = $this->post('/password/email', [
            'email' => 'reset@example.com',
        ]);
        
        $response->assertSessionHas('status');
        // This would normally test the email was sent, but for now we're just verifying basic functionality
    }

    /** @test */
    public function user_can_view_password_reset_form()
    {
        $response = $this->get('/password/reset');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_update_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'password@example.com',
            'is_active' => true,
        ]);
        
        $token = app('auth.password.broker')->createToken($user);
        
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'password@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        
        $response->assertRedirect('/home');
    }
} 