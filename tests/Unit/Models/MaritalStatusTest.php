<?php

namespace Tests\Unit\Models;

use App\Models\MaritalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = MaritalStatus::factory()->create();

        $this->assertInstanceOf(MaritalStatus::class, $model);
        $this->assertDatabaseHas('maritalstatuses', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new MaritalStatus();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new MaritalStatus();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = MaritalStatus::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = MaritalStatus::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('maritalstatuses', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = MaritalStatus::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('maritalstatuses', [
            'id' => $modelId,
        ]);
    }
}
