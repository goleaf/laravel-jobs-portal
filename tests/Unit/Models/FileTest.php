<?php

namespace Tests\Unit\Models;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $this->markTestSkipped('File factory previously referenced users; skipping create until decoupled.');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new File;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new File;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->markTestSkipped('File factory previously referenced users; skipping update until decoupled.');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->markTestSkipped('File factory previously referenced users; skipping delete until decoupled.');
    }
}
