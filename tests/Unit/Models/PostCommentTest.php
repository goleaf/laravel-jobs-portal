<?php

namespace Tests\Unit\Models;

use App\Models\PostComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PostCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = PostComment::factory()->create();

        $this->assertInstanceOf(PostComment::class, $model);
        $this->assertDatabaseHas('postcomments', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new PostComment;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new PostComment;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = PostComment::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = PostComment::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('postcomments', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = PostComment::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('postcomments', [
            'id' => $modelId,
        ]);
    }
}
