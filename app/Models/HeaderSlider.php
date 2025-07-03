<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\ActivityLog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * HeaderSlider Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property null|string $title
 * @property null|string $sub_title
 * @property null|string $description
 * @property null|string $button_text
 * @property null|string $button_url
 * @property null|string $image_url
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 * @property null|string $target
 * @property null|string $css_class
 * @property null|array $metadata
 * @property null|Carbon $published_at
 * @property null|Carbon $expires_at
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property string $header_slider_url
 * @property string $display_title
 * @property string $display_description
 * @property bool $has_button
 * @property bool $has_image
 * @property bool $is_expired
 * @property bool $is_published
 * @property bool $is_recent
 * @property string $status_label
 * @property Collection|Media[] $media
 * @property null|int $media_count
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder featured()
 * @method static Builder notFeatured()
 * @method static Builder published()
 * @method static Builder unpublished()
 * @method static Builder notExpired()
 * @method static Builder expired()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder withImages()
 * @method static Builder withoutImages()
 * @method static Builder withButtons()
 * @method static Builder withoutButtons()
 * @method static Builder withSubtitles()
 * @method static Builder withoutSubtitles()
 * @method static Builder homepage()
 * @method static Builder search(string $term)
 * @method static Builder alphabetical()
 * @method static Builder ordered()
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder popular()
 * @method static Builder byTarget(string $target)
 * @method static Builder byCssClass(string $cssClass)
 * @method static Builder displayOrder()
 * @method static Builder priority()
 *
 * @mixin \Eloquent
 */
class HeaderSlider extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    /**
     * Status constants.
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
     * Target constants.
     */
    public const TARGET_SELF = '_self';
    public const TARGET_BLANK = '_blank';
    public const TARGET_PARENT = '_parent';
    public const TARGET_TOP = '_top';

    /**
     * Media collection path constant.
     */
    public const PATH = 'header-sliders';

    /**
     * Validation rules.
     */
    public static array $rules = [
        'title' => 'nullable|string|max:255',
        'sub_title' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'button_text' => 'nullable|string|max:100',
        'button_url' => 'nullable|url|max:500',
        'image_url' => 'nullable|url|max:500',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer|min:0|max:999',
        'target' => 'nullable|string|in:_self,_blank,_parent,_top',
        'css_class' => 'nullable|string|max:255',
        'metadata' => 'nullable|array',
        'published_at' => 'nullable|date',
        'expires_at' => 'nullable|date|after:published_at',
        'header_slider' => 'sometimes|required|mimes:jpeg,jpg,png,webp|max:2048',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'header_sliders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'sub_title',
        'description',
        'button_text',
        'button_url',
        'image_url',
        'is_active',
        'is_featured',
        'sort_order',
        'target',
        'css_class',
        'metadata',
        'published_at',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = ['header_slider_url'];

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'is_active', 'is_featured', 'sort_order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Header slider has been {$eventName}");
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active header sliders.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive header sliders.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured header sliders.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured header sliders.
     */
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for published header sliders.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    /**
     * Scope for unpublished header sliders.
     */
    public function scopeUnpublished(Builder $query): Builder
    {
        return $query->where('published_at', '>', now());
    }

    /**
     * Scope for non-expired header sliders.
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Scope for expired header sliders.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent header sliders.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old header sliders.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for today's header sliders.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's header sliders.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope for this month's header sliders.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // =============================================
    // SCOPES - Content-based
    // =============================================

    /**
     * Scope for sliders with images.
     */
    public function scopeWithImages(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNotNull('image_url')
                ->where('image_url', '!=', '')
                ->orWhereHas('media');
        });
    }

    /**
     * Scope for sliders without images.
     */
    public function scopeWithoutImages(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('image_url')
                ->orWhere('image_url', '');
        })->whereDoesntHave('media');
    }

    /**
     * Scope for sliders with buttons.
     */
    public function scopeWithButtons(Builder $query): Builder
    {
        return $query->whereNotNull('button_text')
            ->where('button_text', '!=', '')
            ->whereNotNull('button_url')
            ->where('button_url', '!=', '');
    }

    /**
     * Scope for sliders without buttons.
     */
    public function scopeWithoutButtons(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('button_text')
                ->orWhere('button_text', '')
                ->orWhereNull('button_url')
                ->orWhere('button_url', '');
        });
    }

    /**
     * Scope for sliders with subtitles.
     */
    public function scopeWithSubtitles(Builder $query): Builder
    {
        return $query->whereNotNull('sub_title')
            ->where('sub_title', '!=', '');
    }

    /**
     * Scope for sliders without subtitles.
     */
    public function scopeWithoutSubtitles(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('sub_title')
                ->orWhere('sub_title', '');
        });
    }

    /**
     * Scope for homepage sliders.
     */
    public function scopeHomepage(Builder $query): Builder
    {
        return $query->active()->published()->notExpired()->ordered();
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope for searching header sliders.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('sub_title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('button_text', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for filtering by target.
     */
    public function scopeByTarget(Builder $query, string $target): Builder
    {
        return $query->where('target', $target);
    }

    /**
     * Scope for filtering by CSS class.
     */
    public function scopeByCssClass(Builder $query, string $cssClass): Builder
    {
        return $query->where('css_class', 'like', "%{$cssClass}%");
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for display order (active first, then by sort order).
     */
    public function scopeDisplayOrder(Builder $query): Builder
    {
        return $query->orderBy('is_active', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for priority order (featured first).
     */
    public function scopePriority(Builder $query): Builder
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope for latest header sliders.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest header sliders.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for popular header sliders (featured + active).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->featured()->active()->ordered();
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Get the header slider URL attribute.
     */
    public function getHeaderSliderUrlAttribute(): string
    {
        // Check for uploaded media first
        $media = $this->getFirstMedia();
        if ($media) {
            return $media->getFullUrl();
        }

        // Fallback to image_url field
        if (! empty($this->image_url)) {
            return $this->image_url;
        }

        // Default fallback image
        return asset('assets/img/infyom-logo.png');
    }

    /**
     * Get display title.
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: 'Untitled Slider';
    }

    /**
     * Get display description.
     */
    public function getDisplayDescriptionAttribute(): string
    {
        return $this->description ? str_limit($this->description, 100) : '';
    }

    /**
     * Check if slider has button.
     */
    public function getHasButtonAttribute(): bool
    {
        return ! empty($this->button_text) && ! empty($this->button_url);
    }

    /**
     * Check if slider has image.
     */
    public function getHasImageAttribute(): bool
    {
        return ! empty($this->image_url) || $this->hasMedia();
    }

    /**
     * Check if slider is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if slider is published.
     */
    public function getIsPublishedAttribute(): bool
    {
        return ! $this->published_at || $this->published_at->isPast();
    }

    /**
     * Check if slider is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        if (! $this->is_published) {
            return 'Scheduled';
        }

        return 'Active';
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
     * Check if slider is available for display.
     */
    public function isAvailable(): bool
    {
        return $this->is_active
               && $this->is_published
               && ! $this->is_expired;
    }

    /**
     * Get next sort order.
     */
    public static function getNextSortOrder(): int
    {
        return static::max('sort_order') + 1;
    }

    // =============================================
    // STATIC HELPERS & CACHING
    // =============================================

    /**
     * Get cached active sliders for homepage.
     */
    public static function getCachedHomepage(): Collection
    {
        return Cache::remember('header_sliders.homepage', 3600, function () {
            return static::homepage()->get();
        });
    }

    /**
     * Get cached active sliders.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember('header_sliders.active', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Clear all header slider caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'header_sliders.homepage',
            'header_sliders.active',
            'header_sliders.featured',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'metadata' => 'array',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // =============================================
    // MODEL EVENTS
    // =============================================

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->sort_order) {
                $model->sort_order = static::getNextSortOrder();
            }
        });

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}
