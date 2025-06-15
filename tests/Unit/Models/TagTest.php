<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Tag::factory()->create();

        $this->assertInstanceOf(Tag::class, $model);
        $this->assertDatabaseHas('tags', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Tag();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Tag();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Tag::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Tag::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('tags', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Tag::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('tags', [
            'id' => $modelId,
        ]);
    }
}
