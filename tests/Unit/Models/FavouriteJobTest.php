<?php

namespace Tests\Unit\Models;

use App\Models\FavouriteJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FavouriteJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = FavouriteJob::factory()->create();

        $this->assertInstanceOf(FavouriteJob::class, $model);
        $this->assertDatabaseHas('favouritejobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new FavouriteJob();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new FavouriteJob();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = FavouriteJob::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FavouriteJob::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('favouritejobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = FavouriteJob::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('favouritejobs', [
            'id' => $modelId,
        ]);
    }
}
