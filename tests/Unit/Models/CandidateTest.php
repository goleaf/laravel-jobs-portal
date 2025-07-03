<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Candidate::factory()->create();

        $this->assertInstanceOf(Candidate::class, $model);
        $this->assertDatabaseHas('candidates', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Candidate;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Candidate;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Candidate::factory()->create();

        // Update with only fillable attributes
        $fillableData = [
            'father_name' => 'Updated Father Name',
            'nationality' => 'Updated Nationality',
            'experience' => 5,
            'current_salary' => 60000,
            'expected_salary' => 70000,
            'immediate_available' => 1,
        ];

        $model->update($fillableData);

        $this->assertDatabaseHas('candidates', [
            'id' => $model->id,
            'father_name' => 'Updated Father Name',
            'nationality' => 'Updated Nationality',
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Candidate::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('candidates', [
            'id' => $modelId,
        ]);
    }
}
