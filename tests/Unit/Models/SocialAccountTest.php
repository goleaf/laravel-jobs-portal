<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SocialAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = SocialAccount::factory()->create();
        
        $this->assertInstanceOf(SocialAccount::class, $model);
        $this->assertDatabaseHas('social_accounts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new SocialAccount();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new SocialAccount();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = SocialAccount::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = SocialAccount::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('social_accounts', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = SocialAccount::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('social_accounts', [
            'id' => $modelId
        ]);
    }
}