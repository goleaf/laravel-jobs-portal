<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PostCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected PostCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = PostCategory::factory()->create([
            'name' => 'Test Category',
            'description' => 'Test Description',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 1,
            'color' => '#ff0000',
            'icon' => 'fas fa-test',
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $fillable = [
            'name', 'description', 'is_default', 'is_active',
            'sort_order', 'color', 'icon',
        ];

        $this->assertEquals($fillable, $this->category->getFillable());
    }

    /** @test */
    public function itCastsAttributesCorrectly()
    {
        $this->assertIsBool($this->category->is_default);
        $this->assertIsBool($this->category->is_active);
        $this->assertIsInt($this->category->sort_order);
        $this->assertIsString($this->category->color);
        $this->assertIsString($this->category->icon);
    }

    /** @test */
    public function itCanScopeActiveCategories()
    {
        PostCategory::factory()->create(['is_active' => false]);

        $activeCategories = PostCategory::active()->get();

        $this->assertEquals(1, $activeCategories->count());
        $this->assertTrue($activeCategories->first()->is_active);
    }

    /** @test */
    public function itCanScopeInactiveCategories()
    {
        $inactiveCategory = PostCategory::factory()->create(['is_active' => false]);

        $inactiveCategories = PostCategory::inactive()->get();

        $this->assertEquals(1, $inactiveCategories->count());
        $this->assertFalse($inactiveCategories->first()->is_active);
    }

    /** @test */
    public function itCanScopeDefaultCategories()
    {
        $defaultCategory = PostCategory::factory()->create(['is_default' => true]);

        $defaultCategories = PostCategory::default()->get();

        $this->assertEquals(1, $defaultCategories->count());
        $this->assertTrue($defaultCategories->first()->is_default);
    }

    /** @test */
    public function itCanScopeCustomCategories()
    {
        $customCategories = PostCategory::custom()->get();

        $this->assertEquals(1, $customCategories->count());
        $this->assertFalse($customCategories->first()->is_default);
    }

    /** @test */
    public function itCanSearchCategories()
    {
        PostCategory::factory()->create(['name' => 'Technology', 'description' => 'Tech posts']);
        PostCategory::factory()->create(['name' => 'Sports', 'description' => 'Sport articles']);

        $techResults = PostCategory::search('Tech')->get();
        $testResults = PostCategory::search('Test')->get();

        $this->assertEquals(1, $techResults->count());
        $this->assertEquals(1, $testResults->count());
    }

    /** @test */
    public function itCanScopeRecentCategories()
    {
        PostCategory::factory()->create(['created_at' => now()->subDays(40)]);

        $recentCategories = PostCategory::recent(30)->get();

        $this->assertEquals(1, $recentCategories->count());
    }

    /** @test */
    public function itCanScopePopularCategories()
    {
        $post1 = Post::factory()->create(['is_active' => true]);
        $post2 = Post::factory()->create(['is_active' => true]);

        $this->category->posts()->attach([$post1->id, $post2->id]);

        $popularCategories = PostCategory::popular(5)->get();

        $this->assertGreaterThan(0, $popularCategories->count());
    }

    /** @test */
    public function itCanScopeAlphabeticalCategories()
    {
        PostCategory::factory()->create(['name' => 'Alpha Category']);
        PostCategory::factory()->create(['name' => 'Beta Category']);

        $alphabeticalCategories = PostCategory::alphabetical()->get();

        $this->assertEquals('Alpha Category', $alphabeticalCategories->first()->name);
    }

    /** @test */
    public function itCanScopeOrderedCategories()
    {
        PostCategory::factory()->create(['sort_order' => 2, 'name' => 'Second']);
        PostCategory::factory()->create(['sort_order' => 0, 'name' => 'First']);

        $orderedCategories = PostCategory::ordered()->get();

        $this->assertEquals('First', $orderedCategories->first()->name);
    }

    /** @test */
    public function itCanScopeCategoriesWithPosts()
    {
        $post = Post::factory()->create();
        $this->category->posts()->attach($post->id);

        $categoriesWithPosts = PostCategory::withPosts()->get();

        $this->assertEquals(1, $categoriesWithPosts->count());
    }

    /** @test */
    public function itCanScopeCategoriesWithoutPosts()
    {
        $categoriesWithoutPosts = PostCategory::withoutPosts()->get();

        $this->assertGreaterThan(0, $categoriesWithoutPosts->count());
    }

    /** @test */
    public function itCanScopeEmptyCategories()
    {
        $emptyCategories = PostCategory::empty()->get();

        $this->assertGreaterThan(0, $emptyCategories->count());
    }

    /** @test */
    public function itHasPostsRelationship()
    {
        $post = Post::factory()->create();
        $this->category->posts()->attach($post->id);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->category->posts);
        $this->assertEquals(1, $this->category->posts->count());
    }

    /** @test */
    public function itHasActivePostsRelationship()
    {
        $activePost = Post::factory()->create(['is_active' => true]);
        $inactivePost = Post::factory()->create(['is_active' => false]);

        $this->category->posts()->attach([$activePost->id, $inactivePost->id]);

        $this->assertEquals(1, $this->category->activePosts()->count());
    }

    /** @test */
    public function itCanCheckIfDefault()
    {
        $this->assertFalse($this->category->isDefault());

        $defaultCategory = PostCategory::factory()->create(['is_default' => true]);
        $this->assertTrue($defaultCategory->isDefault());
    }

    /** @test */
    public function itCanCheckIfCustom()
    {
        $this->assertTrue($this->category->isCustom());

        $defaultCategory = PostCategory::factory()->create(['is_default' => true]);
        $this->assertFalse($defaultCategory->isCustom());
    }

    /** @test */
    public function itCanCheckIfActive()
    {
        $this->assertTrue($this->category->isActive());

        $inactiveCategory = PostCategory::factory()->create(['is_active' => false]);
        $this->assertFalse($inactiveCategory->isActive());
    }

    /** @test */
    public function itGeneratesDisplayNameAttribute()
    {
        $displayName = $this->category->display_name;

        $this->assertEquals('Test Category', $displayName);
    }

    /** @test */
    public function itGeneratesBadgeHtmlAttribute()
    {
        $badgeHtml = $this->category->badge_html;

        $this->assertStringContainsString('badge', $badgeHtml);
        $this->assertStringContainsString('#ff0000', $badgeHtml);
        $this->assertStringContainsString('Test Category', $badgeHtml);
    }

    /** @test */
    public function itGeneratesIconHtmlAttribute()
    {
        $iconHtml = $this->category->icon_html;

        $this->assertStringContainsString('fas fa-test', $iconHtml);
    }

    /** @test */
    public function itGeneratesSlugAttribute()
    {
        $slug = $this->category->slug;

        $this->assertEquals('test-category', $slug);
    }

    /** @test */
    public function itCanCheckIfHasPosts()
    {
        $this->assertFalse($this->category->hasPosts());

        $post = Post::factory()->create();
        $this->category->posts()->attach($post->id);

        $this->assertTrue($this->category->fresh()->hasPosts());
    }

    /** @test */
    public function itCanCheckIfHasActivePosts()
    {
        $this->assertFalse($this->category->hasActivePosts());

        $post = Post::factory()->create(['is_active' => true]);
        $this->category->posts()->attach($post->id);

        $this->assertTrue($this->category->fresh()->hasActivePosts());
    }

    /** @test */
    public function itCanCheckIfHasIcon()
    {
        $this->assertTrue($this->category->hasIcon());

        $categoryWithoutIcon = PostCategory::factory()->create(['icon' => null]);
        $this->assertFalse($categoryWithoutIcon->hasIcon());
    }

    /** @test */
    public function itCanCheckIfHasColor()
    {
        $this->assertTrue($this->category->hasColor());

        $categoryWithoutColor = PostCategory::factory()->create(['color' => null]);
        $this->assertFalse($categoryWithoutColor->hasColor());
    }

    /** @test */
    public function itGeneratesStatsAttribute()
    {
        $stats = $this->category->stats;

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_posts', $stats);
        $this->assertArrayHasKey('active_posts', $stats);
        $this->assertArrayHasKey('is_popular', $stats);
        $this->assertArrayHasKey('created_days_ago', $stats);
    }

    /** @test */
    public function itCachesActiveCategories()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('post_categories.active', \Mockery::any(), \Mockery::any())
            ->andReturn(collect([$this->category]))
        ;

        $result = PostCategory::getCachedActive();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function itCachesDefaultCategories()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('post_categories.default', \Mockery::any(), \Mockery::any())
            ->andReturn(collect())
        ;

        $result = PostCategory::getCachedDefault();

        $this->assertNotNull($result);
    }

    /** @test */
    public function itCachesPopularCategories()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('post_categories.popular.10', \Mockery::any(), \Mockery::any())
            ->andReturn(collect([$this->category]))
        ;

        $result = PostCategory::getCachedPopular(10);

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function itClearsCachesWhenModelChanges()
    {
        Cache::shouldReceive('forget')->times(8);

        $this->category->clearCaches();

        $this->assertTrue(true); // If we get here, the method worked
    }

    /** @test */
    public function itUsesSoftDeletes()
    {
        $this->category->delete();

        $this->assertSoftDeleted($this->category);
        $this->assertNotNull($this->category->fresh()->deleted_at);
    }

    /** @test */
    public function itLogsActivity()
    {
        $this->category->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => PostCategory::class,
            'subject_id' => $this->category->id,
        ]);
    }
}
