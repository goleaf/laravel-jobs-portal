<?php

namespace Tests\Unit\Models;

use App\Models\ImageSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ImageSliderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = ImageSlider::factory()->create();

        $this->assertInstanceOf(ImageSlider::class, $model);
        $this->assertDatabaseHas('imagesliders', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new ImageSlider();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new ImageSlider();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = ImageSlider::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = ImageSlider::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('imagesliders', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = ImageSlider::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('imagesliders', [
            'id' => $modelId,
        ]);
    }
}
