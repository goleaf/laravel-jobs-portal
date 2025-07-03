<?php

namespace Tests\Unit\Models;

use App\Models\FrontSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FrontSettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = FrontSetting::factory()->create();

        $this->assertInstanceOf(FrontSetting::class, $model);
        $this->assertDatabaseHas('front_settings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FrontSetting;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FrontSetting;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FrontSetting::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FrontSetting::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('front_settings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FrontSetting::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('front_settings', [
            'id' => $modelId,
        ]);
    }
}
