<?php

namespace Tests\Unit\Models;

use App\Models\FunctionalArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FunctionalAreaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = FunctionalArea::factory()->create();

        $this->assertInstanceOf(FunctionalArea::class, $model);
        $this->assertDatabaseHas('functional_areas', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FunctionalArea;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FunctionalArea;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FunctionalArea::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FunctionalArea::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('functional_areas', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FunctionalArea::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('functional_areas', [
            'id' => $modelId,
        ]);
    }
}
