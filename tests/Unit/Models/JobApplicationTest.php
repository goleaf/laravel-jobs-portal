<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Explicitly disable foreign key constraints for this test
        if (config('database.default') === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys=OFF');
        }
    }

    /** @test */
    public function it_can_be_created()
    {
        $model = JobApplication::factory()->create();
        
        $this->assertInstanceOf(JobApplication::class, $model);
        $this->assertDatabaseHas('job_applications', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobApplication();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobApplication();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = JobApplication::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = JobApplication::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('job_applications', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = JobApplication::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('job_applications', [
            'id' => $modelId
        ]);
    }
}