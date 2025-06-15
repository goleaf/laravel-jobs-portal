<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\CompanySize;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanySizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = CompanySize::factory()->create();

        $this->assertInstanceOf(CompanySize::class, $model);
        $this->assertDatabaseHas('company_sizes', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
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
    public function itHasProperCasts()
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
    public function itCanBeUpdated()
    {
        $model = CompanySize::factory()->create();

        $newData = ['size' => 'Updated Size'];
        $model->update($newData);

        $this->assertDatabaseHas('company_sizes', [
            'id' => $model->id,
            'size' => 'Updated Size',
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = CompanySize::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('company_sizes', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function itHasCompaniesRelationship()
    {
        $companySize = CompanySize::factory()->create();
        $companies = Company::factory()->count(3)->create(['company_size_id' => $companySize->id]);

        $this->assertInstanceOf(HasMany::class, $companySize->companies());
        $this->assertCount(3, $companySize->companies);
        $this->assertEquals($companies->pluck('id')->sort(), $companySize->companies->pluck('id')->sort());
    }

    /** @test */
    public function scopeActiveReturnsActiveCompanySizes()
    {
        // Get initial count of active company sizes
        $initialActiveCount = CompanySize::active()->count();

        CompanySize::factory()->count(3)->create(['is_active' => true]);
        CompanySize::factory()->count(2)->create(['is_active' => false]);

        $activeCompanySizes = CompanySize::active()->get();

        // Should have 3 more active company sizes than before
        $this->assertCount($initialActiveCount + 3, $activeCompanySizes);
        $activeCompanySizes->each(function ($companySize) {
            $this->assertTrue($companySize->is_active);
        });
    }

    /** @test */
    public function scopeInactiveReturnsInactiveCompanySizes()
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
    public function scopeDefaultReturnsDefaultCompanySizes()
    {
        // Get initial count of default company sizes
        $initialDefaultCount = CompanySize::default()->count();

        CompanySize::factory()->count(3)->create(['is_default' => false]);
        CompanySize::factory()->count(1)->create(['is_default' => true]);

        $defaultCompanySizes = CompanySize::default()->get();

        // Should have 1 more default company size than before
        $this->assertCount($initialDefaultCount + 1, $defaultCompanySizes);
        $defaultCompanySizes->each(function ($companySize) {
            $this->assertTrue($companySize->is_default);
        });
    }

    /** @test */
    public function scopeCustomReturnsCustomCompanySizes()
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
    public function scopeSearchFindsCompanySizesBySizeName()
    {
        CompanySize::factory()->create(['size' => 'Small Company']);
        CompanySize::factory()->create(['size' => 'Large Corporation']);
        CompanySize::factory()->create(['size' => 'Medium Business']);

        $results = CompanySize::search('Small')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Small Company', $results->first()->size);
    }

    /** @test */
    public function scopeRecentReturnsRecentlyCreatedCompanySizes()
    {
        // Get initial count of recent company sizes
        $initialRecentCount = CompanySize::recent(30)->count();

        // Create old company sizes
        CompanySize::factory()->count(2)->create(['created_at' => now()->subDays(60)]);

        // Create recent company sizes
        CompanySize::factory()->count(3)->create(['created_at' => now()->subDays(15)]);

        $recentCompanySizes = CompanySize::recent(30)->get();

        // Should have 3 more recent company sizes than before
        $this->assertCount($initialRecentCount + 3, $recentCompanySizes);
    }

    /** @test */
    public function scopeOldReturnsOldCompanySizes()
    {
        // Create old company sizes
        CompanySize::factory()->count(2)->create(['created_at' => now()->subDays(400)]);

        // Create recent company sizes
        CompanySize::factory()->count(3)->create(['created_at' => now()->subDays(15)]);

        $oldCompanySizes = CompanySize::old(365)->get();

        $this->assertCount(2, $oldCompanySizes);
    }

    /** @test */
    public function scopeWithCompaniesReturnsCompanySizesThatHaveCompanies()
    {
        // Get initial count of company sizes with companies
        $initialWithCompaniesCount = CompanySize::withCompanies()->count();

        $companySizeWithCompanies = CompanySize::factory()->create();
        $companySizeWithoutCompanies = CompanySize::factory()->create();

        Company::factory()->create(['company_size_id' => $companySizeWithCompanies->id]);

        $companySizesWithCompanies = CompanySize::withCompanies()->get();

        // Should have 1 more company size with companies than before
        $this->assertCount($initialWithCompaniesCount + 1, $companySizesWithCompanies);
        $this->assertTrue($companySizesWithCompanies->contains($companySizeWithCompanies));
    }

    /** @test */
    public function scopeAlphabeticalOrdersCompanySizesBySizeName()
    {
        $testCompanySize1 = CompanySize::factory()->create(['size' => 'Zebra Company']);
        $testCompanySize2 = CompanySize::factory()->create(['size' => 'Alpha Corporation']);
        $testCompanySize3 = CompanySize::factory()->create(['size' => 'Beta Business']);

        $orderedCompanySizes = CompanySize::alphabetical()->get();

        // Find positions of our test company sizes
        $alphaPosition = $orderedCompanySizes->search(function ($companySize) use ($testCompanySize2) {
            return $companySize->id === $testCompanySize2->id;
        });

        $betaPosition = $orderedCompanySizes->search(function ($companySize) use ($testCompanySize3) {
            return $companySize->id === $testCompanySize3->id;
        });

        $zebraPosition = $orderedCompanySizes->search(function ($companySize) use ($testCompanySize1) {
            return $companySize->id === $testCompanySize1->id;
        });

        // Alpha should come before Beta, and Beta should come before Zebra
        $this->assertLessThan($betaPosition, $alphaPosition, 'Alpha Corporation should come before Beta Business');
        $this->assertLessThan($zebraPosition, $betaPosition, 'Beta Business should come before Zebra Company');
    }

    /** @test */
    public function scopePopularReturnsMostUsedCompanySizes()
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
    public function scopeSmallReturnsSmallCompanySizes()
    {
        CompanySize::factory()->create(['size' => 'Small Business']);
        CompanySize::factory()->create(['size' => 'Startup']);
        CompanySize::factory()->create(['size' => 'Large Corporation']);

        $smallCompanySizes = CompanySize::small()->get();

        $this->assertCount(2, $smallCompanySizes);
    }

    /** @test */
    public function scopeMediumReturnsMediumCompanySizes()
    {
        CompanySize::factory()->create(['size' => 'Medium Business']);
        CompanySize::factory()->create(['size' => 'Mid-size Company']);
        CompanySize::factory()->create(['size' => 'Small Startup']);

        $mediumCompanySizes = CompanySize::medium()->get();

        $this->assertCount(2, $mediumCompanySizes);
    }

    /** @test */
    public function scopeLargeReturnsLargeCompanySizes()
    {
        CompanySize::factory()->create(['size' => 'Large Corporation']);
        CompanySize::factory()->create(['size' => 'Enterprise']);
        CompanySize::factory()->create(['size' => 'Small Business']);

        $largeCompanySizes = CompanySize::large()->get();

        $this->assertCount(2, $largeCompanySizes);
    }
}
