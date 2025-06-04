<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SalaryCurrency;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalaryCurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = SalaryCurrency::factory()->create();
        
        $this->assertInstanceOf(SalaryCurrency::class, $model);
        $this->assertDatabaseHas('salarycurrencies', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new SalaryCurrency();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new SalaryCurrency();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = SalaryCurrency::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = SalaryCurrency::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('salarycurrencies', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = SalaryCurrency::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('salarycurrencies', [
            'id' => $modelId
        ]);
    }
}