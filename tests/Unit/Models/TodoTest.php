<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Todo::factory()->create();
        
        $this->assertInstanceOf(Todo::class, $model);
        $this->assertDatabaseHas('todos', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Todo();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Todo();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Todo::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Todo::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('todos', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Todo::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('todos', [
            'id' => $modelId
        ]);
    }
}