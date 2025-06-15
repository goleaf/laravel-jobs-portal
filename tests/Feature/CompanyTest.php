<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Job;
use App\Models\OwnerShipType;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;
    protected $employerUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['user_type' => User::ADMIN]);
        $this->employerUser = User::factory()->create(['user_type' => User::EMPLOYER]);
        $this->company = Company::factory()->create(['user_id' => $this->employerUser->id]);
        $this->employerUser->company()->save($this->company);
        $this->employerUser->load('company');
    }

    /** @test */
    public function companyCanBeCreated()
    {
        $user = User::factory()->create();

        $companyData = [
            'user_id' => $user->id,
            'name' => $this->faker->company,
            'website' => $this->faker->url,
            'location' => $this->faker->address,
            'industry_id' => 1,
            'size_id' => 1,
            'ownership_type_id' => 1,
            'established_in' => $this->faker->year,
            'is_featured' => false,
            'details' => $this->faker->paragraph,
            'facebook_url' => $this->faker->url,
            'twitter_url' => $this->faker->url,
            'linkedin_url' => $this->faker->url,
            'google_plus_url' => $this->faker->url,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'no_of_offices' => $this->faker->randomDigit(),
            'no_of_employees' => $this->faker->randomNumber(3),
            'is_active' => true,
        ];

        $company = Company::create($companyData);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals($companyData['user_id'], $company->user_id);
        $this->assertEquals($companyData['name'], $company->name);
        $this->assertEquals($companyData['website'], $company->website);
        $this->assertEquals($companyData['location'], $company->location);
        $this->assertTrue($company->is_active);
    }

    /** @test */
    public function companyCanBeUpdated()
    {
        $company = Company::factory()->create();

        $updatedData = [
            'name' => $this->faker->company,
            'website' => $this->faker->url,
            'is_featured' => true,
            'details' => $this->faker->paragraph,
        ];

        $company->update($updatedData);
        $company->refresh();

        $this->assertEquals($updatedData['name'], $company->name);
        $this->assertEquals($updatedData['website'], $company->website);
        $this->assertTrue($company->is_featured);
        $this->assertEquals($updatedData['details'], $company->details);
    }

    /** @test */
    public function companyBelongsToUser()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user_id);
    }

    /** @test */
    public function companyBelongsToIndustry()
    {
        $industry = Industry::factory()->create();
        $company = Company::factory()->create(['industry_id' => $industry->id]);

        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($industry->id, $company->industry_id);
    }

    /** @test */
    public function companyBelongsToOwnershipType()
    {
        $ownershipType = OwnerShipType::factory()->create();
        $company = Company::factory()->create(['ownership_type_id' => $ownershipType->id]);

        $this->assertInstanceOf(OwnerShipType::class, $company->ownershipType);
        $this->assertEquals($ownershipType->id, $company->ownership_type_id);
    }

    /** @test */
    public function companyBelongsToCompanySize()
    {
        $companySize = CompanySize::factory()->create();
        $company = Company::factory()->create(['size_id' => $companySize->id]);

        $this->assertInstanceOf(CompanySize::class, $company->companySize);
        $this->assertEquals($companySize->id, $company->size_id);
    }

    /** @test */
    public function companyCanHaveJobs()
    {
        $company = Company::factory()->create();

        $job = Job::factory()->create([
            'company_id' => $company->id,
            'job_title' => $this->faker->jobTitle,
            'status' => Job::STATUS_OPEN,
        ]);

        $this->assertInstanceOf(Job::class, $company->jobs->first());
        $this->assertCount(1, $company->jobs);
        $this->assertEquals($job->id, $company->jobs->first()->id);
    }

    /** @test */
    public function companiesCanBeFilteredByFeaturedStatus()
    {
        Company::factory()->count(3)->create(['is_featured' => true]);
        Company::factory()->count(2)->create(['is_featured' => false]);

        $featuredCompanies = Company::where('is_featured', true)->get();
        $nonFeaturedCompanies = Company::where('is_featured', false)->get();

        $this->assertCount(3, $featuredCompanies);
        $this->assertCount(2, $nonFeaturedCompanies);
    }

    /** @test */
    public function companiesCanBeFilteredByActiveStatus()
    {
        Company::factory()->count(3)->create(['is_active' => true]);
        Company::factory()->count(2)->create(['is_active' => false]);

        $activeCompanies = Company::where('is_active', true)->get();
        $inactiveCompanies = Company::where('is_active', false)->get();

        $this->assertCount(3, $activeCompanies);
        $this->assertCount(2, $inactiveCompanies);
    }

    // =========================================
    // Admin Company Management Tests
    // =========================================

    /** @test */
    public function guestsCannotAccessAdminCompaniesSection()
    {
        $this->get('/admin/companies')->assertRedirect('/login');
        $this->get('/admin/companies/create')->assertRedirect('/login');
        $this->post('/admin/companies')->assertRedirect('/login');
    }

    /** @test */
    public function nonAdminUsersCannotAccessAdminCompaniesSection()
    {
        $candidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Test candidate access
        $this->actingAs($candidateUser)->get('/admin/companies')->assertStatus(403);
        $this->actingAs($candidateUser)->get('/admin/companies/create')->assertStatus(403);

        // Test employer access
        $this->actingAs($this->employerUser)->get('/admin/companies')->assertStatus(403);
        $this->actingAs($this->employerUser)->get('/admin/companies/create')->assertStatus(403);
    }

    /** @test */
    public function adminCanViewCompaniesList()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/companies');
        $response->assertStatus(200);
        $response->assertViewIs('companies.index');
    }

    /** @test */
    public function adminCanViewCompanyDetails()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/companies/{$this->company->id}");
        $response->assertStatus(200);
        $response->assertViewIs('companies.show');
        $response->assertSee($this->company->name);
    }

    /** @test */
    public function adminCanViewEditCompanyForm()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/companies/{$this->company->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('companies.edit');
        $response->assertSee($this->company->name);
    }

    /** @test */
    public function adminCanUpdateCompany()
    {
        $updateData = $this->getCompanyData('Updated Company Name');
        // Unset fields not expected in admin update, or ensure factories exist
        unset($updateData['user_id']);

        $response = $this->actingAs($this->adminUser)->put("/admin/companies/{$this->company->id}", $updateData);

        $response->assertRedirect('/admin/companies');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name',
            'website' => $updateData['website'],
        ]);
    }

    /** @test */
    public function adminCanDeleteCompany()
    {
        // Create a separate company/user for deletion test
        $tempEmployer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $tempCompany = Company::factory()->create(['user_id' => $tempEmployer->id]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/companies/{$tempCompany->id}");

        // Assuming JSON response
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('companies', ['id' => $tempCompany->id]);
        $this->assertDatabaseMissing('users', ['id' => $tempEmployer->id]);
    }

    /** @test */
    public function adminCanChangeCompanyActiveStatus()
    {
        $initialStatus = $this->employerUser->is_active;

        $response = $this->actingAs($this->adminUser)->postJson("/admin/companies/{$this->company->id}/change-status");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->employerUser->refresh();
        $this->assertEquals(!$initialStatus, $this->employerUser->is_active);
    }

    /** @test */
    public function adminCanMarkCompanyAsFeaturedAndUnfeatured()
    {
        // Mark as featured
        $response = $this->actingAs($this->adminUser)->postJson("/admin/companies/{$this->company->id}/mark-featured");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('featured_records', [
            'owner_id' => $this->company->id,
            'owner_type' => Company::class,
        ]);
        $this->company->refresh();
        $this->assertTrue($this->company->is_featured);

        // Mark as un-featured
        $response = $this->actingAs($this->adminUser)->postJson("/admin/companies/{$this->company->id}/unmark-featured");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('featured_records', [
            'owner_id' => $this->company->id,
            'owner_type' => Company::class,
        ]);
        $this->company->refresh();
        $this->assertFalse($this->company->is_featured);
    }

    /** @test */
    public function adminCanChangeCompanyEmailVerifiedStatus()
    {
        $this->employerUser->update(['email_verified_at' => null]); // Ensure not verified

        $response = $this->actingAs($this->adminUser)->postJson("/admin/companies/{$this->company->id}/change-is-verified");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->employerUser->refresh();
        $this->assertNotNull($this->employerUser->email_verified_at);

        // Test un-verifying
        $response = $this->actingAs($this->adminUser)->postJson("/admin/companies/{$this->company->id}/change-is-verified");
        $response->assertStatus(200);
        $this->employerUser->refresh();
        $this->assertNull($this->employerUser->email_verified_at);
    }

    // =========================================
    // Employer Company Profile Tests
    // =========================================

    /** @test */
    public function employerCanViewTheirCompanyEditForm()
    {
        $response = $this->actingAs($this->employerUser)->get("/employer/company/{$this->company->id}/edit"); // Assuming route matches editCompany action

        $response->assertStatus(200);
        $response->assertViewIs('employer.companies.edit');
        $response->assertSee($this->company->name);
    }

    /** @test */
    public function employerCannotViewOtherCompanyEditForm()
    {
        $otherEmployer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $otherCompany = Company::factory()->create(['user_id' => $otherEmployer->id]);

        $response = $this->actingAs($this->employerUser)->get("/employer/company/{$otherCompany->id}/edit");

        $response->assertStatus(404); // Or 403
    }

    /** @test */
    public function employerCanUpdateTheirCompanyProfile()
    {
        $updateData = $this->getCompanyData('Updated Profile Name');
        // Unset fields not expected in employer update or ensure factories exist
        unset($updateData['user_id'], $updateData['is_active']);
        // Usually only admin changes this

        // Use the route name from the controller redirect if available, otherwise guess
        // $response = $this->actingAs($this->employerUser)->put(route('company.update.form', $this->company->id), $updateData);
        $response = $this->actingAs($this->employerUser)->put("/employer/company/{$this->company->id}", $updateData); // Guessing route

        // Check redirect based on updateCompany method
        $response->assertRedirect(route('company.edit.form', $this->company->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Profile Name',
            'website' => $updateData['website'],
        ]);
    }

    /**
     * Helper function to get valid company data for tests.
     */
    protected function getCompanyData(?string $name = null): array
    {
        // Ensure related models exist or create them
        if (!Country::find(1)) {
            Country::factory()->create(['id' => 1]);
        }
        if (!State::find(1)) {
            State::factory()->create(['id' => 1, 'country_id' => 1]);
        }
        if (!City::find(1)) {
            City::factory()->create(['id' => 1, 'state_id' => 1]);
        }

        return [
            'name' => $name ?? $this->faker->company,
            'email' => $this->faker->unique()->safeEmail, // User field
            'password' => 'password123', // User field - needed for validation
            'password_confirmation' => 'password123', // User field - needed for validation
            'user_id' => $this->employerUser->id, // Needed for some contexts
            'website' => $this->faker->url,
            'location' => $this->faker->address,
            'industry_id' => Industry::factory()->create()->id,
            'size_id' => CompanySize::factory()->create()->id,
            'ownership_type_id' => OwnerShipType::factory()->create()->id,
            'established_in' => $this->faker->year,
            'details' => $this->faker->paragraph,
            'facebook_url' => $this->faker->url,
            'twitter_url' => $this->faker->url,
            'linkedin_url' => $this->faker->url,
            'google_plus_url' => $this->faker->url,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'phone' => $this->faker->phoneNumber, // User field
            'region_code' => 'GB', // User field
            'user_type' => User::EMPLOYER, // User field - needed for validation
            'no_of_offices' => $this->faker->randomDigitNotNull,
            'is_active' => true, // Usually only admin changes this
            // Add other fields from Create/UpdateCompanyRequest as needed
        ];
    }
}
