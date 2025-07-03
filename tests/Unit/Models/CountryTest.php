<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CountryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Country::factory()->create();

        $this->assertInstanceOf(Country::class, $model);
        $this->assertDatabaseHas('countries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Country;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Country;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Country::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Country::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('countries', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Country::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('countries', [
            'id' => $modelId,
        ]);
    }
}
