<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function unauthenticated_users_cannot_access_protected_api_routes()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_fetch_their_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /** @test */
    public function api_can_list_jobs()
    {
        Job::factory()->count(3)->create();

        $response = $this->getJson('/api/jobs');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function api_can_show_job_details()
    {
        $job = Job::factory()->create([
            'job_title' => 'Software Engineer',
        ]);

        $response = $this->getJson("/api/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $job->id,
                    'job_title' => 'Software Engineer',
                ],
            ]);
    }

    /** @test */
    public function api_can_list_companies()
    {
        Company::factory()->count(3)->create(['is_active' => true]);

        $response = $this->getJson('/api/companies');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function api_can_show_company_details()
    {
        $company = Company::factory()->create([
            'name' => 'Acme Inc',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $company->id,
                    'name' => 'Acme Inc',
                ],
            ]);
    }

    /** @test */
    public function api_can_filter_jobs_by_category()
    {
        $categoryId = 1;
        Job::factory()->count(2)->create(['job_category_id' => $categoryId]);
        Job::factory()->count(3)->create(['job_category_id' => 2]);

        $response = $this->getJson("/api/jobs?category_id={$categoryId}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function api_can_search_jobs_by_title()
    {
        Job::factory()->create(['job_title' => 'Senior PHP Developer']);
        Job::factory()->create(['job_title' => 'Junior PHP Developer']);
        Job::factory()->create(['job_title' => 'Java Developer']);

        $response = $this->getJson('/api/jobs?search=PHP');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function api_can_list_candidates()
    {
        $user = User::factory()->create(['is_active' => true]);
        Candidate::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/candidates');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function api_can_filter_candidates_by_experience()
    {
        $user = User::factory()->create(['is_active' => true]);
        Candidate::factory()->count(2)->create([
            'experience' => 5,
            'user_id' => $user->id,
        ]);
        Candidate::factory()->count(3)->create([
            'experience' => 2,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/candidates?min_experience=5');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function authenticated_users_can_apply_for_jobs()
    {
        $user = User::factory()->create(['is_active' => true]);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        $job = Job::factory()->create(['status' => Job::STATUS_OPEN]);

        $response = $this->actingAs($user, 'api')
            ->postJson("/api/jobs/{$job->id}/apply", [
                'cover_letter' => $this->faker->paragraph,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Application submitted successfully',
            ]);
    }
}
