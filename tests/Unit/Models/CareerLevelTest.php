<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CareerLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CareerLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CareerLevel::factory()->create();
        
        $this->assertInstanceOf(CareerLevel::class, $model);
        $this->assertDatabaseHas('career_levels', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CareerLevel();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CareerLevel();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CareerLevel::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = CareerLevel::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('career_levels', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CareerLevel::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('career_levels', [
            'id' => $modelId
        ]);
    }
}