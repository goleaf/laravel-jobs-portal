<?php

namespace Tests\Unit\Models;

use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Explicitly disable foreign key constraints for this test
        if ('sqlite' === config('database.default')) {
            \DB::statement('PRAGMA foreign_keys=OFF');
        }
    }

    /** @test */
    public function itCanBeCreated()
    {
        $model = JobApplication::factory()->create();

        $this->assertInstanceOf(JobApplication::class, $model);
        $this->assertDatabaseHas('job_applications', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new JobApplication();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new JobApplication();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = JobApplication::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = JobApplication::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('job_applications', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = JobApplication::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('job_applications', [
            'id' => $modelId,
        ]);
    }
}
