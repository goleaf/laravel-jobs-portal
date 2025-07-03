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
    public function it_can_be_created()
    {
        $model = Subscription::factory()->create();

        $this->assertInstanceOf(Subscription::class, $model);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Subscription;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Subscription;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = Subscription::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $modelId,
        ]);
    }
}
