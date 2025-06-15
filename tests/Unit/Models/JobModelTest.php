<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $Job = Job::factory()->create();

        $this->assertInstanceOf(Job::class, $Job);
        $this->assertModelExists($Job);
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        $Job = new Job();
        $fillable = $Job->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itCanBeSoftDeleted()
    {
        $Job = Job::factory()->create();
        $Job->delete();

        $this->assertSoftDeleted($Job);
    }
}
