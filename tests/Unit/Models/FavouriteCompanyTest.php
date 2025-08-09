<?php

namespace Tests\Unit\Models;

use App\Models\FavouriteCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FavouriteCompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $this->markTestSkipped('FavouriteCompany depends on users/auth (removed).');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new FavouriteCompany;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new FavouriteCompany;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->markTestSkipped('FavouriteCompany depends on users/auth (removed).');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->markTestSkipped('FavouriteCompany depends on users/auth (removed).');
    }
}
