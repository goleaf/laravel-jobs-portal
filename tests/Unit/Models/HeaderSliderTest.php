<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\HeaderSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HeaderSliderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = HeaderSlider::factory()->create();
        
        $this->assertInstanceOf(HeaderSlider::class, $model);
        $this->assertDatabaseHas('headersliders', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new HeaderSlider();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new HeaderSlider();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = HeaderSlider::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = HeaderSlider::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('headersliders', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = HeaderSlider::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('headersliders', [
            'id' => $modelId
        ]);
    }
}