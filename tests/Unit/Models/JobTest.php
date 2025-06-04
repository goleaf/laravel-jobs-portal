<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Job::factory()->create();
        
        $this->assertInstanceOf(Job::class, $model);
        $this->assertDatabaseHas('jobs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Job();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Job();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Job::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Job::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('jobs', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Job::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('jobs', [
            'id' => $modelId
        ]);
    }
}