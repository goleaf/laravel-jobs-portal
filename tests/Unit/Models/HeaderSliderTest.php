<?php

namespace Tests\Unit\Models;

use App\Models\HeaderSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HeaderSliderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = HeaderSlider::factory()->create();

        $this->assertInstanceOf(HeaderSlider::class, $model);
        $this->assertDatabaseHas('headersliders', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new HeaderSlider();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new HeaderSlider();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = HeaderSlider::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = HeaderSlider::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('headersliders', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = HeaderSlider::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('headersliders', [
            'id' => $modelId,
        ]);
    }
}
