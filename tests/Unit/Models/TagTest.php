<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Tag::factory()->create();
        
        $this->assertInstanceOf(Tag::class, $model);
        $this->assertDatabaseHas('tags', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Tag();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Tag();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Tag::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Tag::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('tags', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Tag::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('tags', [
            'id' => $modelId
        ]);
    }
}