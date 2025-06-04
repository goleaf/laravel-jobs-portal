<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Testimonial::factory()->create();
        
        $this->assertInstanceOf(Testimonial::class, $model);
        $this->assertDatabaseHas('testimonials', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Testimonial();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Testimonial();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Testimonial::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Testimonial::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('testimonials', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Testimonial::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('testimonials', [
            'id' => $modelId
        ]);
    }
}