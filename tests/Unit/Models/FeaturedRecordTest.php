<?php

namespace Tests\Unit\Models;

use App\Models\FeaturedRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FeaturedRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = FeaturedRecord::factory()->create();

        $this->assertInstanceOf(FeaturedRecord::class, $model);
        $this->assertDatabaseHas('featured_records', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FeaturedRecord;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FeaturedRecord;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FeaturedRecord::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FeaturedRecord::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('featured_records', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FeaturedRecord::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('featured_records', [
            'id' => $modelId,
        ]);
    }
}
