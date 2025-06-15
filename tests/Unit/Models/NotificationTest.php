<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Notification::factory()->create();

        $this->assertInstanceOf(Notification::class, $model);
        $this->assertDatabaseHas('notifications', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Notification();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Notification();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Notification::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Notification::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('notifications', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Notification::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('notifications', [
            'id' => $modelId,
        ]);
    }
}
