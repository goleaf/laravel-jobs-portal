<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EmployerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $employerUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create employer user for testing
        $this->employerUser = User::factory()->create([
            'user_type' => User::EMPLOYER,
            'password' => Hash::make('password123'),
        ]);

        // Create company for employer
        $this->company = Company::factory()->create([
            'user_id' => $this->employerUser->id,
        ]);
    }

    /** @test */
    public function employer_can_view_their_profile()
    {
        $response = $this->actingAs($this->employerUser)
            ->getJson('/employer/profile');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'employer' => [
                    'id', 'name', 'email',
                ],
                'company' => [
                    'id', 'name', 'website',
                ],
            ],
        ]);
    }

    /** @test */
    public function employer_can_update_their_profile()
    {
        $profileData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'facebook_url' => $this->faker->url,
            'twitter_url' => $this->faker->url,
            'linkedin_url' => $this->faker->url,
            'google_plus_url' => $this->faker->url,
        ];

        $response = $this->actingAs($this->employerUser)
            ->postJson('/employer/profile-update', $profileData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', [
            'id' => $this->employerUser->id,
            'email' => $profileData['email'],
        ]);
    }

    /** @test */
    public function employer_can_change_password()
    {
        $passwordData = [
            'password_current' => 'password123',
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ];

        $response = $this->actingAs($this->employerUser)
            ->postJson('/employer/change-password', $passwordData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Validate that password was changed
        $this->employerUser->refresh();
        $this->assertTrue(Hash::check('newPassword456', $this->employerUser->password));
    }

    /** @test */
    public function employer_cannot_change_password_with_incorrect_current_password()
    {
        $passwordData = [
            'password_current' => 'wrongPassword',
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ];

        $response = $this->actingAs($this->employerUser)
            ->postJson('/employer/change-password', $passwordData);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        // Validate that password was not changed
        $this->employerUser->refresh();
        $this->assertFalse(Hash::check('newPassword456', $this->employerUser->password));
        $this->assertTrue(Hash::check('password123', $this->employerUser->password));
    }

    /** @test */
    public function employer_cannot_change_password_with_non_matching_confirmation()
    {
        $passwordData = [
            'password_current' => 'password123',
            'password' => 'newPassword456',
            'password_confirmation' => 'differentPassword',
        ];

        $response = $this->actingAs($this->employerUser)
            ->postJson('/employer/change-password', $passwordData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');

        // Validate that password was not changed
        $this->employerUser->refresh();
        $this->assertFalse(Hash::check('newPassword456', $this->employerUser->password));
        $this->assertTrue(Hash::check('password123', $this->employerUser->password));
    }

    /** @test */
    public function non_employer_cannot_access_employer_profile_endpoints()
    {
        $candidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);

        $response = $this->actingAs($candidateUser)
            ->getJson('/employer/profile');

        // Assuming proper middleware restricting access
        $response->assertStatus(403); // Or 401/404 depending on implementation
    }
}
