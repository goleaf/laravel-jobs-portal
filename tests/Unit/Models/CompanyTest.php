<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Job;
use App\Models\OwnershipType;
use App\Models\State;
// Users/auth removed
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanyTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    // Users/auth removed

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data (no user association)
        $this->company = Company::factory()->create(['user_id' => null]);
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $expectedFillable = [
            'user_id', 'name', 'slug', 'email', 'phone', 'website',
            'description', 'short_description', 'founded_year', 'employee_count',
            'industry_id', 'company_size_id', 'ownership_type_id',
            'country_id', 'state_id', 'city_id', 'address', 'postal_code',
            'latitude', 'longitude', 'is_active', 'is_featured', 'is_verified',
            'is_private', 'logo', 'cover_image', 'social_facebook',
            'social_twitter', 'social_linkedin', 'social_instagram',
            'social_youtube', 'social_github', 'culture_description',
            'benefits', 'technologies', 'certifications', 'awards',
            'office_locations', 'working_hours', 'dress_code',
            'company_type', 'revenue', 'market_cap', 'stock_symbol',
            'headquarters', 'ceo_name', 'mission_statement', 'vision_statement',
            'values', 'company_culture', 'diversity_policy',
            'ceo', 'no_of_offices', 'no_of_employees', 'established_in', 'details', 'fax',
            'facebook_url', 'twitter_url', 'linkedin_url', 'google_plus_url',
            'pinterest_url', 'unique_id', 'location', 'location2', 'founded_at', 'status',
        ];

        $this->assertEquals($expectedFillable, $this->company->getFillable());
    }

    /** @test */
    public function it_has_correct_casts(): void
    {
        $expectedCasts = [
            'id' => 'int',
            'user_id' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'industry_id' => 'integer',
            'ownership_type_id' => 'integer',
            'company_size_id' => 'integer',
            'established_in' => 'integer',
            'no_of_employees' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals(
                $cast,
                $this->company->getCasts()[$attribute] ?? null,
                "Cast for {$attribute} should be {$cast}"
            );
        }
    }

    /** @test */
    public function it_belongs_to_user(): void
    {
        $this->markTestSkipped('Users/auth removed.');
    }

    /** @test */
    public function it_belongs_to_location_models(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        $company = Company::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertInstanceOf(Country::class, $company->country);
        $this->assertInstanceOf(State::class, $company->state);
        $this->assertInstanceOf(City::class, $company->city);

        $this->assertEquals($country->id, $company->country->id);
        $this->assertEquals($state->id, $company->state->id);
        $this->assertEquals($city->id, $company->city->id);
    }

    /** @test */
    public function it_belongs_to_industry(): void
    {
        $industry = Industry::factory()->create();
        $company = Company::factory()->create(['industry_id' => $industry->id]);

        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($industry->id, $company->industry->id);
    }

    /** @test */
    public function it_belongs_to_ownership_type(): void
    {
        $ownershipType = OwnershipType::factory()->create();
        $company = Company::factory()->create(['ownership_type_id' => $ownershipType->id]);

        $this->assertInstanceOf(OwnershipType::class, $company->ownershipType);
        $this->assertEquals($ownershipType->id, $company->ownershipType->id);
    }

    /** @test */
    public function it_belongs_to_company_size(): void
    {
        $companySize = CompanySize::factory()->create();
        $company = Company::factory()->create(['company_size_id' => $companySize->id]);

        $this->assertInstanceOf(CompanySize::class, $company->companySize);
        $this->assertEquals($companySize->id, $company->companySize->id);
    }

    /** @test */
    public function it_has_many_jobs(): void
    {
        $jobs = Job::factory()->count(3)->create(['company_id' => $this->company->id]);

        $this->assertInstanceOf(Collection::class, $this->company->jobs);
        $this->assertCount(3, $this->company->jobs);

        foreach ($jobs as $job) {
            $this->assertTrue($this->company->jobs->contains($job));
        }
    }

    /** @test */
    public function active_scope_returns_only_active_companies(): void
    {
        Company::factory()->create(['is_active' => true]);
        Company::factory()->create(['is_active' => false]);

        $activeCompanies = Company::active()->get();

        $activeCompanies->each(function ($company) {
            $this->assertTrue($company->is_active);
        });
    }

    /** @test */
    public function inactive_scope_returns_only_inactive_companies(): void
    {
        Company::factory()->create(['is_active' => true]);
        Company::factory()->create(['is_active' => false]);

        $inactiveCompanies = Company::inactive()->get();

        $inactiveCompanies->each(function ($company) {
            $this->assertFalse($company->is_active);
        });
    }

    /** @test */
    public function featured_scope_returns_only_featured_companies(): void
    {
        Company::factory()->create(['is_featured' => true]);
        Company::factory()->create(['is_featured' => false]);

        $featuredCompanies = Company::featured()->get();

        $featuredCompanies->each(function ($company) {
            $this->assertTrue($company->is_featured);
        });
    }

    /** @test */
    public function verified_scope_returns_only_verified_companies(): void
    {
        Company::factory()->create(['is_verified' => true]);
        Company::factory()->create(['is_verified' => false]);

        $verifiedCompanies = Company::verified()->get();

        $verifiedCompanies->each(function ($company) {
            $this->assertTrue($company->is_verified);
        });
    }

    /** @test */
    public function by_industry_scope_filters_by_industry(): void
    {
        $industry = Industry::factory()->create();
        Company::factory()->create(['industry_id' => $industry->id]);

        $industryCompanies = Company::byIndustry($industry->id)->get();

        $industryCompanies->each(function ($company) use ($industry) {
            $this->assertEquals($industry->id, $company->industry_id);
        });
    }

    /** @test */
    public function by_location_scope_filters_by_location(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        Company::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $locationCompanies = Company::byLocation($country->id, $state->id, $city->id)->get();

        $locationCompanies->each(function ($company) use ($country, $state, $city) {
            $this->assertEquals($country->id, $company->country_id);
            $this->assertEquals($state->id, $company->state_id);
            $this->assertEquals($city->id, $company->city_id);
        });
    }

    /** @test */
    public function by_size_scope_filters_by_company_size(): void
    {
        $companySize = CompanySize::factory()->create();
        Company::factory()->create(['company_size_id' => $companySize->id]);

        $sizeCompanies = Company::bySize($companySize->id)->get();

        $sizeCompanies->each(function ($company) use ($companySize) {
            $this->assertEquals($companySize->id, $company->company_size_id);
        });
    }

    /** @test */
    public function search_scope_searches_in_name_and_details(): void
    {
        Company::factory()->create(['name' => 'Tech Corporation', 'details' => 'Software development']);
        Company::factory()->create(['name' => 'Marketing Agency', 'details' => 'Digital marketing']);

        $searchCompanies = Company::search('Tech')->get();

        $this->assertCount(1, $searchCompanies);
        $this->assertStringContainsString('Tech', $searchCompanies->first()->name);
    }

    /** @test */
    public function recent_scope_returns_companies_from_last30_days(): void
    {
        Company::factory()->create(['created_at' => now()->subDays(10)]);
        Company::factory()->create(['created_at' => now()->subDays(40)]);

        $recentCompanies = Company::recent()->get();

        $recentCompanies->each(function ($company) {
            $this->assertTrue($company->created_at->gte(now()->subDays(30)));
        });
    }

    /** @test */
    public function established_between_scope_filters_by_establishment_year(): void
    {
        Company::factory()->create(['established_in' => 2010]);
        Company::factory()->create(['established_in' => 2020]);

        $establishedCompanies = Company::establishedBetween(2015, 2025)->get();

        $establishedCompanies->each(function ($company) {
            $this->assertTrue($company->established_in >= 2015 && $company->established_in <= 2025);
        });
    }

    /** @test */
    public function with_jobs_scope_returns_companies_with_jobs(): void
    {
        $companyWithJobs = Company::factory()->create();
        Job::factory()->create(['company_id' => $companyWithJobs->id]);

        $companyWithoutJobs = Company::factory()->create();

        $companiesWithJobs = Company::withJobs()->get();

        $this->assertTrue($companiesWithJobs->contains($companyWithJobs));
        $this->assertFalse($companiesWithJobs->contains($companyWithoutJobs));
    }

    /** @test */
    public function it_can_get_full_location_string(): void
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $state = State::factory()->create(['name' => 'California', 'country_id' => $country->id]);
        $city = City::factory()->create(['name' => 'San Francisco', 'state_id' => $state->id]);

        $company = Company::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'location' => '123 Main St',
        ]);

        $fullLocation = $company->getFullLocation();

        $this->assertStringContainsString('San Francisco', $fullLocation);
        $this->assertStringContainsString('California', $fullLocation);
        $this->assertStringContainsString('United States', $fullLocation);
    }

    /** @test */
    public function it_can_get_jobs_count(): void
    {
        Job::factory()->count(5)->create(['company_id' => $this->company->id]);

        $this->assertEquals(5, $this->company->getJobsCount());
    }

    /** @test */
    public function it_can_get_active_jobs_count(): void
    {
        Job::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => Job::STATUS_OPEN,
        ]);
        Job::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => Job::STATUS_CLOSED,
        ]);

        $this->assertEquals(3, $this->company->getActiveJobsCount());
    }

    /** @test */
    public function it_can_check_if_company_has_social_links(): void
    {
        $companyWithSocial = Company::factory()->create([
            'facebook_url' => 'https://facebook.com/company',
            'twitter_url' => 'https://twitter.com/company',
        ]);

        $companyWithoutSocial = Company::factory()->create([
            'facebook_url' => null,
            'twitter_url' => null,
            'linkedin_url' => null,
        ]);

        $this->assertTrue($companyWithSocial->hasSocialLinks());
        $this->assertFalse($companyWithoutSocial->hasSocialLinks());
    }

    /** @test */
    public function it_can_get_company_age(): void
    {
        $company = Company::factory()->create(['established_in' => 2020]);

        $expectedAge = now()->year - 2020;
        $this->assertEquals($expectedAge, $company->getCompanyAge());
    }

    /** @test */
    public function it_returns_null_age_for_companies_without_establishment_year(): void
    {
        $company = Company::factory()->create(['established_in' => null]);

        $this->assertNull($company->getCompanyAge());
    }

    /** @test */
    public function it_can_get_employee_range_description(): void
    {
        $companySize = CompanySize::factory()->create(['size' => '50-100 employees']);
        $company = Company::factory()->create(['company_size_id' => $companySize->id]);

        $this->assertEquals('50-100 employees', $company->getEmployeeRangeDescription());
    }

    /** @test */
    public function it_returns_employee_count_when_no_size_category(): void
    {
        $company = Company::factory()->create([
            'company_size_id' => null,
            'no_of_employees' => 75,
        ]);

        $this->assertEquals('75 employees', $company->getEmployeeRangeDescription());
    }
}
