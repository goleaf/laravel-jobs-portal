<?php

namespace Tests\Unit\Models;

use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PostCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = PostCategory::factory()->create();

        $this->assertInstanceOf(PostCategory::class, $model);
        $this->assertDatabaseHas('post_categories', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new PostCategory();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new PostCategory();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = PostCategory::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = PostCategory::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('post_categories', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = PostCategory::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('post_categories', [
            'id' => $modelId,
        ]);
    }
}
