<?php

namespace Tests\Unit\Models;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Subscription::factory()->create();

        $this->assertInstanceOf(Subscription::class, $model);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Subscription();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Subscription();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Subscription::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Subscription::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Subscription::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $modelId,
        ]);
    }
}
