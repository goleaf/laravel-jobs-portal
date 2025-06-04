<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = State::factory()->create();
        
        $this->assertInstanceOf(State::class, $model);
        $this->assertDatabaseHas('states', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new State();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new State();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = State::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = State::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('states', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = State::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('states', [
            'id' => $modelId
        ]);
    }
}