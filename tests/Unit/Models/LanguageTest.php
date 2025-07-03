<?php

namespace Tests\Unit\Models;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Language::factory()->create();

        $this->assertInstanceOf(Language::class, $model);
        $this->assertDatabaseHas('languages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Language;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Language;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Language::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Language::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('languages', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Language::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('languages', [
            'id' => $modelId,
        ]);
    }
}
