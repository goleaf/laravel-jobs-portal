<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $Application = Application::factory()->create();
        
        $this->assertInstanceOf(Application::class, $Application);
        $this->assertModelExists($Application);
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        $Application = new Application();
        $fillable = $Application->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $Application = Application::factory()->create();
        $Application->delete();
        
        $this->assertSoftDeleted($Application);
    }
}