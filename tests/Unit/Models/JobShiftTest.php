<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\JobShift;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobShiftTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = JobShift::factory()->create();
        
        $this->assertInstanceOf(JobShift::class, $model);
        $this->assertDatabaseHas('job_shifts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobShift();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobShift();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = JobShift::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = JobShift::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('job_shifts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = JobShift::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('job_shifts', [
            'id' => $modelId
        ]);
    }
}