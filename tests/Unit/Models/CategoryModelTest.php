<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $Category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $Category);
        $this->assertModelExists($Category);
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        $Category = new Category();
        $fillable = $Category->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $Category = Category::factory()->create();
        $categoryId = $Category->id;
        $Category->delete();

        $this->assertDatabaseMissing('job_categories', [
            'id' => $categoryId,
        ]);
    }
}
