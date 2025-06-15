<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class AuthenticationOptimizedTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginPageLoads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => \Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
    }

    public function testUserCannotLoginWithInvalidCredentials(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => \Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    public function testRegistrationCreatesNewUser(): void
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }
}
