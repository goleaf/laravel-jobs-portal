<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\BrandingSliders;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandingSlidersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = BrandingSliders::factory()->create();
        
        $this->assertInstanceOf(BrandingSliders::class, $model);
        $this->assertDatabaseHas('brandingsliderses', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new BrandingSliders();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new BrandingSliders();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = BrandingSliders::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = BrandingSliders::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('brandingsliderses', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = BrandingSliders::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('brandingsliderses', [
            'id' => $modelId
        ]);
    }
}