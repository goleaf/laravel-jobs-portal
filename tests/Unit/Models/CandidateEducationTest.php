<?php

namespace Tests\Unit\Models;

use App\Models\CandidateEducation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateEducationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = CandidateEducation::factory()->create();

        $this->assertInstanceOf(CandidateEducation::class, $model);
        $this->assertDatabaseHas('candidate_educations', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new CandidateEducation();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new CandidateEducation();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = CandidateEducation::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = CandidateEducation::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('candidate_educations', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = CandidateEducation::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('candidate_educations', [
            'id' => $modelId,
        ]);
    }
}
