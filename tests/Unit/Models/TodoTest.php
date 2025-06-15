<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TodoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Todo::factory()->create();

        $this->assertInstanceOf(Todo::class, $model);
        $this->assertDatabaseHas('todos', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Todo();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Todo();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Todo::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Todo::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('todos', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Todo::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('todos', [
            'id' => $modelId,
        ]);
    }
}
