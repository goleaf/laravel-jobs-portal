<?php

namespace Tests\Unit\Models;

use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Explicitly disable foreign key constraints for this test
        if (config('database.default') === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys=OFF');
        }
    }

    /** @test */
    public function it_can_be_created()
    {
        $this->markTestSkipped('JobApplication depends on candidates/users (removed).');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobApplication;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobApplication;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->markTestSkipped('JobApplication depends on candidates/users (removed).');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->markTestSkipped('JobApplication depends on candidates/users (removed).');
    }
}
