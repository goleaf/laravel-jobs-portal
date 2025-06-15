<?php

namespace Tests\Unit\Models;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Skill::factory()->create();

        $this->assertInstanceOf(Skill::class, $model);
        $this->assertDatabaseHas('skills', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Skill();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Skill();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Skill::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Skill::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('skills', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Skill::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('skills', [
            'id' => $modelId,
        ]);
    }
}
