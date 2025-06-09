<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\PostCategory
 *
 * @mixin \Eloquen
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PostCategory whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $postAssignCategories
 * @property-read int|null $post_assign_categories_count
 */

    /**
     * Scope a query to only include featured records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("is_featured", true);
    }




    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
        protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'is_default' => 'boolean',
    
        ];
    }


    /**
     * Scope for active post categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive post categories.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default post categories.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom post categories.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for searching post categories.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent post categories.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old post categories.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for categories with posts.
     */
    public function scopeWithPosts($query)
    {
        return $query->has('posts');
    }

    /**
     * Scope for categories without posts.
     */
    public function scopeWithoutPosts($query)
    {
        return $query->doesntHave('posts');
    }

    /**
     * Scope for categories with published posts.
     */
    public function scopeWithPublishedPosts($query)
    {
        return $query->whereHas('posts', function ($query) {
            $query->where('is_published', true)
                  ->where('published_at', '<=', now());
        });
    }

    /**
     * Scope for popular categories (with most posts).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for categories by slug.
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function postAssignCategories(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_assigned_categories', 'post_categories_id');
    }
}
