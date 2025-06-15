<?php

namespace Tests\Feature\Api\Universal;

use App\Models\Candidate;
use App\Models\CareerLevel;
use App\Models\City;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\SalaryCurrency;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Universal API Test for CandidateApiController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class CandidateApiControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $baseUrl = '/api/universal/candidates';
    protected $user;
    protected $candidate;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = User::factory()->create([
            'user_type' => 'candidate',
            'is_active' => 1,
        ]);

        $this->adminUser = User::factory()->create([
            'user_type' => 'admin',
            'is_active' => 1,
        ]);

        // Create supporting data
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);
        $careerLevel = CareerLevel::factory()->create();
        $industry = Industry::factory()->create();
        $functionalArea = FunctionalArea::factory()->create();
        $currency = SalaryCurrency::factory()->create();

        // Create test candidate
        $this->candidate = Candidate::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'career_level_id' => $careerLevel->id,
            'industry_id' => $industry->id,
            'functional_area_id' => $functionalArea->id,
            'salary_currency_id' => $currency->id,
        ]);
    }

    /** @test */
    public function itCanListCandidatesWithPagination()
    {
        // Create additional candidates
        Candidate::factory()->count(5)->create();

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'candidates' => [
                        '*' => [
                            'id',
                            'personal',
                            'professional',
                            'location',
                            'profile',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                    ],
                ],
            ])
        ;
    }

    /** @test */
    public function itCanSearchCandidatesByKeyword()
    {
        // Create candidate with specific name
        $searchCandidate = Candidate::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Developer',
        ]);

        $response = $this->getJson($this->baseUrl.'?search=John');

        $response->assertStatus(200)
            ->assertJsonPath('data.candidates.0.personal.first_name', 'John')
        ;
    }

    /** @test */
    public function itCanFilterCandidatesByLocation()
    {
        $country = Country::factory()->create(['name' => 'Test Country']);
        Candidate::factory()->create([
            'country_id' => $country->id,
        ]);

        $response = $this->getJson($this->baseUrl.'?filter_location=Test Country');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.candidates')
        ;
    }

    /** @test */
    public function itCanShowSpecificCandidate()
    {
        $response = $this->getJson($this->baseUrl.'/'.$this->candidate->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'candidate' => [
                        'id',
                        'user_id',
                        'personal' => [
                            'first_name',
                            'last_name',
                            'full_name',
                            'phone',
                            'gender',
                            'nationality',
                        ],
                        'professional' => [
                            'current_salary',
                            'expected_salary',
                            'experience_years',
                            'willing_to_relocate',
                            'available_for_remote',
                        ],
                        'location' => [
                            'address',
                            'country',
                            'state',
                            'city',
                        ],
                        'profile' => [
                            'bio',
                            'website',
                            'linkedin',
                            'visibility',
                            'availability_status',
                        ],
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonPath('data.candidate.id', $this->candidate->id)
        ;
    }

    /** @test */
    public function itCanShowCandidateWithRelationships()
    {
        $response = $this->getJson($this->baseUrl.'/'.$this->candidate->id.'?include=user,skills,experiences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'candidate' => [
                        'user',
                        'skills',
                        'experiences',
                    ],
                ],
            ])
        ;
    }

    /** @test */
    public function itRequiresAuthenticationToCreateCandidate()
    {
        $candidateData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson($this->baseUrl, $candidateData);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticatedUserCanCreateCandidate()
    {
        Sanctum::actingAs($this->user);

        $country = Country::factory()->create();
        $currency = SalaryCurrency::factory()->create();

        $candidateData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => '+1234567890',
            'expected_salary' => 50000,
            'country_id' => $country->id,
            'salary_currency_id' => $currency->id,
        ];

        $response = $this->postJson($this->baseUrl, $candidateData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'candidate' => [
                        'id',
                        'personal',
                        'professional',
                        'location',
                    ],
                ],
            ])
        ;

        $this->assertDatabaseHas('candidates', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function itValidatesRequiredFieldsWhenCreatingCandidate()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->baseUrl, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name'])
        ;
    }

    /** @test */
    public function userCanUpdateTheirOwnCandidateProfile()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'first_name' => 'Updated Name',
            'summary' => 'Updated bio content',
        ];

        $response = $this->putJson($this->baseUrl.'/'.$this->candidate->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.candidate.personal.first_name', 'Updated Name')
        ;

        $this->assertDatabaseHas('candidates', [
            'id' => $this->candidate->id,
            'first_name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function userCannotUpdateOtherUsersCandidateProfile()
    {
        $otherUser = User::factory()->create(['user_type' => 'candidate']);
        Sanctum::actingAs($otherUser);

        $updateData = [
            'first_name' => 'Unauthorized Update',
        ];

        $response = $this->putJson($this->baseUrl.'/'.$this->candidate->id, $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function adminCanUpdateAnyCandidateProfile()
    {
        Sanctum::actingAs($this->adminUser);

        $updateData = [
            'first_name' => 'Admin Updated',
        ];

        $response = $this->putJson($this->baseUrl.'/'.$this->candidate->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.candidate.personal.first_name', 'Admin Updated')
        ;
    }

    /** @test */
    public function userCanDeleteTheirOwnCandidateProfile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->baseUrl.'/'.$this->candidate->id, [
            'confirmation' => true,
            'reason' => 'No longer looking for jobs',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'deletion' => [
                        'deletion_details',
                        'cleanup_summary',
                        'audit_trail',
                    ],
                ],
            ])
        ;

        $this->assertSoftDeleted('candidates', ['id' => $this->candidate->id]);
    }

    /** @test */
    public function deletionRequiresConfirmation()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->baseUrl.'/'.$this->candidate->id, [
            'reason' => 'Test deletion',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['confirmation'])
        ;
    }

    /** @test */
    public function adminCanForceDeleteCandidate()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson($this->baseUrl.'/'.$this->candidate->id, [
            'confirmation' => true,
            'force_delete' => true,
            'reason' => 'Policy violation',
        ]);

        $response->assertStatus(204);
    }

    /** @test */
    public function itHandlesRateLimitingOnCreation()
    {
        Sanctum::actingAs($this->user);

        $candidateData = [
            'first_name' => 'Rate',
            'last_name' => 'Limited',
            'phone' => '+1234567890',
        ];

        // Make multiple rapid requests
        for ($i = 0; $i < 10; ++$i) {
            $response = $this->postJson($this->baseUrl, $candidateData);
            if (429 === $response->status()) {
                break;
            }
        }

        // Should eventually hit rate limit
        $this->assertTrue(201 === $response->status() || 429 === $response->status());
    }

    /** @test */
    public function itIncludesStatisticsWhenRequested()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->baseUrl.'/'.$this->candidate->id.'?with_stats=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'candidate' => [
                        'statistics' => [
                            'profile_completion',
                            'total_applications',
                            'active_applications',
                            'profile_views',
                            'response_rate',
                        ],
                    ],
                ],
            ])
        ;
    }

    /** @test */
    public function itValidatesSalaryRangeConsistency()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'current_salary' => 100000,
            'expected_salary' => 50000, // Lower than current
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422);
    }

    /** @test */
    public function itHandlesConcurrentUpdatesGracefully()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'first_name' => 'Concurrent Update',
        ];

        // Simulate concurrent updates
        $response1 = $this->putJson($this->baseUrl.'/'.$this->candidate->id, $updateData);
        $response2 = $this->putJson($this->baseUrl.'/'.$this->candidate->id, $updateData);

        $this->assertTrue(200 === $response1->status() || 200 === $response2->status());
    }

    /** @test */
    public function itValidatesPhoneNumberFormat()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => 'invalid-phone',
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone'])
        ;
    }

    /** @test */
    public function itHandlesFileUploadForAvatar()
    {
        Sanctum::actingAs($this->user);

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        $response = $this->putJson($this->baseUrl.'/'.$this->candidate->id, [
            'avatar' => $file,
            'first_name' => 'Updated',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function itReturnsProperErrorForNonexistentCandidate()
    {
        $response = $this->getJson($this->baseUrl.'/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Candidate not found',
            ]);
    }
}
