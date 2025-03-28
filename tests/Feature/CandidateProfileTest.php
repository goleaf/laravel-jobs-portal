<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function candidate_profile_page_can_be_viewed()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('candidate.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('candidate.profile.edit');
    }

    /** @test */
    public function candidate_profile_can_be_updated()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'current_salary' => '50000',
            'expected_salary' => '60000',
            'career_level_id' => 1,
            'functional_area_id' => 1,
            'industry_id' => 1,
            'experience' => '5',
            'skills' => [1, 2, 3],
            'language_id' => [1, 2],
        ];

        $response = $this->actingAs($user)
            ->put(route('candidate.profile.update'), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['first_name' => 'John', 'last_name' => 'Doe']);
    }

    /** @test */
    public function candidate_avatar_can_be_uploaded()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->post(route('candidate.profile-update'), [
                'avatar' => $file
            ]);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('candidates/profile/' . $file->hashName());
    }

    /** @test */
    public function candidate_resume_can_be_uploaded()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('resume.pdf', 100);

        $response = $this->actingAs($user)
            ->post(route('candidate.resume.upload'), [
                'resume' => $file
            ]);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('resumes/' . $file->hashName());
    }

    /** @test */
    public function candidate_education_can_be_added()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $educationData = [
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'degree_level_id' => 1,
            'institute' => 'Test University',
            'degree_title' => 'Bachelor of Science',
            'year' => '2018',
        ];

        $response = $this->actingAs($user)
            ->post(route('candidate.education.store'), $educationData);

        $response->assertRedirect();
        $this->assertDatabaseHas('candidate_educations', ['institute' => 'Test University']);
    }

    /** @test */
    public function candidate_experience_can_be_added()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $experienceData = [
            'title' => 'Software Developer',
            'company' => 'Test Company',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'start_date' => '2018-01-01',
            'end_date' => '2020-01-01',
            'description' => 'Test job description',
            'currently_working' => 0,
        ];

        $response = $this->actingAs($user)
            ->post(route('candidate.experience.store'), $experienceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('candidate_experiences', ['title' => 'Software Developer']);
    }

    /** @test */
    public function candidate_details_page_can_be_viewed_by_public()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
            'is_active' => true
        ]);

        $response = $this->get(route('front.candidate.details', $candidate->id));

        $response->assertStatus(200);
        $response->assertViewIs('front_web.candidate.candidate_details');
        $response->assertSee($user->full_name);
    }

    /** @test */
    public function inactive_candidate_cannot_be_viewed_by_public()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
            'is_active' => false
        ]);

        $response = $this->get(route('front.candidate.details', $candidate->id));

        $response->assertStatus(404);
    }
} 