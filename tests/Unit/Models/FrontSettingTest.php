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
    public function itCanBeCreated()
    {
        $model = FrontSetting::factory()->create();

        $this->assertInstanceOf(FrontSetting::class, $model);
        $this->assertDatabaseHas('frontsettings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new FrontSetting();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new FrontSetting();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = FrontSetting::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FrontSetting::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('frontsettings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = FrontSetting::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('frontsettings', [
            'id' => $modelId,
        ]);
    }
}
