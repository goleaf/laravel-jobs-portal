<?php

namespace Tests\Unit\Models;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Post::factory()->create();

        $this->assertInstanceOf(Post::class, $model);
        $this->assertDatabaseHas('posts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Post();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Post();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Post::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Post::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('posts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Post::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('posts', [
            'id' => $modelId,
        ]);
    }
}
