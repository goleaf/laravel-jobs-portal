<?php

namespace Tests\Unit\Models;

use App\Models\SocialAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = SocialAccount::factory()->create();

        $this->assertInstanceOf(SocialAccount::class, $model);
        $this->assertDatabaseHas('social_accounts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new SocialAccount();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new SocialAccount();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = SocialAccount::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = SocialAccount::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('social_accounts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = SocialAccount::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('social_accounts', [
            'id' => $modelId,
        ]);
    }
}
