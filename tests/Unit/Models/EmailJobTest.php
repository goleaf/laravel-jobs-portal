<?php

namespace Tests\Unit\Models;

use App\Models\EmailJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EmailJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = EmailJob::factory()->create();

        $this->assertInstanceOf(EmailJob::class, $model);
        $this->assertDatabaseHas('emailjobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new EmailJob();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new EmailJob();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = EmailJob::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = EmailJob::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('emailjobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = EmailJob::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('emailjobs', [
            'id' => $modelId,
        ]);
    }
}
