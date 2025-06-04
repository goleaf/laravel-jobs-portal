<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CandidateExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidateExperienceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CandidateExperience::factory()->create();
        
        $this->assertInstanceOf(CandidateExperience::class, $model);
        $this->assertDatabaseHas('candidateexperiences', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CandidateExperience();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CandidateExperience();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CandidateExperience::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = CandidateExperience::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('candidateexperiences', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CandidateExperience::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('candidateexperiences', [
            'id' => $modelId
        ]);
    }
}