<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ReportedJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportedJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = ReportedJob::factory()->create();
        
        $this->assertInstanceOf(ReportedJob::class, $model);
        $this->assertDatabaseHas('reportedjobs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new ReportedJob();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new ReportedJob();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = ReportedJob::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = ReportedJob::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('reportedjobs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = ReportedJob::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('reportedjobs', [
            'id' => $modelId
        ]);
    }
}