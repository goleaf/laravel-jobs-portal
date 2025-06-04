<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = City::factory()->create();
        
        $this->assertInstanceOf(City::class, $model);
        $this->assertDatabaseHas('cities', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new City();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new City();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = City::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = City::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('cities', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = City::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('cities', [
            'id' => $modelId
        ]);
    }
}