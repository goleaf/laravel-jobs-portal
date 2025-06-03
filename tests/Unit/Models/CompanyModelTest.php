<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\OwnerShipType;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required data for tests
        $this->industry = Industry::factory()->create();
        $this->companySize = CompanySize::factory()->create();
        $this->ownershipType = OwnerShipType::factory()->create();
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(1, Company::ISACTIVE);
        $this->assertEquals(0, Company::DEACTIVE);
        $this->assertEquals(2, Company::ALL);
        $this->assertEquals(0, Company::COMPANY_LOGIN_TYPE);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $company = new Company();
        $fillable = $company->getFillable();

        $expectedFillable = [
            'ceo',
            'industry_id',
            'ownership_type_id',
            'company_size_id',
            'established_in',
            'details',
            'website',
            'location',
            'location2',
            'no_of_offices',
            'fax',
            'user_id',
            'unique_id',
            'last_change',
            'logo_path',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $company = new Company();
        $casts = $company->getCasts();

        $expectedCasts = [
            'id' => 'integer',
            'industry_id' => 'integer',
            'ownership_type_id' => 'integer',
            'company_size_id' => 'integer',
            'established_in' => 'integer',
            'no_of_offices' => 'integer',
            'user_id' => 'integer',
            'last_change' => 'integer',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_has_is_featured_constants()
    {
        $expectedIsFeatured = [
            2 => 'Select Featured Company',
            1 => 'Yes',
            0 => 'No',
        ];

        $this->assertEquals($expectedIsFeatured, Company::IS_FEATURED);
    }

    /** @test */
    public function it_has_status_constants_array()
    {
        $expectedStatus = [
            2 => 'ALL',
            1 => 'Active',
            0 => 'Deactive',
        ];

        $this->assertEquals($expectedStatus, Company::STATUS);
    }

    /** @test */
    public function it_has_btn_color_constants()
    {
        $expectedColors = [
            'btn btn-green btn-small-effect',
            'btn btn-purple btn-small btn-effect',
            'btn btn-blue btn-small btn-effect',
            'btn btn-orange btn-small btn-effect',
            'btn btn-red btn-small btn-effect',
            'btn btn-blue-grey btn-small btn-effect',
            'btn btn-green btn-small btn-effect',
        ];

        $this->assertEquals($expectedColors, Company::BTN_BTN_COLOR);
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $company = new Company([
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 2,
            'established_in' => 2010,
            'details' => 'A great technology company',
            'website' => 'https://example.com',
            'location' => 'New York',
            'location2' => 'NY',
            'no_of_offices' => 5,
            'fax' => '+1-555-123-4567',
            'unique_id' => 'COMP123456',
        ]);

        $this->assertEquals('John Doe', $company->ceo);
        $this->assertEquals(1, $company->industry_id);
        $this->assertEquals(1, $company->ownership_type_id);
        $this->assertEquals(2, $company->company_size_id);
        $this->assertEquals(2010, $company->established_in);
        $this->assertEquals('A great technology company', $company->details);
        $this->assertEquals('https://example.com', $company->website);
        $this->assertEquals('New York', $company->location);
        $this->assertEquals('NY', $company->location2);
        $this->assertEquals(5, $company->no_of_offices);
        $this->assertEquals('+1-555-123-4567', $company->fax);
        $this->assertEquals('COMP123456', $company->unique_id);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $company = new Company();
        
        // Test that relationship methods exist by checking they are callable
        $this->assertTrue(method_exists($company, 'industry'));
        $this->assertTrue(method_exists($company, 'ownerShipType'));
        $this->assertTrue(method_exists($company, 'companySize'));
        $this->assertTrue(method_exists($company, 'user'));
        $this->assertTrue(method_exists($company, 'jobs'));
        $this->assertTrue(method_exists($company, 'featured'));
        $this->assertTrue(method_exists($company, 'activeFeatured'));
        $this->assertTrue(method_exists($company, 'admin'));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $company = new Company();
        $this->assertEquals('companies', $company->getTable());
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'ceo' => 'required|max:180',
            'industry_id' => 'required',
            'ownership_type_id' => 'required',
            'company_size_id' => 'required',
            'established_in' => 'required',
            'website' => 'nullable|url',
            'location' => 'required',
            'no_of_offices' => 'required|numeric|min:1|max:1000',
        ];

        $this->assertEquals($expectedRules, Company::$rules);
    }

    public function test_company_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user->id);
    }

    public function test_company_belongs_to_industry(): void
    {
        $company = Company::factory()->create([
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($this->industry->name, $company->industry->name);
    }

    public function test_company_belongs_to_company_size(): void
    {
        $company = Company::factory()->create([
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        $this->assertInstanceOf(CompanySize::class, $company->companySize);
        $this->assertEquals($this->companySize->size, $company->companySize->size);
    }

    public function test_company_belongs_to_ownership_type(): void
    {
        $company = Company::factory()->create([
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        $this->assertInstanceOf(OwnerShipType::class, $company->ownerShipType);
        $this->assertEquals($this->ownershipType->name, $company->ownerShipType->name);
    }

    public function test_company_has_many_jobs(): void
    {
        $company = Company::factory()->create([
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        Job::factory()->count(3)->create(['company_id' => $company->id]);

        $this->assertCount(3, $company->jobs);
        $this->assertInstanceOf(Job::class, $company->jobs->first());
    }

    public function test_company_fillable_attributes(): void
    {
        $fillableAttributes = [
            'user_id', 'ceo', 'industry_id', 'ownership_type_id', 
            'company_size_id', 'established_in', 'details', 'website',
            'location', 'no_of_offices', 'unique_id'
        ];

        $company = new Company();
        
        $this->assertEquals($fillableAttributes, $company->getFillable());
    }

    public function test_company_creation_with_all_attributes(): void
    {
        $user = User::factory()->create();
        
        $companyData = [
            'user_id' => $user->id,
            'ceo' => 'John Doe',
            'industry_id' => $this->industry->id,
            'ownership_type_id' => $this->ownershipType->id,
            'company_size_id' => $this->companySize->id,
            'established_in' => 2020,
            'website' => 'https://example.com',
            'location' => 'New York',
            'no_of_offices' => 5,
            'details' => 'A great company',
            'unique_id' => 'john-doe-2024',
        ];

        $company = Company::create($companyData);

        $this->assertDatabaseHas('companies', $companyData);
        $this->assertEquals('John Doe', $company->ceo);
        $this->assertEquals(2020, $company->established_in);
        $this->assertEquals('https://example.com', $company->website);
    }

    public function test_company_active_scope(): void
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        $activeCompany = Company::factory()->create([
            'user_id' => $activeUser->id,
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);
        
        $inactiveCompany = Company::factory()->create([
            'user_id' => $inactiveUser->id,
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        $activeCompanies = Company::whereHas('user', fn($q) => $q->where('is_active', true))->get();

        $this->assertTrue($activeCompanies->contains($activeCompany));
        $this->assertFalse($activeCompanies->contains($inactiveCompany));
    }

    public function test_company_active_jobs_relationship(): void
    {
        $company = Company::factory()->create([
            'industry_id' => $this->industry->id,
            'company_size_id' => $this->companySize->id,
            'ownership_type_id' => $this->ownershipType->id,
        ]);

        // Create jobs with different statuses
        Job::factory()->create(['company_id' => $company->id, 'status' => 1]); // Active
        Job::factory()->create(['company_id' => $company->id, 'status' => 1]); // Active
        Job::factory()->create(['company_id' => $company->id, 'status' => 2]); // Closed

        $this->assertCount(3, $company->jobs);
        $this->assertCount(2, $company->jobs()->where('status', 1)->get());
    }
} 