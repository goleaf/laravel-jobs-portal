<?php

namespace Tests\Unit\Models;

use App\Models\JobStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobStageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = JobStage::factory()->create();

        $this->assertInstanceOf(JobStage::class, $model);
        $this->assertDatabaseHas('jobstages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new JobStage();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new JobStage();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = JobStage::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = JobStage::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('jobstages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = JobStage::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('jobstages', [
            'id' => $modelId,
        ]);
    }
}
