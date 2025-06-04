<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\NewsLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsLetterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = NewsLetter::factory()->create();
        
        $this->assertInstanceOf(NewsLetter::class, $model);
        $this->assertDatabaseHas('newsletters', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new NewsLetter();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new NewsLetter();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = NewsLetter::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = NewsLetter::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('newsletters', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = NewsLetter::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('newsletters', [
            'id' => $modelId
        ]);
    }
}