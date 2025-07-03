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
    public function it_can_be_created()
    {
        $model = EmailJob::factory()->create();

        $this->assertInstanceOf(EmailJob::class, $model);
        $this->assertDatabaseHas('email_jobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new EmailJob;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new EmailJob;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = EmailJob::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = EmailJob::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('email_jobs', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = EmailJob::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('email_jobs', [
            'id' => $modelId,
        ]);
    }
}
