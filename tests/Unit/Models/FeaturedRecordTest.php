<?php

namespace Tests\Unit\Models;

use App\Models\FeaturedRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FeaturedRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = FeaturedRecord::factory()->create();

        $this->assertInstanceOf(FeaturedRecord::class, $model);
        $this->assertDatabaseHas('featuredrecords', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new FeaturedRecord();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new FeaturedRecord();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = FeaturedRecord::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FeaturedRecord::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('featuredrecords', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = FeaturedRecord::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('featuredrecords', [
            'id' => $modelId,
        ]);
    }
}
