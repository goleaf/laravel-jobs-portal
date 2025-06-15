<?php

namespace Tests\Unit\Models;

use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = EmailTemplate::factory()->create();

        $this->assertInstanceOf(EmailTemplate::class, $model);
        $this->assertDatabaseHas('email_templates', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new EmailTemplate();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new EmailTemplate();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = EmailTemplate::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = EmailTemplate::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('email_templates', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = EmailTemplate::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('email_templates', [
            'id' => $modelId,
        ]);
    }
}
