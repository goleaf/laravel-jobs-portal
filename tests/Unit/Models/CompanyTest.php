<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\User;
use App\Models\Job;
use App\Models\Industry;
use App\Models\CompanySize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_company()
    {
        $company = Company::factory()->create();
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    /** @test */
    public function a_company_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user->id);
    }

    /** @test */
    public function a_company_has_jobs()
    {
        $company = Company::factory()->create();
        Job::factory(3)->create(['company_id' => $company->id]);
        
        $this->assertCount(3, $company->jobs);
        $this->assertInstanceOf(Job::class, $company->jobs->first());
    }

    /** @test */
    public function a_company_belongs_to_an_industry()
    {
        $industry = Industry::factory()->create();
        $company = Company::factory()->create(['industry_id' => $industry->id]);
        
        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($industry->id, $company->industry->id);
    }

    /** @test */
    public function a_company_belongs_to_a_company_size()
    {
        $companySize = CompanySize::factory()->create();
        $company = Company::factory()->create(['company_size_id' => $companySize->id]);
        
        $this->assertInstanceOf(CompanySize::class, $company->companySize);
        $this->assertEquals($companySize->id, $company->companySize->id);
    }

    /** @test */
    public function it_can_filter_active_companies()
    {
        $activeCompany = Company::factory()->create(['is_active' => true]);
        $inactiveCompany = Company::factory()->create(['is_active' => false]);
        
        $activeCompanies = Company::whereIsActive()->get();
        
        $this->assertTrue($activeCompanies->contains($activeCompany));
        $this->assertFalse($activeCompanies->contains($inactiveCompany));
    }

    /** @test */
    public function it_can_filter_featured_companies()
    {
        $featuredCompany = Company::factory()->create(['is_featured' => true]);
        $regularCompany = Company::factory()->create(['is_featured' => false]);
        
        $featuredCompanies = Company::whereFeatured()->get();
        
        $this->assertTrue($featuredCompanies->contains($featuredCompany));
        $this->assertFalse($featuredCompanies->contains($regularCompany));
    }
} 