<?php

namespace Tests\Unit\Models;

use App\Models\NotificationSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NotificationSettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = NotificationSetting::factory()->create();

        $this->assertInstanceOf(NotificationSetting::class, $model);
        $this->assertDatabaseHas('notificationsettings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new NotificationSetting();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new NotificationSetting();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = NotificationSetting::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = NotificationSetting::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('notificationsettings', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = NotificationSetting::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('notificationsettings', [
            'id' => $modelId,
        ]);
    }
}
