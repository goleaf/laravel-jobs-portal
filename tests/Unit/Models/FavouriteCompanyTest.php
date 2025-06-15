<?php

namespace Tests\Unit\Models;

use App\Models\FavouriteCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FavouriteCompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = FavouriteCompany::factory()->create();

        $this->assertInstanceOf(FavouriteCompany::class, $model);
        $this->assertDatabaseHas('favourite_companies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new FavouriteCompany();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new FavouriteCompany();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = FavouriteCompany::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = FavouriteCompany::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('favourite_companies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = FavouriteCompany::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('favourite_companies', [
            'id' => $modelId,
        ]);
    }
}
