<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\EnvSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnvSettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = EnvSetting::factory()->create();
        
        $this->assertInstanceOf(EnvSetting::class, $model);
        $this->assertDatabaseHas('envsettings', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new EnvSetting();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new EnvSetting();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = EnvSetting::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = EnvSetting::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('envsettings', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = EnvSetting::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('envsettings', [
            'id' => $modelId
        ]);
    }
}