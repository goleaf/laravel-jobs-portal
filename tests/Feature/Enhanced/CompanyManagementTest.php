<?php

namespace Tests\Feature\Enhanced;

use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\Industry;
use App\Models\OwnerShipType;
use App\Models\Plan;
use App\Models\User;
use App\Services\EnhancedCompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Employer']);
        Role::create(['name' => 'Candidate']);

        // Create required data
        Industry::factory()->create(['id' => 1, 'name' => 'Technology']);
        CompanySize::factory()->create(['id' => 1, 'size' => 'Small (1-50)']);
        OwnerShipType::factory()->create(['id' => 1, 'name' => 'Private']);
        Country::factory()->create(['id' => 1, 'name' => 'United States']);
        Plan::factory()->create(['id' => 1, 'name' => 'Basic Plan', 'is_trial_plan' => true]);

        Storage::fake('public');
    }

    /** @test */
    public function adminCanViewCompaniesIndex()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Company::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('companies.index');
        $response->assertViewHas('companies');
    }

    /** @test */
    public function employerCanCreateCompanyWithValidData()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $companyData = [
            'name' => 'Test Company Ltd',
            'email' => 'contact@testcompany.com',
            'password' => 'SecurePassword123!',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'country_id' => 1,
            'established_in' => 2020,
            'website' => 'https://testcompany.com',
            'location' => 'New York, NY',
            'no_of_offices' => 3,
            'details' => 'This is a test company for software development.',
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        if (422 === $response->status()) {
            dump('Validation errors:', $response->json());
        }

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company Ltd',
            'ceo' => 'John Doe',
            'user_id' => $employer->id,
            'slug' => 'test-company-ltd',
        ]);
    }

    /** @test */
    public function employerCanCreateCompanyWithLogo()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $logo = UploadedFile::fake()->image('logo.png', 200, 200);

        $companyData = [
            'name' => 'Logo Test Company',
            'ceo' => 'Jane Smith',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2019,
            'website' => 'https://logotest.com',
            'location' => 'San Francisco, CA',
            'no_of_offices' => 1,
            'details' => 'Company with logo upload test.',
            'logo' => $logo,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertRedirect();

        $company = Company::where('name', 'Logo Test Company')->first();
        $this->assertNotNull($company);
        $this->assertNotNull($company->logo_path);

        Storage::disk('public')->assertExists($company->logo_path);
    }

    /** @test */
    public function employerCannotCreateSecondCompany()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        // Create first company
        Company::factory()->create(['user_id' => $employer->id]);

        $companyData = [
            'name' => 'Second Company',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertStatus(403);
    }

    /** @test */
    public function candidateCannotCreateCompany()
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $companyData = [
            'name' => 'Unauthorized Company',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
        ];

        $response = $this->actingAs($candidate)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertStatus(403);
    }

    /** @test */
    public function companyCreationValidatesRequiredFields()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), [])
        ;

        $response->assertSessionHasErrors([
            'name', 'ceo', 'industry_id', 'ownership_type_id',
            'company_size_id', 'established_in', 'location', 'no_of_offices',
        ]);
    }

    /** @test */
    public function companyCreationValidatesUniqueName()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        Company::factory()->create(['name' => 'Existing Company']);

        $companyData = [
            'name' => 'Existing Company',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function companyCreationValidatesEstablishmentYear()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $companyData = [
            'name' => 'Future Company',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => date('Y') + 1, // Future year
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertSessionHasErrors(['established_in']);
    }

    /** @test */
    public function companyCreationValidatesLogoFile()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $companyData = [
            'name' => 'Invalid Logo Company',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
            'logo' => $invalidFile,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $companyData)
        ;

        $response->assertSessionHasErrors(['logo']);
    }

    /** @test */
    public function employerCanUpdateOwnCompany()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $company = Company::factory()->create(['user_id' => $employer->id]);

        $updateData = [
            'name' => 'Updated Company Name',
            'ceo' => 'Updated CEO',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2019,
            'location' => 'Updated Location',
            'no_of_offices' => 5,
            'details' => 'Updated company details.',
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->put(route('companies.update', $company), $updateData)
        ;

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name',
            'ceo' => 'Updated CEO',
        ]);
    }

    /** @test */
    public function employerCannotUpdateOtherCompany()
    {
        $employer1 = User::factory()->create();
        $employer1->assignRole('Employer');

        $employer2 = User::factory()->create();
        $employer2->assignRole('Employer');

        $company = Company::factory()->create(['user_id' => $employer2->id]);

        $updateData = [
            'name' => 'Unauthorized Update',
            'ceo' => 'Hacker CEO',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Hacker Location',
            'no_of_offices' => 1,
        ];

        $response = $this->actingAs($employer1)
            ->put(route('companies.update', $company), $updateData)
        ;

        $response->assertStatus(403);
    }

    /** @test */
    public function adminCanUpdateAnyCompany()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $company = Company::factory()->create(['user_id' => $employer->id]);

        $updateData = [
            'name' => 'Admin Updated Company',
            'ceo' => 'Admin Updated CEO',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2020,
            'location' => 'Admin Location',
            'no_of_offices' => 10,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($admin)
            ->put(route('companies.update', $company), $updateData)
        ;

        $response->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Admin Updated Company',
        ]);
    }

    /** @test */
    public function adminCanDeleteCompany()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $company = Company::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('companies.destroy', $company))
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    /** @test */
    public function employerCanDeleteOwnCompany()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $company = Company::factory()->create(['user_id' => $employer->id]);

        $response = $this->actingAs($employer)
            ->delete(route('companies.destroy', $company))
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    /** @test */
    public function companySearchWorksCorrectly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Company::factory()->create(['name' => 'Tech Solutions Inc']);
        Company::factory()->create(['name' => 'Marketing Agency']);
        Company::factory()->create(['name' => 'Tech Innovations']);

        $response = $this->actingAs($admin)
            ->get(route('companies.index', ['search' => 'Tech']))
        ;

        $response->assertStatus(200);
        $response->assertViewHas('companies');

        $companies = $response->viewData('companies');
        $this->assertCount(2, $companies);
    }

    /** @test */
    public function companyCanBeMarkedAsFeatured()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $company = Company::factory()->create(['is_featured' => false]);

        $response = $this->actingAs($admin)
            ->post(route('companies.mark-featured', $company))
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'is_featured' => true,
        ]);
    }

    /** @test */
    public function onlyAdminCanMarkCompanyAsFeatured()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $company = Company::factory()->create(['user_id' => $employer->id]);

        $response = $this->actingAs($employer)
            ->post(route('companies.mark-featured', $company))
        ;

        $response->assertStatus(403);
    }

    /** @test */
    public function companyStatusCanBeChanged()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $company = Company::factory()->create(['status' => Company::STATUS_ACTIVE]);

        $response = $this->actingAs($admin)
            ->post(route('companies.change-status', $company))
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $company->refresh();
        $this->assertEquals(Company::STATUS_INACTIVE, $company->status);
    }

    /** @test */
    public function companyServiceCreatesCompanyWithSlug()
    {
        $service = app(EnhancedCompanyService::class);

        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $data = [
            'name' => 'Test Company for Slug',
            'ceo' => 'John Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2020,
            'location' => 'New York',
            'no_of_offices' => 1,
            'user_id' => $employer->id,
        ];

        $company = $service->createCompany($data);

        $this->assertEquals('test-company-for-slug', $company->slug);
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company for Slug',
            'slug' => 'test-company-for-slug',
        ]);
    }

    /** @test */
    public function companyServiceHandlesDuplicateSlugs()
    {
        $service = app(EnhancedCompanyService::class);

        $employer1 = User::factory()->create();
        $employer1->assignRole('Employer');

        $employer2 = User::factory()->create();
        $employer2->assignRole('Employer');

        // Create first company
        Company::factory()->create([
            'name' => 'Duplicate Name',
            'slug' => 'duplicate-name',
            'user_id' => $employer1->id,
        ]);

        $data = [
            'name' => 'Duplicate Name',
            'ceo' => 'Jane Doe',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston',
            'no_of_offices' => 1,
            'user_id' => $employer2->id,
        ];

        $company = $service->createCompany($data);

        $this->assertEquals('duplicate-name-1', $company->slug);
    }

    /** @test */
    public function companyStatisticsAreCalculatedCorrectly()
    {
        $service = app(EnhancedCompanyService::class);

        Company::factory()->count(5)->create(['status' => Company::STATUS_ACTIVE]);
        Company::factory()->count(2)->create(['status' => Company::STATUS_INACTIVE]);
        Company::factory()->count(3)->create(['is_featured' => true]);

        $stats = $service->getCompanyStats();

        $this->assertEquals(7, $stats['total_companies']);
        $this->assertEquals(5, $stats['active_companies']);
        $this->assertEquals(3, $stats['featured_companies']);
    }

    /** @test */
    public function guestCannotAccessCompanyCreation()
    {
        $response = $this->get(route('companies.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function companyValidationPreventsXss()
    {
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $maliciousData = [
            'name' => '<script>alert("xss")</script>Evil Company',
            'ceo' => '<img src=x onerror=alert("xss")>',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2021,
            'location' => 'Boston, MA',
            'no_of_offices' => 1,
            'user_id' => $employer->id,
        ];

        $response = $this->actingAs($employer)
            ->post(route('companies.store'), $maliciousData);

        $response->assertSessionHasErrors(['name', 'ceo']);
    }
}
