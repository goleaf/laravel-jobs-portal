<?php

namespace Tests\Unit\Models;

use App\Models\JobApplicationSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApplicationScheduleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = JobApplicationSchedule::factory()->create();

        $this->assertInstanceOf(JobApplicationSchedule::class, $model);
        $this->assertDatabaseHas('job_application_schedules', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new JobApplicationSchedule();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new JobApplicationSchedule();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = JobApplicationSchedule::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = JobApplicationSchedule::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('job_application_schedules', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = JobApplicationSchedule::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('job_application_schedules', [
            'id' => $modelId,
        ]);
    }
}
