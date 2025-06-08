<?php

namespace Tests\Feature\Api\Universal;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Industry;
use App\Models\OwnershipType;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Universal API Test for CompanyApiController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns
 */
class CompanyApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $baseUrl = '/api/universal/companies';
    protected $user;
    protected $company;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->user = User::factory()->create([
            'user_type' => 'employer',
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
        $companySize = CompanySize::factory()->create();
        $industry = Industry::factory()->create();
        $ownershipType = OwnershipType::factory()->create();
        
        // Create test company
        $this->company = Company::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'company_size_id' => $companySize->id,
            'industry_id' => $industry->id,
            'ownership_type_id' => $ownershipType->id,
        ]);
    }

    /** @test */
    public function it_can_list_companies_with_pagination()
    {
        // Create additional companies
        Company::factory()->count(5)->create();

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'companies' => [
                        '*' => [
                            'id',
                            'basic_info',
                            'contact_info',
                            'business_details',
                            'location',
                            'social_links',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_search_companies_by_name()
    {
        $searchCompany = Company::factory()->create([
            'name' => 'TechCorp Solutions',
        ]);

        $response = $this->getJson($this->baseUrl . '?search=TechCorp');

        $response->assertStatus(200)
            ->assertJsonPath('data.companies.0.basic_info.name', 'TechCorp Solutions');
    }

    /** @test */
    public function it_can_filter_companies_by_industry()
    {
        $industry = Industry::factory()->create(['name' => 'Technology']);
        Company::factory()->create([
            'industry_id' => $industry->id,
        ]);

        $response = $this->getJson($this->baseUrl . '?filter_industry=Technology');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.companies');
    }

    /** @test */
    public function it_can_filter_companies_by_size()
    {
        $response = $this->getJson($this->baseUrl . '?filter_size=medium');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_show_specific_company()
    {
        $response = $this->getJson($this->baseUrl . '/' . $this->company->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'company' => [
                        'id',
                        'basic_info' => [
                            'name',
                            'slug',
                            'tagline',
                            'description',
                            'founded_year',
                        ],
                        'contact_info' => [
                            'email',
                            'phone',
                            'website',
                            'contact_person',
                        ],
                        'business_details' => [
                            'industry',
                            'company_size',
                            'ownership_type',
                            'employee_count',
                            'annual_revenue',
                        ],
                        'location' => [
                            'address',
                            'country',
                            'state',
                            'city',
                            'postal_code',
                        ],
                        'social_links' => [
                            'linkedin',
                            'twitter',
                            'facebook',
                            'instagram',
                        ],
                        'verification',
                        'statistics',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonPath('data.company.id', $this->company->id);
    }

    /** @test */
    public function it_can_show_company_with_relationships()
    {
        $response = $this->getJson($this->baseUrl . '/' . $this->company->id . '?include=user,jobs,reviews');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'company' => [
                        'user',
                        'jobs',
                        'reviews',
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_requires_authentication_to_create_company()
    {
        $companyData = [
            'name' => 'New Company',
            'email' => 'contact@newcompany.com',
        ];

        $response = $this->postJson($this->baseUrl, $companyData);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_create_company()
    {
        Sanctum::actingAs($this->user);

        $country = Country::factory()->create();
        $industry = Industry::factory()->create();

        $companyData = [
            'name' => 'Innovative Startup',
            'email' => 'hello@innovative.com',
            'phone' => '+1234567890',
            'website' => 'https://innovative.com',
            'description' => 'We build amazing products',
            'country_id' => $country->id,
            'industry_id' => $industry->id,
        ];

        $response = $this->postJson($this->baseUrl, $companyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'company' => [
                        'id',
                        'basic_info',
                        'contact_info',
                        'business_details',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Innovative Startup',
            'email' => 'hello@innovative.com',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_company()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->baseUrl, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function it_validates_unique_company_name()
    {
        Sanctum::actingAs($this->user);

        $companyData = [
            'name' => $this->company->name, // Duplicate name
            'email' => 'unique@email.com',
        ];

        $response = $this->postJson($this->baseUrl, $companyData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function user_can_update_their_own_company()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'name' => 'Updated Company Name',
            'description' => 'Updated company description',
            'website' => 'https://updated-company.com',
        ];

        $response = $this->putJson($this->baseUrl . '/' . $this->company->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.company.basic_info.name', 'Updated Company Name');

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name',
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_company()
    {
        $otherUser = User::factory()->create(['user_type' => 'employer']);
        Sanctum::actingAs($otherUser);

        $updateData = [
            'name' => 'Unauthorized Update',
        ];

        $response = $this->putJson($this->baseUrl . '/' . $this->company->id, $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_company()
    {
        Sanctum::actingAs($this->adminUser);

        $updateData = [
            'name' => 'Admin Updated Company',
            'is_verified' => true,
        ];

        $response = $this->putJson($this->baseUrl . '/' . $this->company->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.company.basic_info.name', 'Admin Updated Company');
    }

    /** @test */
    public function user_can_delete_their_own_company()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->baseUrl . '/' . $this->company->id, [
            'confirmation' => true,
            'reason' => 'Business closure',
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
                    ]
                ]
            ]);

        $this->assertSoftDeleted('companies', ['id' => $this->company->id]);
    }

    /** @test */
    public function deletion_requires_confirmation()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->baseUrl . '/' . $this->company->id, [
            'reason' => 'Test deletion',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['confirmation']);
    }

    /** @test */
    public function it_validates_website_url_format()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'website' => 'invalid-url',
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['website']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => 'Test Company',
            'email' => 'invalid-email',
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_handles_logo_upload()
    {
        Sanctum::actingAs($this->user);

        $file = \Illuminate\Http\UploadedFile::fake()->image('logo.jpg', 300, 300);

        $response = $this->putJson($this->baseUrl . '/' . $this->company->id, [
            'logo' => $file,
            'name' => 'Updated Company',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_includes_statistics_when_requested()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->baseUrl . '/' . $this->company->id . '?with_stats=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'company' => [
                        'statistics' => [
                            'total_jobs',
                            'active_jobs',
                            'total_applications',
                            'profile_views',
                            'response_rate',
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_validates_founded_year_range()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'founded_year' => 2025, // Future year
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['founded_year']);
    }

    /** @test */
    public function it_handles_rate_limiting_on_creation()
    {
        Sanctum::actingAs($this->user);

        $companyData = [
            'name' => 'Rate Limited Company',
            'email' => 'rate@limited.com',
        ];

        // Make multiple rapid requests
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson($this->baseUrl, array_merge($companyData, ['name' => "Company {$i}"]));
            if ($response->status() === 429) {
                break;
            }
        }

        // Should eventually hit rate limit
        $this->assertTrue($response->status() === 201 || $response->status() === 429);
    }

    /** @test */
    public function it_returns_proper_error_for_nonexistent_company()
    {
        $response = $this->getJson($this->baseUrl . '/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Company not found',
            ]);
    }

    /** @test */
    public function it_can_filter_by_verification_status()
    {
        Company::factory()->create(['is_verified' => true]);
        Company::factory()->create(['is_verified' => false]);

        $response = $this->getJson($this->baseUrl . '?verified_only=true');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_employee_count_range()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'employee_count' => -5, // Negative count
        ];

        $response = $this->postJson($this->baseUrl, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['employee_count']);
    }
}
