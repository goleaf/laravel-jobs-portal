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
    public function it_can_be_created()
    {
        $model = JobCategory::factory()->create();

        $this->assertInstanceOf(JobCategory::class, $model);
        $this->assertDatabaseHas('job_categories', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobCategory;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobCategory;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = JobCategory::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('job_categories', [
            'id' => $modelId,
        ]);
    }
}
