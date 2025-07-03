<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
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
class CandidateAuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function candidate_can_view_registration_form()
    {
        $this->get(route('candidate.register'))
            ->assertStatus(200)
            ->assertViewIs('candidate.auth.register');
    }

    /** @test */
    public function candidate_can_register_with_valid_data()
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        $candidateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ];

        $this->post(route('candidate.register'), $candidateData)
            ->assertRedirect(route('candidate.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_type' => 'App\Models\Candidate',
        ]);

        $this->assertDatabaseHas('candidates', [
            'user_id' => User::where('email', 'john.doe@example.com')->first()->id,
        ]);
    }

    /** @test */
    public function candidate_cannot_register_with_invalid_email()
    {
        $candidateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
        ];

        $this->post(route('candidate.register'), $candidateData)
            ->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function candidate_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $candidateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
        ];

        $this->post(route('candidate.register'), $candidateData)
            ->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function candidate_cannot_register_with_mismatched_passwords()
    {
        $candidateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'phone' => '+1234567890',
        ];

        $this->post(route('candidate.register'), $candidateData)
            ->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function candidate_can_view_login_form()
    {
        $this->get(route('candidate.login'))
            ->assertStatus(200)
            ->assertViewIs('candidate.auth.login');
    }

    /** @test */
    public function candidate_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => Hash::make('password123'),
            'owner_type' => 'App\Models\Candidate',
        ]);

        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->update(['owner_id' => $candidate->id]);

        $this->post(route('candidate.login'), [
            'email' => 'candidate@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('candidate.dashboard'))
            ->assertSessionHas('success');

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function candidate_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('candidate.login'), [
            'email' => 'candidate@example.com',
            'password' => 'wrong_password',
        ])
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function authenticated_candidate_can_logout()
    {
        $user = User::factory()->create([
            'owner_type' => 'App\Models\Candidate',
        ]);

        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('candidate.logout'))
            ->assertRedirect(route('front.home'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }

    /** @test */
    public function authenticated_candidate_can_access_dashboard()
    {
        $user = User::factory()->create([
            'owner_type' => 'App\Models\Candidate',
        ]);

        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('candidate.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('candidate.dashboard.index');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_candidate_dashboard()
    {
        $this->get(route('candidate.dashboard'))
            ->assertRedirect(route('candidate.login'));
    }

    /** @test */
    public function registration_requires_all_required_fields()
    {
        $this->post(route('candidate.register'), [])
            ->assertSessionHasErrors([
                'first_name',
                'last_name',
                'email',
                'password',
                'phone',
                'country_id',
                'state_id',
                'city_id',
            ]);
    }
}
