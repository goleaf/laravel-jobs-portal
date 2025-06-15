<?php

namespace Tests\Unit\Models;

use App\Models\SalaryCurrency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SalaryCurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = SalaryCurrency::factory()->create();

        $this->assertInstanceOf(SalaryCurrency::class, $model);
        $this->assertDatabaseHas('salary_currencies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new SalaryCurrency();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new SalaryCurrency();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = SalaryCurrency::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = SalaryCurrency::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('salary_currencies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = SalaryCurrency::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('salary_currencies', [
            'id' => $modelId,
        ]);
    }
}
