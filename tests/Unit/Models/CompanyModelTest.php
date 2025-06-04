<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $Company = Company::factory()->create();
        
        $this->assertInstanceOf(Company::class, $Company);
        $this->assertModelExists($Company);
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        $Company = new Company();
        $fillable = $Company->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $Company = Company::factory()->create();
        $Company->delete();
        
        $this->assertSoftDeleted($Company);
    }
}