<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\Models\Media;

/**
 * Post Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property null|string $content
 * @property null|string $excerpt
 * @property null|string $slug
 * @property int $created_by
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_published
 * @property bool $is_default
 * @property null|Carbon $published_at
 * @property null|int $views_count
 * @property null|int $likes_count
 * @property null|int $comments_count
 * @property null|array $meta_data
 * @property null|string $meta_title
 * @property null|string $meta_description
 * @property null|string $meta_keywords
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User $user
 * @property Collection|PostCategory[] $postAssignCategories
 * @property Collection|PostComment[] $comments
 * @property Collection|Media[] $media
 * @property string $blog_image_url
 * @property string $display_title
 * @property string $reading_time
 * @property string $status_label
 * @property string $formatted_published_date
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder published()
 * @method static \Illuminate\Database\Eloquent\Builder unpublished()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder notFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(int $categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder byAuthor(int $authorId)
 * @method static \Illuminate\Database\Eloquent\Builder withFeaturedImages()
 * @method static \Illuminate\Database\Eloquent\Builder withoutFeaturedImages()
 * @method static \Illuminate\Database\Eloquent\Builder withComments()
 * @method static \Illuminate\Database\Eloquent\Builder withoutComments()
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder trending(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder latest(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder mostViewed(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder mostLiked(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder mostCommented(int $limit = 10)
 *
 * @mixin \Eloquent
 */
class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Media collection path constant.
     */
    public const PATH = 'posts';

    /**
     * Validation rules for creating posts.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'title' => 'required|string|max:255|unique:posts,title',
        'description' => 'required|string',
        'content' => 'nullable|string',
        'excerpt' => 'nullable|string|max:500',
        'slug' => 'nullable|string|max:255|unique:posts,slug',
        'created_by' => 'required|integer|exists:users,id',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'is_default' => 'boolean',
        'published_at' => 'nullable|date',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'meta_keywords' => 'nullable|string|max:255',
        'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'excerpt',
        'slug',
        'created_by',
        'is_active',
        'is_featured',
        'is_published',
        'is_default',
        'published_at',
        'views_count',
        'likes_count',
        'comments_count',
        'meta_data',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['blog_image_url'];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'description',
                'content',
                'slug',
                'is_active',
                'is_featured',
                'is_published',
                'published_at',
                'meta_title',
                'meta_description',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for posts.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'title' => 'required|string|max:255|unique:posts,title,'.$id,
            'description' => 'required|string',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|unique:posts,slug,'.$id,
            'created_by' => 'required|integer|exists:users,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'is_default' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the categories assigned to the post.
     */
    public function postAssignCategories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'post_assigned_categories', 'post_id', 'post_categories_id');
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope for active posts.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive posts.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for published posts.
     *
     * @param  mixed  $query
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for unpublished posts.
     *
     * @param  mixed  $query
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false)
            ->orWhere('published_at', '>', now());
    }

    /**
     * Scope for featured posts.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured posts.
     *
     * @param  mixed  $query
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching posts.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%')
                ->orWhere('content', 'like', '%'.$term.'%')
                ->orWhere('excerpt', 'like', '%'.$term.'%');
        });
    }

    /**
     * Scope for recent posts.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old posts.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope for posts by category.
     *
     * @param  mixed  $query
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->whereHas('postAssignCategories', function ($q) use ($categoryId) {
            $q->where('post_categories_id', $categoryId);
        });
    }

    /**
     * Scope for posts by author.
     *
     * @param  mixed  $query
     */
    public function scopeByAuthor($query, int $authorId)
    {
        return $query->where('created_by', $authorId);
    }

    /**
     * Scope for posts with featured images.
     *
     * @param  mixed  $query
     */
    public function scopeWithFeaturedImages($query)
    {
        return $query->has('media');
    }

    /**
     * Scope for posts without featured images.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutFeaturedImages($query)
    {
        return $query->doesntHave('media');
    }

    /**
     * Scope for posts with comments.
     *
     * @param  mixed  $query
     */
    public function scopeWithComments($query)
    {
        return $query->has('comments');
    }

    /**
     * Scope for posts without comments.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutComments($query)
    {
        return $query->doesntHave('comments');
    }

    /**
     * Scope for popular posts (most viewed).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Scope for trending posts (recent with high engagement).
     *
     * @param  mixed  $query
     */
    public function scopeTrending($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderByRaw('(views_count + likes_count + comments_count) DESC');
    }

    /**
     * Scope for latest posts.
     *
     * @param  mixed  $query
     */
    public function scopeLatest($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title');
    }

    /**
     * Scope for most viewed posts.
     *
     * @param  mixed  $query
     */
    public function scopeMostViewed($query, int $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Scope for most liked posts.
     *
     * @param  mixed  $query
     */
    public function scopeMostLiked($query, int $limit = 10)
    {
        return $query->orderBy('likes_count', 'desc')->limit($limit);
    }

    /**
     * Scope for most commented posts.
     *
     * @param  mixed  $query
     */
    public function scopeMostCommented($query, int $limit = 10)
    {
        return $query->orderBy('comments_count', 'desc')->limit($limit);
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the blog image URL.
     */
    public function getBlogImageUrlAttribute(): string
    {
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('front_web/images/blog-1.png');
    }

    /**
     * Get the display title.
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: 'Untitled Post';
    }

    /**
     * Get estimated reading time.
     */
    public function getReadingTimeAttribute(): string
    {
        $wordCount = str_word_count(strip_tags($this->content ?? $this->description));
        $minutes = ceil($wordCount / 200); // Average reading speed

        return $minutes.' min read';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }

        if (! $this->is_published) {
            return 'Draft';
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return 'Scheduled';
        }

        return 'Published';
    }

    /**
     * Get formatted published date.
     */
    public function getFormattedPublishedDateAttribute(): string
    {
        if (! $this->published_at) {
            return 'Not published';
        }

        return $this->published_at->format('M d, Y');
    }

    // =============================================
    // UTILITY METHODS
    // =============================================

    /**
     * Check if post is published and visible.
     */
    public function isPublished(): bool
    {
        return $this->is_active
               && $this->is_published
               && $this->published_at
               && $this->published_at->isPast();
    }

    /**
     * Check if post is scheduled for future publication.
     */
    public function isScheduled(): bool
    {
        return $this->is_published
               && $this->published_at
               && $this->published_at->isFuture();
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
        $this->clearCaches();
    }

    /**
     * Increment likes count.
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
        $this->clearCaches();
    }

    /**
     * Update comments count.
     */
    public function updateCommentsCount(): void
    {
        $this->update(['comments_count' => $this->comments()->count()]);
        $this->clearCaches();
    }

    /**
     * Generate slug from title.
     */
    public function generateSlug(): string
    {
        $slug = \Str::slug($this->title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    // =============================================
    // STATIC METHODS & CACHING
    // =============================================

    /**
     * Get cached published posts.
     */
    public static function getCachedPublished(int $limit = 10): Collection
    {
        return Cache::remember("posts_published_{$limit}", 1800, function () use ($limit) {
            return static::published()->active()->latest($limit)->get();
        });
    }

    /**
     * Get cached featured posts.
     */
    public static function getCachedFeatured(int $limit = 5): Collection
    {
        return Cache::remember("posts_featured_{$limit}", 1800, function () use ($limit) {
            return static::featured()->published()->active()->latest($limit)->get();
        });
    }

    /**
     * Get cached popular posts.
     */
    public static function getCachedPopular(int $limit = 10): Collection
    {
        return Cache::remember("posts_popular_{$limit}", 3600, function () use ($limit) {
            return static::popular($limit)->published()->active()->get();
        });
    }

    /**
     * Get cached posts by category.
     */
    public static function getCachedByCategory(int $categoryId, int $limit = 10): Collection
    {
        return Cache::remember("posts_category_{$categoryId}_{$limit}", 1800, function () use ($categoryId, $limit) {
            return static::byCategory($categoryId)->published()->active()->latest($limit)->get();
        });
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('posts_published_10');
        Cache::forget('posts_featured_5');
        Cache::forget('posts_popular_10');

        // Clear category-specific caches
        $this->postAssignCategories->each(function ($category) {
            Cache::forget("posts_category_{$category->id}_10");
        });

        // Clear pattern-based caches
        $this->clearCachePattern('posts_*');
    }

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
            'is_published' => 'boolean',
            'is_default' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'comments_count' => 'integer',
            'meta_data' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // =============================================
    // MODEL EVENTS
    // =============================================

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = $post->generateSlug();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = $post->generateSlug();
            }
        });

        static::saved(function ($post) {
            $post->clearCaches();
        });

        static::deleted(function ($post) {
            $post->clearCaches();
        });
    }

    /**
     * Clear cache by pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        if (method_exists(Cache::getStore(), 'flush')) {
            // For stores that support pattern clearing
            $keys = Cache::getStore()->getRedis()->keys($pattern);
            if (! empty($keys)) {
                Cache::getStore()->getRedis()->del($keys);
            }
        }
    }
}
