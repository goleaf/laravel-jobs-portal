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
    public function it_can_be_created()
    {
        $model = EmailTemplate::factory()->create();

        $this->assertInstanceOf(EmailTemplate::class, $model);
        $this->assertDatabaseHas('email_templates', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new EmailTemplate;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new EmailTemplate;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = EmailTemplate::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertSoftDeleted('email_templates', [
            'id' => $modelId,
        ]);
    }
}
