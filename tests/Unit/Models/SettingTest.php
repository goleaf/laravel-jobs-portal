<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Setting::factory()->create();

        $this->assertInstanceOf(Setting::class, $model);
        $this->assertDatabaseHas('settings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Setting;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Setting;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Setting::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Setting::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('settings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Setting::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('settings', [
            'id' => $modelId,
        ]);
    }
}
