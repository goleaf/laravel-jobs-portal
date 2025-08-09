<?php

namespace Tests\Unit\Models;

use App\Models\FavouriteJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FavouriteJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $this->markTestSkipped('FavouriteJob depends on users/auth (removed).');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FavouriteJob;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FavouriteJob;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->markTestSkipped('FavouriteJob depends on users/auth (removed).');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->markTestSkipped('FavouriteJob depends on users/auth (removed).');
    }
}
