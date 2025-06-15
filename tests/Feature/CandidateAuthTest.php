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
    public function candidateCanViewRegistrationForm()
    {
        $this->get(route('candidate.register'))
            ->assertStatus(200)
            ->assertViewIs('candidate.auth.register')
        ;
    }

    /** @test */
    public function candidateCanRegisterWithValidData()
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
            ->assertSessionHas('success')
        ;

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
    public function candidateCannotRegisterWithInvalidEmail()
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
            ->assertSessionHasErrors(['email'])
        ;
    }

    /** @test */
    public function candidateCannotRegisterWithExistingEmail()
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
            ->assertSessionHasErrors(['email'])
        ;
    }

    /** @test */
    public function candidateCannotRegisterWithMismatchedPasswords()
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
            ->assertSessionHasErrors(['password'])
        ;
    }

    /** @test */
    public function candidateCanViewLoginForm()
    {
        $this->get(route('candidate.login'))
            ->assertStatus(200)
            ->assertViewIs('candidate.auth.login')
        ;
    }

    /** @test */
    public function candidateCanLoginWithValidCredentials()
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
            ->assertSessionHas('success')
        ;

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function candidateCannotLoginWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('candidate.login'), [
            'email' => 'candidate@example.com',
            'password' => 'wrong_password',
        ])
            ->assertSessionHasErrors(['email'])
        ;

        $this->assertGuest();
    }

    /** @test */
    public function authenticatedCandidateCanLogout()
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
            ->assertSessionHas('success')
        ;

        $this->assertGuest();
    }

    /** @test */
    public function authenticatedCandidateCanAccessDashboard()
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
            ->assertViewIs('candidate.dashboard.index')
        ;
    }

    /** @test */
    public function unauthenticatedUserCannotAccessCandidateDashboard()
    {
        $this->get(route('candidate.dashboard'))
            ->assertRedirect(route('candidate.login'))
        ;
    }

    /** @test */
    public function registrationRequiresAllRequiredFields()
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
