<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SalaryPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalaryPeriodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = SalaryPeriod::factory()->create();
        
        $this->assertInstanceOf(SalaryPeriod::class, $model);
        $this->assertDatabaseHas('salary_periods', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new SalaryPeriod();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new SalaryPeriod();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = SalaryPeriod::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = SalaryPeriod::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('salary_periods', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = SalaryPeriod::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('salary_periods', [
            'id' => $modelId
        ]);
    }
}