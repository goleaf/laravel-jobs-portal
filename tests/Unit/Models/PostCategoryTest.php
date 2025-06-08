<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = PostCategory::factory()->create();
        
        $this->assertInstanceOf(PostCategory::class, $model);
        $this->assertDatabaseHas('post_categories', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new PostCategory();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new PostCategory();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = PostCategory::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = PostCategory::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('post_categories', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = PostCategory::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('postcategories', [
            'id' => $modelId
        ]);
    }
}