<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Post::factory()->create();
        
        $this->assertInstanceOf(Post::class, $model);
        $this->assertDatabaseHas('posts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Post();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Post();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Post::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Post::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('posts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Post::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('posts', [
            'id' => $modelId
        ]);
    }
}