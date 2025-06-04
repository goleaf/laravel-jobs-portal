<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FAQTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = FAQ::factory()->create();
        
        $this->assertInstanceOf(FAQ::class, $model);
        $this->assertDatabaseHas('faqs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FAQ();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FAQ();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FAQ::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = FAQ::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('faqs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FAQ::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('faqs', [
            'id' => $modelId
        ]);
    }
}