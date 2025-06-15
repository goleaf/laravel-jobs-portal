<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Plan::factory()->create();

        $this->assertInstanceOf(Plan::class, $model);
        $this->assertDatabaseHas('plans', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Plan();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Plan();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Plan::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Plan::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('plans', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Plan::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('plans', [
            'id' => $modelId,
        ]);
    }
}
