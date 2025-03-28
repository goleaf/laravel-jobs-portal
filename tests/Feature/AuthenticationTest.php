<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function login_page_can_be_rendered()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function users_can_authenticate_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        
        $response = $this->post('/users/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        
        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard'); // Adjust redirect path as needed
    }

    /** @test */
    public function users_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        
        $response = $this->post('/users/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        
        $this->assertGuest();
    }

    /** @test */
    public function users_cannot_authenticate_if_not_active()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'is_active' => false,
        ]);
        
        $response = $this->post('/users/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);
        
        $this->assertGuest();
    }

    /** @test */
    public function users_can_logout()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/logout');
        
        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /** @test */
    public function password_reset_page_can_be_rendered()
    {
        $response = $this->get('/password/reset');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function password_reset_links_can_be_requested()
    {
        Notification::fake();
        
        $user = User::factory()->create();
        
        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);
        
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function password_can_be_reset_with_valid_token()
    {
        Notification::fake();
        
        $user = User::factory()->create();
        
        $token = Password::createToken($user);
        
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);
        
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /** @test */
    public function employee_login_page_can_be_rendered()
    {
        $response = $this->get(route('front.employee.login'));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function candidate_login_page_can_be_rendered()
    {
        $response = $this->get(route('front.candidate.login'));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_login_page_can_be_rendered()
    {
        $response = $this->get(route('admin.login'));
        
        $response->assertStatus(200);
    }
} 