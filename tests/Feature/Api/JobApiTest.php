<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_jobs_list(): void
    {
        [$user, $jobs] = TestHelpers::createTestEnvironment(3);

        $response = $this->getJson("/api/jobs");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    "data" => [
                        "*" => ["id", "title", "description"]
                    ]
                ]);
    }

    public function test_can_create_job_with_authentication(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail();
        $headers = TestHelpers::getApiAuthHeaders($user);

        $jobData = [
            "title" => "API Test Job",
            "description" => "Job created via API test",
            "company_id" => 1,
            "job_category_id" => 1,
            "job_type_id" => 1
        ];

        $response = $this->postJson("/api/jobs", $jobData, $headers);

        $response->assertStatus(201)
                ->assertJson([
                    "data" => [
                        "title" => "API Test Job"
                    ]
                ]);
    }

    public function test_cannot_create_job_without_authentication(): void
    {
        $jobData = [
            "title" => "Unauthorized Job",
            "description" => "This should fail"
        ];

        $response = $this->postJson("/api/jobs", $jobData);

        $response->assertStatus(401);
    }
}