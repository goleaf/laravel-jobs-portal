<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * FAQ Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $category
 * @property int|null $user_id
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_published
 * @property int|null $view_count
 * @property int|null $helpful_count
 * @property int|null $not_helpful_count
 * @property int $sort_order
 * @property array|null $tags
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User|null $author
 * @property-read string $category_label
 * @property-read string $status_label
 * @property-read float $helpfulness_ratio
 * @property-read bool $is_helpful
 * @property-read bool $is_popular
 * @property-read string $display_text
 * @property-read string $excerpt
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder published()
 * @method static \Illuminate\Database\Eloquent\Builder unpublished()
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder byAuthor(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder helpful()
 * @method static \Illuminate\Database\Eloquent\Builder mostViewed()
 * @method static \Illuminate\Database\Eloquent\Builder byTag(string $tag)
 * @method static \Illuminate\Database\Eloquent\Builder general()
 * @method static \Illuminate\Database\Eloquent\Builder technical()
 * @method static \Illuminate\Database\Eloquent\Builder billing()
 * @method static \Illuminate\Database\Eloquent\Builder account()
 * @method static \Illuminate\Database\Eloquent\Builder security()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder latest()
 * @method static \Illuminate\Database\Eloquent\Builder oldest()
 *
 * @mixin \Eloquent
 */
class FAQ extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faqs';

    /**
     * Category constants
     */
    public const CATEGORY_GENERAL = 'general';
    public const CATEGORY_TECHNICAL = 'technical';
    public const CATEGORY_BILLING = 'billing';
    public const CATEGORY_ACCOUNT = 'account';
    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_JOBS = 'jobs';
    public const CATEGORY_COMPANY = 'company';
    public const CATEGORY_SUPPORT = 'support';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'user_id',
        'is_active',
        'is_featured',
        'is_published',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'sort_order',
        'tags',
        'meta',
        'published_at',
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
     * @var array<string>
     */
    protected $appends = [
        'category_label',
        'helpfulness_ratio',
        'excerpt',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'view_count' => 'integer',
            'helpful_count' => 'integer',
            'not_helpful_count' => 'integer',
            'sort_order' => 'integer',
            'tags' => 'array',
            'meta' => 'array',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'category', 'is_active', 'is_featured', 'is_published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating FAQs.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:5000',
        'category' => 'nullable|string|max:100',
        'user_id' => 'nullable|integer|exists:users,id',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'view_count' => 'nullable|integer|min:0',
        'helpful_count' => 'nullable|integer|min:0',
        'not_helpful_count' => 'nullable|integer|min:0',
        'sort_order' => 'nullable|integer|min:0',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
        'meta' => 'nullable|array',
        'published_at' => 'nullable|date',
    ];

    /**
     * Update validation rules for FAQs.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer|exists:users,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'view_count' => 'nullable|integer|min:0',
            'helpful_count' => 'nullable|integer|min:0',
            'not_helpful_count' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta' => 'nullable|array',
            'published_at' => 'nullable|date',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the author of the FAQ.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive FAQs.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured FAQs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured FAQs.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to only include published FAQs.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('is_active', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to only include unpublished FAQs.
     */
    public function scopeUnpublished($query)
    {
        return $query->where(function ($q) {
            $q->where('is_published', false)
              ->orWhere('is_active', false)
              ->orWhereNull('published_at')
              ->orWhere('published_at', '>', now());
        });
    }

    // =============================================
    // SCOPES - Category & Author
    // =============================================

    /**
     * Scope to get FAQs by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get FAQs by author.
     */
    public function scopeByAuthor($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get general FAQs.
     */
    public function scopeGeneral($query)
    {
        return $query->where('category', self::CATEGORY_GENERAL);
    }

    /**
     * Scope to get technical FAQs.
     */
    public function scopeTechnical($query)
    {
        return $query->where('category', self::CATEGORY_TECHNICAL);
    }

    /**
     * Scope to get billing FAQs.
     */
    public function scopeBilling($query)
    {
        return $query->where('category', self::CATEGORY_BILLING);
    }

    /**
     * Scope to get account FAQs.
     */
    public function scopeAccount($query)
    {
        return $query->where('category', self::CATEGORY_ACCOUNT);
    }

    /**
     * Scope to get security FAQs.
     */
    public function scopeSecurity($query)
    {
        return $query->where('category', self::CATEGORY_SECURITY);
    }

    /**
     * Scope to get job-related FAQs.
     */
    public function scopeJobs($query)
    {
        return $query->where('category', self::CATEGORY_JOBS);
    }

    /**
     * Scope to get company-related FAQs.
     */
    public function scopeCompany($query)
    {
        return $query->where('category', self::CATEGORY_COMPANY);
    }

    /**
     * Scope to get support FAQs.
     */
    public function scopeSupport($query)
    {
        return $query->where('category', self::CATEGORY_SUPPORT);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search FAQs by title, description, or tags.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhereJsonContains('tags', $term);
        });
    }

    /**
     * Scope to get FAQs by tag.
     */
    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope to get FAQs created within specified days.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old FAQs created before specified days.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Popularity & Performance
    // =============================================

    /**
     * Scope to get popular FAQs (by view count and helpfulness).
     */
    public function scopePopular($query)
    {
        return $query->where('view_count', '>', 100)
                    ->whereRaw('helpful_count > not_helpful_count')
                    ->orderByRaw('view_count + helpful_count DESC');
    }

    /**
     * Scope to get helpful FAQs.
     */
    public function scopeHelpful($query)
    {
        return $query->where('helpful_count', '>', 0)
                    ->whereRaw('helpful_count >= not_helpful_count')
                    ->orderBy('helpful_count', 'desc');
    }

    /**
     * Scope to get most viewed FAQs.
     */
    public function scopeMostViewed($query, int $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get trending FAQs (recent and popular).
     */
    public function scopeTrending($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->where('view_count', '>', 10)
                    ->orderByRaw('view_count + helpful_count DESC');
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order FAQs alphabetically by title.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope to order FAQs by sort order and title.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('title', 'asc');
    }

    /**
     * Scope to get latest FAQs.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get oldest FAQs.
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope to order by helpfulness.
     */
    public function scopeByHelpfulness($query)
    {
        return $query->orderByRaw('helpful_count - not_helpful_count DESC');
    }

    // =============================================
    // CACHE METHODS - Context7 Caching Strategy
    // =============================================

    /**
     * Get cached published FAQs.
     */
    public static function getCachedPublished(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('faqs.published', now()->addHours(2), function () {
            return static::published()->ordered()->get();
        });
    }

    /**
     * Get cached FAQs by category.
     */
    public static function getCachedByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("faqs.category.{$category}", now()->addHours(2), function () use ($category) {
            return static::published()->byCategory($category)->ordered()->get();
        });
    }

    /**
     * Get cached featured FAQs.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('faqs.featured', now()->addHours(1), function () {
            return static::featured()->published()->ordered()->get();
        });
    }

    /**
     * Get cached popular FAQs.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("faqs.popular.{$limit}", now()->addMinutes(30), function () use ($limit) {
            return static::popular()->published()->limit($limit)->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_TECHNICAL => 'Technical',
            self::CATEGORY_BILLING => 'Billing',
            self::CATEGORY_ACCOUNT => 'Account',
            self::CATEGORY_SECURITY => 'Security',
            self::CATEGORY_JOBS => 'Jobs',
            self::CATEGORY_COMPANY => 'Company',
            self::CATEGORY_SUPPORT => 'Support',
            default => $this->category ? ucfirst($this->category) : 'General',
        };
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        if (!$this->is_published) {
            return 'Draft';
        }
        if ($this->is_featured) {
            return 'Featured';
        }
        return 'Published';
    }

    /**
     * Get the helpfulness ratio attribute.
     */
    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        return $total > 0 ? round(($this->helpful_count / $total) * 100, 2) : 0;
    }

    /**
     * Check if FAQ is helpful.
     */
    public function getIsHelpfulAttribute(): bool
    {
        return $this->helpful_count > $this->not_helpful_count;
    }

    /**
     * Check if FAQ is popular.
     */
    public function getIsPopularAttribute(): bool
    {
        return $this->view_count > 50 && $this->is_helpful;
    }

    /**
     * Get display text attribute (cleaned description).
     */
    public function getDisplayTextAttribute(): string
    {
        return strip_tags($this->description);
    }

    /**
     * Get excerpt attribute (truncated description).
     */
    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->display_text, 150);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if FAQ is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if FAQ is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if FAQ is published.
     */
    public function isPublished(): bool
    {
        return $this->is_published && 
               $this->is_active && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): bool
    {
        return $this->increment('view_count');
    }

    /**
     * Mark as helpful.
     */
    public function markAsHelpful(): bool
    {
        return $this->increment('helpful_count');
    }

    /**
     * Mark as not helpful.
     */
    public function markAsNotHelpful(): bool
    {
        return $this->increment('not_helpful_count');
    }

    /**
     * Publish the FAQ.
     */
    public function publish(): bool
    {
        return $this->update([
            'is_published' => true,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the FAQ.
     */
    public function unpublish(): bool
    {
        return $this->update(['is_published' => false]);
    }

    /**
     * Get category color.
     */
    public function getCategoryColor(): string
    {
        return match($this->category) {
            self::CATEGORY_GENERAL => '#6c757d',
            self::CATEGORY_TECHNICAL => '#dc3545',
            self::CATEGORY_BILLING => '#28a745',
            self::CATEGORY_ACCOUNT => '#007bff',
            self::CATEGORY_SECURITY => '#fd7e14',
            self::CATEGORY_JOBS => '#6f42c1',
            self::CATEGORY_COMPANY => '#20c997',
            self::CATEGORY_SUPPORT => '#ffc107',
            default => '#6c757d',
        };
    }

    /**
     * Get FAQ excerpt with specified length.
     */
    public function getExcerpt(int $length = 150): string
    {
        return \Str::limit(strip_tags($this->description), $length);
    }

    /**
     * Add tag to FAQ.
     */
    public function addTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            return $this->update(['tags' => $tags]);
        }
        return true;
    }

    /**
     * Remove tag from FAQ.
     */
    public function removeTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        $tags = array_values(array_filter($tags, fn($t) => $t !== $tag));
        return $this->update(['tags' => $tags]);
    }

    // =============================================
    // CACHE MANAGEMENT
    // =============================================

    /**
     * Clear all related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'faqs.published',
            'faqs.featured',
            "faqs.category.{$this->category}",
        ];

        // Clear popular cache variants
        for ($i = 5; $i <= 20; $i += 5) {
            $cacheKeys[] = "faqs.popular.{$i}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default values
        static::creating(function ($model) {
            $model->view_count = $model->view_count ?? 0;
            $model->helpful_count = $model->helpful_count ?? 0;
            $model->not_helpful_count = $model->not_helpful_count ?? 0;
            $model->sort_order = $model->sort_order ?? 0;
            $model->category = $model->category ?? self::CATEGORY_GENERAL;
        });

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });

        static::restored(function ($model) {
            $model->clearCaches();
        });
    }
}
