<?php

namespace Tests\Unit\Models;

use App\Models\Industry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class IndustryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Industry::factory()->create();

        $this->assertInstanceOf(Industry::class, $model);
        $this->assertDatabaseHas('industries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Industry;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Industry;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Industry::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Industry::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('industries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Industry::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('industries', [
            'id' => $modelId,
        ]);
    }
}
