<?php

namespace Tests\Unit\Models;

use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = JobCategory::factory()->create();

        $this->assertInstanceOf(JobCategory::class, $model);
        $this->assertDatabaseHas('job_categories', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new JobCategory();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new JobCategory();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = JobCategory::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = JobCategory::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('job_categories', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = JobCategory::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('job_categories', [
            'id' => $modelId,
        ]);
    }
}
