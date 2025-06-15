<?php

namespace Tests\Unit\Models;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Language::factory()->create();

        $this->assertInstanceOf(Language::class, $model);
        $this->assertDatabaseHas('languages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Language();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Language();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Language::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Language::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('languages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Language::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('languages', [
            'id' => $modelId,
        ]);
    }
}
