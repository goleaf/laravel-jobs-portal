<?php

namespace Tests\Unit\Models;

use App\Models\RequiredDegreeLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RequiredDegreeLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = RequiredDegreeLevel::factory()->create();

        $this->assertInstanceOf(RequiredDegreeLevel::class, $model);
        $this->assertDatabaseHas('required_degree_levels', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new RequiredDegreeLevel();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new RequiredDegreeLevel();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = RequiredDegreeLevel::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = RequiredDegreeLevel::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('required_degree_levels', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = RequiredDegreeLevel::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('required_degree_levels', [
            'id' => $modelId,
        ]);
    }
}
