<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CmsServices;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CmsServicesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CmsServices::factory()->create();
        
        $this->assertInstanceOf(CmsServices::class, $model);
        $this->assertDatabaseHas('cms_services', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CmsServices();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CmsServices();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CmsServices::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = CmsServices::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('cms_services', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CmsServices::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        // With SoftDeletes, the record still exists but with deleted_at timestamp
        $this->assertSoftDeleted('cms_services', [
            'id' => $modelId
        ]);
    }
}