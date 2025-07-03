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
    public function it_can_be_created()
    {
        $model = Noticeboard::factory()->create();

        $this->assertInstanceOf(Noticeboard::class, $model);
        $this->assertDatabaseHas('noticeboards', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Noticeboard;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Noticeboard;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = Noticeboard::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('noticeboards', [
            'id' => $modelId,
        ]);
    }
}
