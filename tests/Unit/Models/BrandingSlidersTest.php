<?php

namespace Tests\Unit\Models;

use App\Models\BrandingSliders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BrandingSlidersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = BrandingSliders::factory()->create();

        $this->assertInstanceOf(BrandingSliders::class, $model);
        $this->assertDatabaseHas('branding_sliders', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new BrandingSliders();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new BrandingSliders();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = BrandingSliders::factory()->create();

        // Use only fillable attributes for mass assignment
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'is_active' => false,
        ];

        $model->update($updateData);

        $this->assertDatabaseHas('branding_sliders', [
            'id' => $model->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = BrandingSliders::factory()->create();
        $modelId = $model->id;

        $model->delete();

        // Check that record is soft deleted (not actually removed)
        $this->assertDatabaseHas('branding_sliders', [
            'id' => $modelId,
        ]);

        // Check that deleted_at is not null
        $this->assertNotNull($model->fresh()->deleted_at);
    }
}
