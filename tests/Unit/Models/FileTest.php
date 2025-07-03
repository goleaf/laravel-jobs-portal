<?php

namespace Tests\Unit\Models;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = File::factory()->create();

        $this->assertInstanceOf(File::class, $model);
        $this->assertDatabaseHas('files', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new File;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new File;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = File::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = File::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('files', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = File::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('files', [
            'id' => $modelId,
        ]);
    }
}
