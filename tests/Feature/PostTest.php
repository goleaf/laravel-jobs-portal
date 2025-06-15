<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->adminUser = User::factory()->create(['user_type' => User::ADMIN]);
    }

    /** @test */
    public function adminCanViewPostsList()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/posts')
        ;

        $response->assertStatus(200);
        $response->assertViewIs('blogs.index');
    }

    /** @test */
    public function adminCanViewCreatePostForm()
    {
        // Create a post category for the form
        PostCategory::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/posts/create')
        ;

        $response->assertStatus(200);
        $response->assertViewIs('blogs.create');
        $response->assertViewHas('blogCategories');
    }

    /** @test */
    public function adminCanCreateAPost()
    {
        $category = PostCategory::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'blog_category' => [$category->id],
            'is_published' => true,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/admin/posts', $postData)
        ;

        $response->assertRedirect('/admin/posts');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'is_published' => 1,
        ]);

        // Test that category association was created
        $post = Post::where('title', $postData['title'])->first();
        $this->assertDatabaseHas('post_assign_categories', [
            'post_id' => $post->id,
            'post_categories_id' => $category->id,
        ]);
    }

    /** @test */
    public function adminCanViewPostDetails()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/posts/{$post->id}")
        ;

        $response->assertStatus(200);
        $response->assertViewIs('blogs.show');
        $response->assertViewHas('post', $post);
    }

    /** @test */
    public function adminCanViewEditPostForm()
    {
        $post = Post::factory()->create();

        // Create categories
        $categories = PostCategory::factory()->count(3)->create();

        // Assign categories to post
        $post->postAssignCategories()->attach($categories->first()->id);

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/posts/{$post->id}/edit")
        ;

        $response->assertStatus(200);
        $response->assertViewIs('blogs.edit');
        $response->assertViewHas('post', $post);
        $response->assertViewHas('blogCategories');
        $response->assertViewHas('selectedBlogCategories');
    }

    /** @test */
    public function adminCanUpdateAPost()
    {
        $post = Post::factory()->create();
        $category = PostCategory::factory()->create();

        $updatedData = [
            'title' => 'Updated Post Title',
            'description' => $this->faker->paragraph,
            'blog_category' => [$category->id],
            'is_published' => true,
        ];

        $response = $this->actingAs($this->adminUser)
            ->put("/admin/posts/{$post->id}", $updatedData)
        ;

        $response->assertRedirect('/admin/posts');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Post Title',
            'is_published' => 1,
        ]);

        // Test that category association was updated
        $this->assertDatabaseHas('post_assign_categories', [
            'post_id' => $post->id,
            'post_categories_id' => $category->id,
        ]);
    }

    /** @test */
    public function adminCanDeleteAPost()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/posts/{$post->id}")
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function adminCanViewAllPostComments()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/post-comments')
        ;

        $response->assertStatus(200);
        $response->assertViewIs('post_comments.index');
    }

    /** @test */
    public function adminCanViewPostCommentDetails()
    {
        $post = Post::factory()->create();
        $comment = PostComment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson("/admin/post-comments/{$comment->id}")
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'post', 'name', 'email', 'comment'],
        ]);
    }
}
