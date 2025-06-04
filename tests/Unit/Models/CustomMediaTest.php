<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CustomMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomMediaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CustomMedia::factory()->create();
        
        $this->assertInstanceOf(CustomMedia::class, $model);
        $this->assertDatabaseHas('custommedias', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CustomMedia();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CustomMedia();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CustomMedia::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = CustomMedia::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('custommedias', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CustomMedia::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('custommedias', [
            'id' => $modelId
        ]);
    }
}