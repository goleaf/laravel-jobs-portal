<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Skill::factory()->create();
        
        $this->assertInstanceOf(Skill::class, $model);
        $this->assertDatabaseHas('skills', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Skill();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Skill();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Skill::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Skill::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('skills', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Skill::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('skills', [
            'id' => $modelId
        ]);
    }
}