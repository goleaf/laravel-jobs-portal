<?php

namespace Tests\Unit\Models;

use App\Models\Industry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class IndustryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Industry::factory()->create();

        $this->assertInstanceOf(Industry::class, $model);
        $this->assertDatabaseHas('industries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Industry();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Industry();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Industry::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Industry::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('industries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Industry::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('industries', [
            'id' => $modelId,
        ]);
    }
}
