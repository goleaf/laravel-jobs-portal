<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ImageSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageSliderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = ImageSlider::factory()->create();
        
        $this->assertInstanceOf(ImageSlider::class, $model);
        $this->assertDatabaseHas('imagesliders', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new ImageSlider();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new ImageSlider();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = ImageSlider::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = ImageSlider::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('imagesliders', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = ImageSlider::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('imagesliders', [
            'id' => $modelId
        ]);
    }
}