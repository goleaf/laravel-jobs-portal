<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $Company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $Company);
        $this->assertModelExists($Company);
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        $Company = new Company();
        $fillable = $Company->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itCanBeSoftDeleted()
    {
        $Company = Company::factory()->create();
        $Company->delete();

        $this->assertSoftDeleted($Company);
    }
}
