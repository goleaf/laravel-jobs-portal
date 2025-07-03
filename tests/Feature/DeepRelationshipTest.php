<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Deep Relationship Integration Test
 *
 * Tests the integration of staudenmeir/eloquent-has-many-deep package
 * with our Laravel job portal's complex relationship scenarios.
 *
 * Package: https://github.com/staudenmeir/eloquent-has-many-deep
 * Reference: https://madewithlaravel.com/eloquent-has-many-deep
 * Version: v1.21
 */
class DeepRelationshipTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $candidate;
    protected User $employer;
    protected Company $company;
    protected Country $country;
    protected State $state;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        // Create location hierarchy
        $this->country = Country::factory()->create(['name' => 'Test Country']);
        $this->state = State::factory()->create([
            'name' => 'Test State',
            'country_id' => $this->country->id,
        ]);
        $this->city = City::factory()->create([
            'name' => 'Test City',
            'state_id' => $this->state->id,
            'country_id' => $this->country->id,
        ]);

        // Create users with location
        $this->candidate = User::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
        ]);
        $this->candidate->assignRole('candidate');

        $this->employer = User::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
        ]);
        $this->employer->assignRole('employer');

        // Create candidate profile
        Candidate::factory()->create(['user_id' => $this->candidate->id]);

        // Create company
        $this->company = Company::factory()->create([
            'user_id' => $this->employer->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
        ]);
    }

    /** @test */
    public function it_can_get_location_jobs_using_deep_relationships()
    {
        // Create jobs in the same location
        $job1 = Job::factory()->create([
            'company_id' => $this->company->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        $job2 = Job::factory()->create([
            'company_id' => $this->company->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'is_active' => true,
        ]);

        // Test the API endpoint
        $response = $this->actingAs($this->candidate, 'sanctum')
            ->getJson('/api/deep-relationships/location-jobs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user_location' => [
                        'country_id',
                        'state_id',
                        'city_id',
                    ],
                    'jobs_count',
                    'jobs' => [
                        '*' => [
                            'id',
                            'title',
                            'company',
                            'location',
                            'salary_range',
                            'posted_date',
                            'expires_at',
                        ],
                    ],
                ],
                'query_info' => [
                    'method',
                    'path',
                    'package',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'jobs_count' => 2,
                ],
                'query_info' => [
                    'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
                ],
            ]);
    }

    /** @test */
    public function it_can_get_company_applications_using_deep_relationships()
    {
        // Create jobs for the company
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
        ]);

        // Create job applications
        JobApplication::factory()->count(3)->create([
            'job_id' => $job->id,
            'candidate_id' => $this->candidate->id,
        ]);

        // Test the API endpoint
        $response = $this->actingAs($this->employer, 'sanctum')
            ->getJson('/api/deep-relationships/company-applications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'employer_id',
                    'applications_count',
                    'applications' => [
                        '*' => [
                            'id',
                            'candidate_name',
                            'candidate_email',
                            'job_title',
                            'applied_date',
                            'status',
                            'expected_salary',
                        ],
                    ],
                ],
                'query_info' => [
                    'method',
                    'path',
                    'package',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'applications_count' => 3,
                ],
            ]);
    }

    /** @test */
    public function it_can_get_region_candidates_using_deep_relationships()
    {
        // Create additional candidates in the same region
        $candidate2 = User::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
        ]);
        $candidate2->assignRole('candidate');
        Candidate::factory()->create(['user_id' => $candidate2->id]);

        $candidate3 = User::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
        ]);
        $candidate3->assignRole('candidate');
        Candidate::factory()->create(['user_id' => $candidate3->id]);

        // Test the API endpoint
        $response = $this->actingAs($this->candidate, 'sanctum')
            ->getJson('/api/deep-relationships/region-candidates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user_location',
                    'candidates_count',
                    'candidates' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'profile_views',
                            'skills_count',
                            'registered_date',
                        ],
                    ],
                ],
                'query_info',
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'candidates_count' => 2, // Excludes the requesting user
                ],
            ]);
    }

    /** @test */
    public function it_can_get_deep_analytics()
    {
        // Create some test data
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
            'city_id' => $this->city->id,
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $this->candidate->id,
        ]);

        // Test candidate analytics
        $response = $this->actingAs($this->candidate, 'sanctum')
            ->getJson('/api/deep-relationships/analytics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user_info' => [
                        'id',
                        'name',
                        'role',
                    ],
                    'deep_relationships',
                ],
                'package_info' => [
                    'name',
                    'version',
                    'github',
                    'reference',
                ],
            ])
            ->assertJson([
                'success' => true,
                'package_info' => [
                    'name' => 'staudenmeir/eloquent-has-many-deep',
                    'version' => 'v1.21',
                    'github' => 'https://github.com/staudenmeir/eloquent-has-many-deep',
                    'reference' => 'https://madewithlaravel.com/eloquent-has-many-deep',
                ],
            ]);

        // Test employer analytics
        $response = $this->actingAs($this->employer, 'sanctum')
            ->getJson('/api/deep-relationships/analytics');

        $response->assertStatus(200)
            ->assertJsonPath('data.user_info.role', 'employer');
    }

    /** @test */
    public function it_requires_authentication_for_deep_relationship_endpoints()
    {
        $endpoints = [
            '/api/deep-relationships/location-jobs',
            '/api/deep-relationships/company-applications',
            '/api/deep-relationships/region-candidates',
            '/api/deep-relationships/applied-skills',
            '/api/deep-relationships/similar-candidates',
            '/api/deep-relationships/analytics',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(401);
        }
    }

    /** @test */
    public function it_requires_proper_role_for_employer_specific_endpoints()
    {
        // Candidate trying to access employer endpoint
        $response = $this->actingAs($this->candidate, 'sanctum')
            ->getJson('/api/deep-relationships/company-applications');

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Access denied. Employer role required.',
            ]);
    }

    /** @test */
    public function it_requires_proper_role_for_candidate_specific_endpoints()
    {
        // Employer trying to access candidate endpoint
        $response = $this->actingAs($this->employer, 'sanctum')
            ->getJson('/api/deep-relationships/applied-skills');

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Access denied. Candidate role required.',
            ]);
    }

    /** @test */
    public function deep_relationship_queries_perform_efficiently()
    {
        // Create test data
        $jobs = Job::factory()->count(10)->create([
            'company_id' => $this->company->id,
            'city_id' => $this->city->id,
        ]);

        // Enable query logging
        \DB::enableQueryLog();

        // Make API call
        $response = $this->actingAs($this->candidate, 'sanctum')
            ->getJson('/api/deep-relationships/location-jobs');

        // Get executed queries
        $queries = \DB::getQueryLog();

        $response->assertStatus(200);

        // Verify efficient querying (should not have N+1 queries)
        $this->assertLessThan(
            10,
            count($queries),
            'Deep relationship queries should be efficient and avoid N+1 problems'
        );
    }
}
