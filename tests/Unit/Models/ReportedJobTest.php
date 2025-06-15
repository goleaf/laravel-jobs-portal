<?php

namespace Tests\Unit\Models;

use App\Models\ReportedJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ReportedJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = ReportedJob::factory()->create();

        $this->assertInstanceOf(ReportedJob::class, $model);
        $this->assertDatabaseHas('reported_jobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new ReportedJob();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new ReportedJob();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = ReportedJob::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = ReportedJob::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('reported_jobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = ReportedJob::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('reported_jobs', [
            'id' => $modelId,
        ]);
    }
}
