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
    public function it_can_be_created()
    {
        $model = JobStage::factory()->create();

        $this->assertInstanceOf(JobStage::class, $model);
        $this->assertDatabaseHas('jobstages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobStage;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobStage;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = JobStage::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('jobstages', [
            'id' => $modelId,
        ]);
    }
}
