<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\ImageSlider.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider query()
 *
 * @mixin \Eloquent
 *
 * @property int $id
 * @property null|string $description
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property mixed $image_slider_url
 * @property Collection|Media[] $media
 * @property null|int $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereUpdatedAt($value)
 *
 * @property int $is_active
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereIsActive($value)
 */
class ImageSlider extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const ALL = 2;
    public const ACTIVE = 1;
    public const DEACTIVE = 0;
    public const STATUS = [
        self::ALL => 'Select Status',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public const PATH = 'image-sliders';

    public $table = 'image_sliders';

    public $fillable = [
        'description',
        'is_active',
        'image_path',
        'settings',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'image_slider' => 'required|mimes:jpeg,jpg,png',
    ];

    /**
     * @var array
     */
    protected $appends = ['image_slider_url'];

    /**
     * @return mixed
     */
    public function getImageSliderUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/img/infyom-logo.png');
    }

    /**
     * Scope for active image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for ordering by sort order.
     *
     * @param  mixed  $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope for sliders with images.
     *
     * @param  mixed  $query
     */
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('image_url')->where('image_url', '!=', '');
    }

    /**
     * Scope for sliders with links.
     *
     * @param  mixed  $query
     */
    public function scopeWithLinks($query)
    {
        return $query->whereNotNull('link_url')->where('link_url', '!=', '');
    }

    /**
     * Scope for sliders without links.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutLinks($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('link_url')->orWhere('link_url', '');
        });
    }

    /**
     * Scope for gallery sliders.
     *
     * @param  mixed  $query
     */
    public function scopeGallery($query)
    {
        return $query->active()->withImages()->ordered();
    }

    /**
     * Scope for promotional sliders.
     *
     * @param  mixed  $query
     */
    public function scopePromotional($query)
    {
        return $query->active()->featured()->withLinks();
    }

    // =============================================
    // ADDITIONAL ENHANCED SCOPES
    // =============================================

    /**
     * Scope for latest image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for today's image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's image sliders.
     *
     * @param  mixed  $query
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for popular image sliders (active + featured).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query)
    {
        return $query->active()->featured()->ordered();
    }

    /**
     * Scope for homepage display.
     *
     * @param  mixed  $query
     */
    public function scopeHomepage($query)
    {
        return $query->active()->withImages()->ordered();
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
        return $this->is_featured ?? false;
    }

    /**
     * Check if slider has image.
     */
    public function hasImage(): bool
    {
        return $this->media->isNotEmpty() || ! empty($this->image_url);
    }

    /**
     * Get display description.
     */
    public function getDisplayDescriptionAttribute(): string
    {
        return $this->description ?: 'No description';
    }

    /**
     * Get next sort order.
     */
    public static function getNextSortOrder(): int
    {
        return static::max('sort_order') + 1;
    }

    // =============================================
    // CACHING METHODS
    // =============================================

    /**
     * Get cached active sliders.
     */
    public static function getCachedActive()
    {
        return Cache::remember('image_sliders.active', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Get cached homepage sliders.
     */
    public static function getCachedHomepage()
    {
        return Cache::remember('image_sliders.homepage', 3600, function () {
            return static::homepage()->get();
        });
    }

    /**
     * Clear slider caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('image_sliders.active');
        Cache::forget('image_sliders.homepage');
        Cache::forget('image_sliders.featured');
    }

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

            'id' => 'integer',
            'description' => 'string',
            'is_active' => 'boolean',
        ];
    }

    // =============================================
    // MODEL EVENTS
    // =============================================

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}
