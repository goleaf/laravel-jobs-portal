<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Job;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\OwnerShipType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function company_can_be_created()
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
    public function company_can_be_updated()
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
    public function company_belongs_to_user()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user_id);
    }

    /** @test */
    public function company_belongs_to_industry()
    {
        $industry = Industry::factory()->create();
        $company = Company::factory()->create(['industry_id' => $industry->id]);
        
        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($industry->id, $company->industry_id);
    }

    /** @test */
    public function company_belongs_to_ownership_type()
    {
        $ownershipType = OwnerShipType::factory()->create();
        $company = Company::factory()->create(['ownership_type_id' => $ownershipType->id]);
        
        $this->assertInstanceOf(OwnerShipType::class, $company->ownershipType);
        $this->assertEquals($ownershipType->id, $company->ownership_type_id);
    }

    /** @test */
    public function company_belongs_to_company_size()
    {
        $companySize = CompanySize::factory()->create();
        $company = Company::factory()->create(['size_id' => $companySize->id]);
        
        $this->assertInstanceOf(CompanySize::class, $company->companySize);
        $this->assertEquals($companySize->id, $company->size_id);
    }

    /** @test */
    public function company_can_have_jobs()
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
    public function companies_can_be_filtered_by_featured_status()
    {
        Company::factory()->count(3)->create(['is_featured' => true]);
        Company::factory()->count(2)->create(['is_featured' => false]);
        
        $featuredCompanies = Company::where('is_featured', true)->get();
        $nonFeaturedCompanies = Company::where('is_featured', false)->get();
        
        $this->assertCount(3, $featuredCompanies);
        $this->assertCount(2, $nonFeaturedCompanies);
    }

    /** @test */
    public function companies_can_be_filtered_by_active_status()
    {
        Company::factory()->count(3)->create(['is_active' => true]);
        Company::factory()->count(2)->create(['is_active' => false]);
        
        $activeCompanies = Company::where('is_active', true)->get();
        $inactiveCompanies = Company::where('is_active', false)->get();
        
        $this->assertCount(3, $activeCompanies);
        $this->assertCount(2, $inactiveCompanies);
    }
} 