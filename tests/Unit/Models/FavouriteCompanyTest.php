<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\FavouriteCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavouriteCompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = FavouriteCompany::factory()->create();
        
        $this->assertInstanceOf(FavouriteCompany::class, $model);
        $this->assertDatabaseHas('favouritecompanies', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FavouriteCompany();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FavouriteCompany();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = FavouriteCompany::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = FavouriteCompany::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('favouritecompanies', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = FavouriteCompany::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('favouritecompanies', [
            'id' => $modelId
        ]);
    }
}