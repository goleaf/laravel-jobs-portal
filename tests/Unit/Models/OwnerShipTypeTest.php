<?php

namespace Tests\Unit\Models;

use App\Models\OwnerShipType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class OwnerShipTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = OwnerShipType::factory()->create();

        $this->assertInstanceOf(OwnerShipType::class, $model);
        $this->assertDatabaseHas('ownership_types', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new OwnerShipType();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new OwnerShipType();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = OwnerShipType::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = OwnerShipType::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('ownership_types', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = OwnerShipType::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('ownership_types', [
            'id' => $modelId,
        ]);
    }
}
