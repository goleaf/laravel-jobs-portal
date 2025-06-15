<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CompanySize;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CompanySizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CompanySize::factory()->create();
        
        $this->assertInstanceOf(CompanySize::class, $model);
        $this->assertDatabaseHas('company_sizes', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CompanySize();
        $fillable = $model->getFillable();
        
        $expectedFillable = ['size', 'is_default', 'is_active'];
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable);
        }
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CompanySize();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        $this->assertArrayHasKey('id', $casts);
        $this->assertArrayHasKey('is_default', $casts);
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertArrayHasKey('created_at', $casts);
        $this->assertArrayHasKey('updated_at', $casts);
        
        // Verify cast types
        $this->assertEquals('int', $casts['id']);
        $this->assertEquals('boolean', $casts['is_default']);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CompanySize::factory()->create();
        
        $newData = ['size' => 'Updated Size'];
        $model->update($newData);
        
        $this->assertDatabaseHas('company_sizes', [
            'id' => $model->id,
            'size' => 'Updated Size'
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CompanySize::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('company_sizes', [
            'id' => $modelId
        ]);
    }

    /** @test */
    public function it_has_companies_relationship()
    {
        $companySize = CompanySize::factory()->create();
        $companies = Company::factory()->count(3)->create(['company_size_id' => $companySize->id]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $companySize->companies());
        $this->assertCount(3, $companySize->companies);
        $this->assertEquals($companies->pluck('id')->sort(), $companySize->companies->pluck('id')->sort());
    }

    /** @test */
    public function scope_active_returns_active_company_sizes()
    {
        CompanySize::factory()->count(3)->create(['is_active' => true]);
        CompanySize::factory()->count(2)->create(['is_active' => false]);
        
        $activeCompanySizes = CompanySize::active()->get();
        
        $this->assertCount(3, $activeCompanySizes);
        $activeCompanySizes->each(function ($companySize) {
            $this->assertTrue($companySize->is_active);
        });
    }

    /** @test */
    public function scope_inactive_returns_inactive_company_sizes()
    {
        CompanySize::factory()->count(3)->create(['is_active' => true]);
        CompanySize::factory()->count(2)->create(['is_active' => false]);
        
        $inactiveCompanySizes = CompanySize::inactive()->get();
        
        $this->assertCount(2, $inactiveCompanySizes);
        $inactiveCompanySizes->each(function ($companySize) {
            $this->assertFalse($companySize->is_active);
        });
    }

    /** @test */
    public function scope_default_returns_default_company_sizes()
    {
        CompanySize::factory()->count(3)->create(['is_default' => false]);
        CompanySize::factory()->count(1)->create(['is_default' => true]);
        
        $defaultCompanySizes = CompanySize::default()->get();
        
        $this->assertCount(1, $defaultCompanySizes);
        $this->assertTrue($defaultCompanySizes->first()->is_default);
    }

    /** @test */
    public function scope_custom_returns_custom_company_sizes()
    {
        CompanySize::factory()->count(2)->create(['is_default' => false]);
        CompanySize::factory()->count(1)->create(['is_default' => true]);
        
        $customCompanySizes = CompanySize::custom()->get();
        
        $this->assertCount(2, $customCompanySizes);
        $customCompanySizes->each(function ($companySize) {
            $this->assertFalse($companySize->is_default);
        });
    }

    /** @test */
    public function scope_search_finds_company_sizes_by_size_name()
    {
        CompanySize::factory()->create(['size' => 'Small Company']);
        CompanySize::factory()->create(['size' => 'Large Corporation']);
        CompanySize::factory()->create(['size' => 'Medium Business']);
        
        $results = CompanySize::search('Small')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('Small Company', $results->first()->size);
    }

    /** @test */
    public function scope_recent_returns_recently_created_company_sizes()
    {
        // Create old company sizes
        CompanySize::factory()->count(2)->create(['created_at' => now()->subDays(60)]);
        
        // Create recent company sizes
        CompanySize::factory()->count(3)->create(['created_at' => now()->subDays(15)]);
        
        $recentCompanySizes = CompanySize::recent(30)->get();
        
        $this->assertCount(3, $recentCompanySizes);
    }

    /** @test */
    public function scope_old_returns_old_company_sizes()
    {
        // Create old company sizes
        CompanySize::factory()->count(2)->create(['created_at' => now()->subDays(400)]);
        
        // Create recent company sizes
        CompanySize::factory()->count(3)->create(['created_at' => now()->subDays(15)]);
        
        $oldCompanySizes = CompanySize::old(365)->get();
        
        $this->assertCount(2, $oldCompanySizes);
    }

    /** @test */
    public function scope_with_companies_returns_company_sizes_that_have_companies()
    {
        $companySizeWithCompanies = CompanySize::factory()->create();
        $companySizeWithoutCompanies = CompanySize::factory()->create();
        
        Company::factory()->create(['company_size_id' => $companySizeWithCompanies->id]);
        
        $companySizesWithCompanies = CompanySize::withCompanies()->get();
        
        $this->assertCount(1, $companySizesWithCompanies);
        $this->assertEquals($companySizeWithCompanies->id, $companySizesWithCompanies->first()->id);
    }

    /** @test */
    public function scope_alphabetical_orders_company_sizes_by_size_name()
    {
        CompanySize::factory()->create(['size' => 'Zebra Company']);
        CompanySize::factory()->create(['size' => 'Alpha Corporation']);
        CompanySize::factory()->create(['size' => 'Beta Business']);
        
        $orderedCompanySizes = CompanySize::alphabetical()->get();
        
        $this->assertEquals('Alpha Corporation', $orderedCompanySizes->first()->size);
        $this->assertEquals('Zebra Company', $orderedCompanySizes->last()->size);
    }

    /** @test */
    public function scope_popular_returns_most_used_company_sizes()
    {
        $popularCompanySize = CompanySize::factory()->create();
        $lessPopularCompanySize = CompanySize::factory()->create();
        
        // Create more companies for the popular size
        Company::factory()->count(5)->create(['company_size_id' => $popularCompanySize->id]);
        Company::factory()->count(2)->create(['company_size_id' => $lessPopularCompanySize->id]);
        
        $popularCompanySizes = CompanySize::popular(1)->get();
        
        $this->assertCount(1, $popularCompanySizes);
        $this->assertEquals($popularCompanySize->id, $popularCompanySizes->first()->id);
    }

    /** @test */
    public function scope_small_returns_small_company_sizes()
    {
        CompanySize::factory()->create(['size' => 'Small Business']);
        CompanySize::factory()->create(['size' => 'Startup']);
        CompanySize::factory()->create(['size' => 'Large Corporation']);
        
        $smallCompanySizes = CompanySize::small()->get();
        
        $this->assertCount(2, $smallCompanySizes);
    }

    /** @test */
    public function scope_medium_returns_medium_company_sizes()
    {
        CompanySize::factory()->create(['size' => 'Medium Business']);
        CompanySize::factory()->create(['size' => 'Mid-size Company']);
        CompanySize::factory()->create(['size' => 'Small Startup']);
        
        $mediumCompanySizes = CompanySize::medium()->get();
        
        $this->assertCount(2, $mediumCompanySizes);
    }

    /** @test */
    public function scope_large_returns_large_company_sizes()
    {
        CompanySize::factory()->create(['size' => 'Large Corporation']);
        CompanySize::factory()->create(['size' => 'Enterprise']);
        CompanySize::factory()->create(['size' => 'Small Business']);
        
        $largeCompanySizes = CompanySize::large()->get();
        
        $this->assertCount(2, $largeCompanySizes);
    }
}