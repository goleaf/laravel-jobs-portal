<?php

namespace Tests\Unit\Models;

use App\Models\NewsLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NewsLetterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = NewsLetter::factory()->create();

        $this->assertInstanceOf(NewsLetter::class, $model);
        $this->assertDatabaseHas('newsletters', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new NewsLetter();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new NewsLetter();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = NewsLetter::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = NewsLetter::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('newsletters', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = NewsLetter::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('newsletters', [
            'id' => $modelId,
        ]);
    }
}
