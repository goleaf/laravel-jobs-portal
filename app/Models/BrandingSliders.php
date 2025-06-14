<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * BrandingSliders Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $link_url
 * @property string|null $button_text
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $open_in_new_tab
 * @property int|null $sort_order
 * @property int|null $view_count
 * @property int|null $click_count
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read string $branding_slider_url
 * @property-read string $status_label
 * @property-read bool $is_live
 * @property-read bool $has_link
 * @property-read bool $has_image
 * @property-read float $click_through_rate
 * @property-read string $display_text
 *
 * Enhanced Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder live()
 * @method static \Illuminate\Database\Eloquent\Builder expired()
 * @method static \Illuminate\Database\Eloquent\Builder scheduled()
 * @method static \Illuminate\Database\Eloquent\Builder withLinks()
 * @method static \Illuminate\Database\Eloquent\Builder withoutLinks()
 * @method static \Illuminate\Database\Eloquent\Builder withImages()
 * @method static \Illuminate\Database\Eloquent\Builder withoutImages()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder trending()
 * @method static \Illuminate\Database\Eloquent\Builder mostViewed()
 * @method static \Illuminate\Database\Eloquent\Builder mostClicked()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder random(int $limit = 5)
 *
 * @mixin \Eloquent
 */
class BrandingSliders extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branding_sliders';

    /**
     * Status constants
     */
    public const ALL = 2;
    public const ACTIVE = 1;
    public const DEACTIVE = 0;
    public const STATUS = [
        self::ALL => 'Select Status',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    /**
     * Media collection constants
     */
    public const PATH = 'branding-sliders';
    public const SLIDER_IMAGE_COLLECTION = 'slider_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'link_url',
        'button_text',
        'is_active',
        'is_featured',
        'open_in_new_tab',
        'sort_order',
        'view_count',
        'click_count',
        'start_date',
        'end_date',
        'meta',
        'branding_slider_url',
        'status_label',
        'is_live',
        'has_link',
        'has_image',
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
        'branding_slider_url',
        'status_label',
        'is_live',
        'has_link',
        'has_image',
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
            'is_featured' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'click_count' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'meta' => 'array',
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
            ->logOnly(['title', 'description', 'link_url', 'is_active', 'is_featured', 'sort_order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating branding sliders.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'link_url' => 'nullable|url|max:500',
        'button_text' => 'nullable|string|max:100',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
        'view_count' => 'nullable|integer|min:0',
        'click_count' => 'nullable|integer|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after:start_date',
        'meta' => 'nullable|array',
        'branding_slider' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
    ];

    /**
     * Update validation rules for branding sliders.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'link_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'view_count' => 'nullable|integer|min:0',
            'click_count' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'meta' => 'nullable|array',
            'branding_slider' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive sliders.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured sliders.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured sliders.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    // =============================================
    // SCOPES - Date & Scheduling
    // =============================================

    /**
     * Scope to only include live sliders (active and within date range).
     */
    public function scopeLive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Scope to only include expired sliders.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('end_date')
                    ->where('end_date', '<', now());
    }

    /**
     * Scope to only include scheduled sliders (not yet started).
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('start_date')
                    ->where('start_date', '>', now());
    }

    // =============================================
    // SCOPES - Content & Media
    // =============================================

    /**
     * Scope to only include sliders with links.
     */
    public function scopeWithLinks($query)
    {
        return $query->whereNotNull('link_url')
                    ->where('link_url', '!=', '');
    }

    /**
     * Scope to only include sliders without links.
     */
    public function scopeWithoutLinks($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('link_url')
              ->orWhere('link_url', '');
        });
    }

    /**
     * Scope to only include sliders with images.
     */
    public function scopeWithImages($query)
    {
        return $query->whereHas('media', function ($q) {
            $q->where('collection_name', self::SLIDER_IMAGE_COLLECTION);
        });
    }

    /**
     * Scope to only include sliders without images.
     */
    public function scopeWithoutImages($query)
    {
        return $query->whereDoesntHave('media', function ($q) {
            $q->where('collection_name', self::SLIDER_IMAGE_COLLECTION);
        });
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search sliders by title or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope to get sliders created within specified days.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old sliders created before specified days.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Performance & Analytics
    // =============================================

    /**
     * Scope to get popular sliders (by view count).
     */
    public function scopePopular($query)
    {
        return $query->where('view_count', '>', 100)
                    ->orderBy('view_count', 'desc');
    }

    /**
     * Scope to get trending sliders (recent and popular).
     */
    public function scopeTrending($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->where('view_count', '>', 10)
                    ->orderBy('view_count', 'desc');
    }

    /**
     * Scope to get most viewed sliders.
     */
    public function scopeMostViewed($query, int $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get most clicked sliders.
     */
    public function scopeMostClicked($query, int $limit = 10)
    {
        return $query->orderBy('click_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get sliders with high click-through rate.
     */
    public function scopeHighCTR($query, float $minRate = 5.0)
    {
        return $query->whereRaw('(click_count / GREATEST(view_count, 1)) * 100 >= ?', [$minRate]);
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order sliders by sort order and creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order sliders alphabetically by title.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope to get random sliders.
     */
    public function scopeRandom($query, int $limit = 5)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    /**
     * Scope to order by performance metrics.
     */
    public function scopeByPerformance($query)
    {
        return $query->orderByRaw('(click_count + view_count) DESC');
    }

    // =============================================
    // CACHE METHODS - Enhanced Caching Strategy
    // =============================================

    /**
     * Get cached live sliders.
     */
    public static function getCachedLive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('branding_sliders.live', now()->addMinutes(30), function () {
            return static::live()->ordered()->get();
        });
    }

    /**
     * Get cached featured sliders.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('branding_sliders.featured', now()->addMinutes(30), function () {
            return static::featured()->live()->ordered()->get();
        });
    }

    /**
     * Get cached homepage sliders.
     */
    public static function getCachedHomepage(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("branding_sliders.homepage.{$limit}", now()->addMinutes(15), function () use ($limit) {
            return static::live()->withImages()->ordered()->limit($limit)->get();
        });
    }

    /**
     * Get cached popular sliders.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("branding_sliders.popular.{$limit}", now()->addHours(1), function () use ($limit) {
            return static::popular()->limit($limit)->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the branding slider URL attribute.
     */
    public function getBrandingSliderUrlAttribute(): string
    {
        $media = $this->getFirstMedia(self::SLIDER_IMAGE_COLLECTION);
        if ($media) {
            return $media->getFullUrl();
        }
        return asset('assets/img/default-slider.jpg');
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        if ($this->is_live) {
            return $this->is_featured ? 'Featured Live' : 'Live';
        }
        if ($this->start_date && $this->start_date->isFuture()) {
            return 'Scheduled';
        }
        if ($this->end_date && $this->end_date->isPast()) {
            return 'Expired';
        }
        return 'Active';
    }

    /**
     * Check if slider is live.
     */
    public function getIsLiveAttribute(): bool
    {
        if (!$this->is_active) return false;
        
        if ($this->start_date && $this->start_date->isFuture()) return false;
        if ($this->end_date && $this->end_date->isPast()) return false;
        
        return true;
    }

    /**
     * Check if slider has link.
     */
    public function getHasLinkAttribute(): bool
    {
        return !empty($this->link_url);
    }

    /**
     * Check if slider has image.
     */
    public function getHasImageAttribute(): bool
    {
        return $this->hasMedia(self::SLIDER_IMAGE_COLLECTION);
    }

    /**
     * Get click-through rate.
     */
    public function getClickThroughRateAttribute(): float
    {
        if ($this->view_count == 0) return 0;
        return round(($this->click_count / $this->view_count) * 100, 2);
    }

    /**
     * Get display text (cleaned description).
     */
    public function getDisplayTextAttribute(): string
    {
        return strip_tags($this->description ?? '');
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if slider is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if slider is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if slider is live.
     */
    public function isLive(): bool
    {
        return $this->is_live;
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): bool
    {
        return $this->increment('view_count');
    }

    /**
     * Increment click count.
     */
    public function incrementClicks(): bool
    {
        return $this->increment('click_count');
    }

    /**
     * Activate the slider.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the slider.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Schedule the slider.
     */
    public function schedule(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate = null): bool
    {
        return $this->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Get link target attribute.
     */
    public function getLinkTarget(): string
    {
        return $this->open_in_new_tab ? '_blank' : '_self';
    }

    /**
     * Get slider performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'views' => $this->view_count ?? 0,
            'clicks' => $this->click_count ?? 0,
            'ctr' => $this->click_through_rate,
            'engagement_score' => ($this->view_count + $this->click_count * 5) ?? 0,
        ];
    }

    // =============================================
    // MEDIA LIBRARY METHODS
    // =============================================

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::SLIDER_IMAGE_COLLECTION)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Define media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections(self::SLIDER_IMAGE_COLLECTION);

        $this->addMediaConversion('slider')
            ->width(1200)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections(self::SLIDER_IMAGE_COLLECTION);

        $this->addMediaConversion('mobile')
            ->width(800)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections(self::SLIDER_IMAGE_COLLECTION);
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
            'branding_sliders.live',
            'branding_sliders.featured',
        ];

        // Clear homepage cache variants
        for ($i = 3; $i <= 10; $i++) {
            $cacheKeys[] = "branding_sliders.homepage.{$i}";
            $cacheKeys[] = "branding_sliders.popular.{$i}";
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
            $model->click_count = $model->click_count ?? 0;
            $model->sort_order = $model->sort_order ?? 0;
            $model->open_in_new_tab = $model->open_in_new_tab ?? false;
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
