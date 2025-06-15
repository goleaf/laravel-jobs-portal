<?php

namespace Tests\Unit\Models;

use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class InquiryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Inquiry::factory()->create();

        $this->assertInstanceOf(Inquiry::class, $model);
        $this->assertDatabaseHas('inquiries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Inquiry();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Inquiry();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Inquiry::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Inquiry::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('inquiries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Inquiry::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('inquiries', [
            'id' => $modelId,
        ]);
    }
}
