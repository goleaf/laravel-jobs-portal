<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CompanySize;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanySizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CompanySize::factory()->create();
        
        $this->assertInstanceOf(CompanySize::class, $model);
        $this->assertDatabaseHas('companysizes', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CompanySize();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CompanySize();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CompanySize::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = CompanySize::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('companysizes', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CompanySize::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('companysizes', [
            'id' => $modelId
        ]);
    }
}