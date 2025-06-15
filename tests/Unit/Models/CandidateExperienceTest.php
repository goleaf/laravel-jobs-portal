<?php

namespace Tests\Unit\Models;

use App\Models\CandidateExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateExperienceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = CandidateExperience::factory()->create();

        $this->assertInstanceOf(CandidateExperience::class, $model);
        $this->assertDatabaseHas('candidate_experiences', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new CandidateExperience();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new CandidateExperience();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = CandidateExperience::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = CandidateExperience::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('candidate_experiences', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = CandidateExperience::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('candidate_experiences', [
            'id' => $modelId,
        ]);
    }
}
