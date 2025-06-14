<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Tag Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $slug
 * @property string|null $color
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $activeJobs
 * @property-read string $display_name
 * @property-read int $jobs_count
 * @property-read int $active_jobs_count
 * @property-read bool $is_popular
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder withJobs()
 * @method static \Illuminate\Database\Eloquent\Builder withoutJobs()
 * @method static \Illuminate\Database\Eloquent\Builder withActiveJobs()
 * @method static \Illuminate\Database\Eloquent\Builder withJobCounts()
 * @method static \Illuminate\Database\Eloquent\Builder byColor(string $color)
 * @method static \Illuminate\Database\Eloquent\Builder trending(int $days = 30, int $limit = 10)
 *
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'color',
        'is_active',
        'is_default',
        'is_featured',
        'sort_order',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
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
            ->logOnly(['name', 'description', 'is_active', 'is_default', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating tags.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:100|unique:tags,name',
        'description' => 'nullable|string|max:500',
        'slug' => 'nullable|string|max:100|unique:tags,slug',
        'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * Update validation rules for tags.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:100|unique:tags,name,' . $id,
            'description' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:100|unique:tags,slug,' . $id,
            'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobs for the tag.
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'jobs_tag');
    }

    /**
     * Get the active jobs for the tag.
     */
    public function activeJobs(): BelongsToMany
    {
        return $this->jobs()->where('jobs.is_active', true);
    }

    /**
     * Get the featured jobs for the tag.
     */
    public function featuredJobs(): BelongsToMany
    {
        return $this->jobs()->where('jobs.is_featured', true);
    }

    /**
     * Get the recent jobs for the tag.
     */
    public function recentJobs(): BelongsToMany
    {
        return $this->jobs()->where('jobs.created_at', '>=', now()->subDays(30));
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active tags.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive tags.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured tags.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured tags.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to only include default tags.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to only include custom tags.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search tags by name and description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('slug', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope to get tags created within specified days.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old tags created before specified days.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope to get tags by color.
     */
    public function scopeByColor($query, string $color)
    {
        return $query->where('color', $color);
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order tags alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope to order tags by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    // =============================================
    // SCOPES - Relationships & Counts
    // =============================================

    /**
     * Scope to get tags with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope to get tags without jobs.
     */
    public function scopeWithoutJobs($query)
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope to get tags with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('jobs.is_active', true);
        });
    }

    /**
     * Scope to get tags with featured jobs.
     */
    public function scopeWithFeaturedJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('jobs.is_featured', true);
        });
    }

    /**
     * Scope to get tags with job counts.
     */
    public function scopeWithJobCounts($query)
    {
        return $query->withCount([
            'jobs',
            'jobs as active_jobs_count' => function ($q) {
                $q->where('jobs.is_active', true);
            },
            'jobs as featured_jobs_count' => function ($q) {
                $q->where('jobs.is_featured', true);
            },
        ]);
    }

    // =============================================
    // SCOPES - Popular & Trending
    // =============================================

    /**
     * Scope to get popular tags by job count.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('jobs.is_active', true);
        }])->orderBy('jobs_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get trending tags by recent activity.
     */
    public function scopeTrending($query, int $days = 30, int $limit = 10)
    {
        return $query->withCount(['jobs as recent_jobs_count' => function ($q) use ($days) {
            $q->where('jobs.is_active', true)
              ->where('jobs.created_at', '>=', now()->subDays($days));
        }])->orderBy('recent_jobs_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get tags with minimum jobs.
     */
    public function scopeMinJobs($query, int $minJobs = 5)
    {
        return $query->withCount('jobs')->having('jobs_count', '>=', $minJobs);
    }

    // =============================================
    // CACHE METHODS - Context7 Caching Strategy
    // =============================================

    /**
     * Get cached active tags.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('tags.active', now()->addHours(6), function () {
            return static::active()->alphabetical()->get();
        });
    }

    /**
     * Get cached featured tags.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('tags.featured', now()->addHours(6), function () {
            return static::featured()->active()->alphabetical()->get();
        });
    }

    /**
     * Get cached popular tags.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("tags.popular.{$limit}", now()->addHours(3), function () use ($limit) {
            return static::popular($limit)->active()->get();
        });
    }

    /**
     * Get cached trending tags.
     */
    public static function getCachedTrending(int $days = 30, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("tags.trending.{$days}.{$limit}", now()->addHours(3), function () use ($days, $limit) {
            return static::trending($days, $limit)->active()->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the display name with job count.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if (isset($this->jobs_count)) {
            $name .= " ({$this->jobs_count})";
        }
        return $name;
    }

    /**
     * Get jobs count attribute.
     */
    public function getJobsCountAttribute(): int
    {
        return $this->jobs()->count();
    }

    /**
     * Get active jobs count attribute.
     */
    public function getActiveJobsCountAttribute(): int
    {
        return $this->activeJobs()->count();
    }

    /**
     * Get featured jobs count attribute.
     */
    public function getFeaturedJobsCountAttribute(): int
    {
        return $this->featuredJobs()->count();
    }

    /**
     * Get recent jobs count attribute.
     */
    public function getRecentJobsCountAttribute(): int
    {
        return $this->recentJobs()->count();
    }

    /**
     * Check if tag is popular.
     */
    public function getIsPopularAttribute(): bool
    {
        return $this->jobs()->count() >= 5;
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if tag is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if tag is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if tag is default.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if tag has jobs.
     */
    public function hasJobs(): bool
    {
        return $this->jobs()->count() > 0;
    }

    /**
     * Check if tag has active jobs.
     */
    public function hasActiveJobs(): bool
    {
        return $this->activeJobs()->count() > 0;
    }

    /**
     * Check if tag has featured jobs.
     */
    public function hasFeaturedJobs(): bool
    {
        return $this->featuredJobs()->count() > 0;
    }

    /**
     * Check if tag has a color.
     */
    public function hasColor(): bool
    {
        return !empty($this->color);
    }

    /**
     * Check if tag is popular (has 5+ jobs).
     */
    public function isPopular(): bool
    {
        return $this->jobs()->count() >= 5;
    }

    /**
     * Check if tag is trending (has 3+ recent jobs).
     */
    public function isTrending(): bool
    {
        return $this->recentJobs()->count() >= 3;
    }

    /**
     * Generate slug from name.
     */
    public function generateSlug(): string
    {
        return \Str::slug($this->name);
    }

    /**
     * Get badge HTML for the tag.
     */
    public function getBadgeHtml(): string
    {
        $color = $this->color ?: '#6c757d';
        return "<span class=\"badge\" style=\"background-color: {$color};\">{$this->name}</span>";
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
            'tags.active',
            'tags.featured',
        ];

        // Clear popular cache variants
        for ($i = 5; $i <= 20; $i += 5) {
            $cacheKeys[] = "tags.popular.{$i}";
        }

        // Clear trending cache variants
        foreach ([7, 14, 30] as $days) {
            foreach ([5, 10, 15] as $limit) {
                $cacheKeys[] = "tags.trending.{$days}.{$limit}";
            }
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

        // Generate slug automatically
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
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
