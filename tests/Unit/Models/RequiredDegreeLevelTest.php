<?php

namespace Tests\Unit\Models;

use App\Models\RequiredDegreeLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RequiredDegreeLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = RequiredDegreeLevel::factory()->create();

        $this->assertInstanceOf(RequiredDegreeLevel::class, $model);
        $this->assertDatabaseHas('required_degree_levels', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new RequiredDegreeLevel;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new RequiredDegreeLevel;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = RequiredDegreeLevel::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = RequiredDegreeLevel::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('required_degree_levels', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = RequiredDegreeLevel::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('required_degree_levels', [
            'id' => $modelId,
        ]);
    }
}
