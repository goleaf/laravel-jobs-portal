<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $Job = Job::factory()->create();
        
        $this->assertInstanceOf(Job::class, $Job);
        $this->assertModelExists($Job);
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        $Job = new Job();
        $fillable = $Job->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $Job = Job::factory()->create();
        $Job->delete();
        
        $this->assertSoftDeleted($Job);
    }
}