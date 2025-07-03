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
    public function it_can_be_created()
    {
        $model = FAQ::factory()->create();

        $this->assertInstanceOf(FAQ::class, $model);
        $this->assertDatabaseHas('faqs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FAQ;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FAQ;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FAQ::factory()->create();

        // Update with only database fields, not computed properties
        $updateData = [
            'title' => 'Updated FAQ Title',
            'description' => 'Updated FAQ Description',
            'category' => 'updated',
            'is_active' => true,
        ];

        $model->update($updateData);

        $this->assertDatabaseHas('faqs', [
            'id' => $model->id,
            'title' => 'Updated FAQ Title',
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FAQ::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('faqs', [
            'id' => $modelId,
        ]);
    }
}
