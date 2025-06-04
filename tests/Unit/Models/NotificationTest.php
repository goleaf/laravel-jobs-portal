<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Notification::factory()->create();
        
        $this->assertInstanceOf(Notification::class, $model);
        $this->assertDatabaseHas('notifications', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Notification();
        $fillable = $model->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Notification();
        $casts = $model->getCasts();
        
        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Notification::factory()->create();
        $originalData = $model->toArray();
        
        // Update with factory data
        $newData = Notification::factory()->make()->toArray();
        $model->update($newData);
        
        $this->assertDatabaseHas('notifications', [
            'id' => $model->id
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Notification::factory()->create();
        $modelId = $model->id;
        
        $model->delete();
        
        $this->assertDatabaseMissing('notifications', [
            'id' => $modelId
        ]);
    }
}