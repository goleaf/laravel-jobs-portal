<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

    /** @test */
    public function registration_requires_name_email_password()
    {
        $response = $this->post('/register', [
            'password_confirmation' => 'password123',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ]);
        $response->assertSessionHasErrors(['name', 'email', 'password']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ]);
        $response->assertSessionHasErrors('email');

         $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ]);
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_requires_valid_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ]);
        $response->assertSessionHasErrors('email');
    }

     /** @test */
    public function registration_requires_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => $this->faker->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'user_type' => User::CANDIDATE,
            'agree_terms_policy' => '1',
        ]);
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_requires_agreeing_to_terms()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => User::CANDIDATE,
            // Missing 'agree_terms_policy'
        ]);
        $response->assertSessionHasErrors('agree_terms_policy');
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors('password');

        $response = $this->post('/login', [
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function authenticated_user_is_redirected_from_login_page()
    {
        $user = User::factory()->create(['is_active' => true]);
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/dashboard'); // Assuming '/dashboard' is the intended redirect
    }

    /** @test */
    public function authenticated_user_is_redirected_from_register_page()
    {
        $user = User::factory()->create(['is_active' => true]);
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/dashboard'); // Assuming '/dashboard' is the intended redirect
    }
}
