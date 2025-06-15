<?php

namespace Tests\Unit\Models;

use App\Models\ReportedToCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ReportedToCompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = ReportedToCompany::factory()->create();

        $this->assertInstanceOf(ReportedToCompany::class, $model);
        $this->assertDatabaseHas('reported_to_companies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new ReportedToCompany();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new ReportedToCompany();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = ReportedToCompany::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = ReportedToCompany::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('reported_to_companies', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = ReportedToCompany::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('reported_to_companies', [
            'id' => $modelId,
        ]);
    }
}
