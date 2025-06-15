<?php

namespace Tests\Unit\Models;

use App\Models\Noticeboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NoticeboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = Noticeboard::factory()->create();

        $this->assertInstanceOf(Noticeboard::class, $model);
        $this->assertDatabaseHas('noticeboards', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new Noticeboard();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new Noticeboard();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = Noticeboard::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Noticeboard::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('noticeboards', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = Noticeboard::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('noticeboards', [
            'id' => $modelId,
        ]);
    }
}
