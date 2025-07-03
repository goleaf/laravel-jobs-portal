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
    public function it_can_be_created()
    {
        $model = Plan::factory()->create();

        $this->assertInstanceOf(Plan::class, $model);
        $this->assertDatabaseHas('plans', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Plan;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Plan;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = Plan::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('plans', [
            'id' => $modelId,
        ]);
    }
}
