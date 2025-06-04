<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Inquiry::factory()->create();
        
        $this->assertInstanceOf(Inquiry::class, $model);
        $this->assertDatabaseHas('inquiries', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Inquiry();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Inquiry();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Inquiry::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Inquiry::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('inquiries', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Inquiry::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('inquiries', [
            'id' => $modelId
        ]);
    }
}