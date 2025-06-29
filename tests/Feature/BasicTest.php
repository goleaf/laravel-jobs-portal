<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BasicTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function publicCanViewHomepage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function publicCanViewLoginPage()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function userCanLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
