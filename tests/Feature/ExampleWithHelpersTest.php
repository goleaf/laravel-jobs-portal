<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class ExampleWithHelpersTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function can_create_user_with_unique_email()
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            'name' => 'Test User',
            'is_admin' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Test User',
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function can_get_api_auth_headers()
    {
        $user = TestHelpers::createUserWithUniqueEmail();

        $headers = TestHelpers::getApiAuthHeaders($user);

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertStringStartsWith('Bearer ', $headers['Authorization']);
    }

    /** @test */
    public function can_create_candidate_with_profile()
    {
        [$user, $candidate] = TestHelpers::createCandidateWithProfile([
            'name' => 'Candidate User',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Candidate User',
        ]);

        $this->assertDatabaseHas('candidates', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function can_create_employer_with_company()
    {
        [$user, $company] = TestHelpers::createEmployerWithCompany([
            'name' => 'Employer User',
        ], [
            'name' => 'Test Company',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Employer User',
        ]);

        $this->assertDatabaseHas('companies', [
            'user_id' => $user->id,
            'name' => 'Test Company',
        ]);
    }

    /** @test */
    public function can_create_jobs_for_company()
    {
        [$user, $company] = TestHelpers::createEmployerWithCompany();

        $jobs = TestHelpers::createJobsForCompany($company, 2);

        $this->assertCount(2, $jobs);

        foreach ($jobs as $job) {
            $this->assertDatabaseHas('jobs', [
                'id' => $job->id,
                'company_id' => $company->id,
            ]);
        }
    }
}
