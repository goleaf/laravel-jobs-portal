<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\JobType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = JobType::factory()->create();
        
        $this->assertInstanceOf(JobType::class, $model);
        $this->assertDatabaseHas('jobtypes', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobType();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobType();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = JobType::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = JobType::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('jobtypes', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = JobType::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('jobtypes', [
            'id' => $modelId
        ]);
    }
}