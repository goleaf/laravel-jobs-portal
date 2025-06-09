<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $postAssignCategories
 * @property-read int|null $post_assign_category_count
 * @property-read mixed $post_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property-read \App\Models\User $user
 * @property-read mixed $blog_image_url
 * @property-read int|null $post_assign_categories_count
 */
class Post extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const PATH = 'posts';

    public $table = 'posts';

    /**
     * @var array
     */
    protected $appends = ['blog_image_url'];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:180',
        'description' => 'required',
        'image' => 'nullable|mimes:png,jpg,jepg',
    ];

    /**
     * @var string[]
     */
    public $fillable = [
        'title',
        'description',
        'created_by',
        'is_default',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
        protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'is_default' => 'boolean',
    
        ];
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'is_default' => 'boolean',
    
        ];
    }


    /**
     * @return mixed
     */
    public function getBlogImageUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('front_web/images/blog-1.png');
    }

    public function postAssignCategories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'post_assigned_categories', 'post_id', 'post_categories_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    /**
     * Scope for active posts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive posts.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for unpublished posts.
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false)
                    ->orWhere('published_at', '>', now());
    }

    /**
     * Scope for featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured posts.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching posts.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
    }

    /**
     * Scope for recent posts.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old posts.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for posts by category.
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('post_category_id', $categoryId);
    }

    /**
     * Scope for posts with featured images.
     */
    public function scopeWithFeaturedImages($query)
    {
        return $query->whereNotNull('featured_image')->where('featured_image', '!=', '');
    }

    /**
     * Scope for posts without featured images.
     */
    public function scopeWithoutFeaturedImages($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('featured_image')->orWhere('featured_image', '');
        });
    }

    /**
     * Scope for posts with comments.
     */
    public function scopeWithComments($query)
    {
        return $query->has('comments');
    }

    /**
     * Scope for posts without comments.
     */
    public function scopeWithoutComments($query)
    {
        return $query->doesntHave('comments');
    }

    /**
     * Scope for popular posts (with most comments).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('comments')
                    ->orderBy('comments_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for latest published posts.
     */
    public function scopeLatest($query, int $limit = 10)
    {
        return $query->published()
                    ->orderBy('published_at', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }
}
