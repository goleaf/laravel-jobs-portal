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
    public function it_can_be_created()
    {
        $Category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $Category);
        $this->assertModelExists($Category);
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        $Category = new Category;
        $fillable = $Category->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $Category = Category::factory()->create();
        $categoryId = $Category->id;
        $Category->delete();

        $this->assertDatabaseMissing('job_categories', [
            'id' => $categoryId,
        ]);
    }
}
