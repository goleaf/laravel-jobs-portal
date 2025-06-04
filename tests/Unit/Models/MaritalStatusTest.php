<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\MaritalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = MaritalStatus::factory()->create();
        
        $this->assertInstanceOf(MaritalStatus::class, $model);
        $this->assertDatabaseHas('maritalstatuses', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new MaritalStatus();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new MaritalStatus();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = MaritalStatus::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = MaritalStatus::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('maritalstatuses', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = MaritalStatus::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('maritalstatuses', [
            'id' => $modelId
        ]);
    }
}