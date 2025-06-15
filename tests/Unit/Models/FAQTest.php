<?php

namespace Tests\Unit\Models;

use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FAQTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = FAQ::factory()->create();

        $this->assertInstanceOf(FAQ::class, $model);
        $this->assertDatabaseHas('faqs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new FAQ();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new FAQ();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = FAQ::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FAQ::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('faqs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = FAQ::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('faqs', [
            'id' => $modelId,
        ]);
    }
}
