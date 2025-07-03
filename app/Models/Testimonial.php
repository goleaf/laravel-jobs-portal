<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Testimonial Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $customer_name
 * @property null|string $customer_title
 * @property null|string $customer_company
 * @property null|string $customer_email
 * @property null|string $description
 * @property null|int $rating
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_verified
 * @property null|string $location
 * @property null|string $project_type
 * @property null|int $sort_order
 * @property null|Carbon $testimonial_date
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property Collection|Media[] $media
 * @property string $customer_image_url
 * @property string $customer_full_name
 * @property string $rating_stars
 * @property string $rating_text
 * @property bool $has_image
 * @property bool $is_high_rated
 * @property string $status_label
 * @property string $display_text
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder verified()
 * @method static \Illuminate\Database\Eloquent\Builder unverified()
 * @method static \Illuminate\Database\Eloquent\Builder published()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder byRating(int $rating)
 * @method static \Illuminate\Database\Eloquent\Builder highRated()
 * @method static \Illuminate\Database\Eloquent\Builder lowRated()
 * @method static \Illuminate\Database\Eloquent\Builder byLocation(string $location)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder withImage()
 * @method static \Illuminate\Database\Eloquent\Builder withoutImage()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder random(int $limit = 5)
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder fiveStars()
 * @method static \Illuminate\Database\Eloquent\Builder fourStarsAndAbove()
 * @method static \Illuminate\Database\Eloquent\Builder threeStarsAndAbove()
 *
 * @mixin \Eloquent
 */
class Testimonial extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Media collection constants.
     */
    public const PATH = 'testimonials';
    public const CUSTOMER_IMAGE_COLLECTION = 'customer_image';

    /**
     * Rating constants.
     */
    public const RATING_MIN = 1;
    public const RATING_MAX = 5;

    /**
     * Project type constants.
     */
    public const PROJECT_WEB = 'web_development';
    public const PROJECT_MOBILE = 'mobile_development';
    public const PROJECT_DESIGN = 'design';
    public const PROJECT_CONSULTING = 'consulting';
    public const PROJECT_OTHER = 'other';

    /**
     * Validation rules for creating testimonials.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_title' => 'nullable|string|max:255',
        'customer_company' => 'nullable|string|max:255',
        'customer_email' => 'nullable|email|max:255',
        'description' => 'required|string|max:2000',
        'rating' => 'required|integer|min:1|max:5',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'location' => 'nullable|string|max:255',
        'project_type' => 'nullable|string|max:100',
        'sort_order' => 'nullable|integer|min:0',
        'testimonial_date' => 'nullable|date|before_or_equal:today',
        'customer_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'testimonials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'customer_title',
        'customer_company',
        'customer_email',
        'description',
        'rating',
        'is_active',
        'is_featured',
        'is_verified',
        'location',
        'project_type',
        'sort_order',
        'testimonial_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'customer_email',
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'customer_image_url',
        'customer_full_name',
        'rating_stars',
        'has_image',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_name', 'description', 'rating', 'is_active', 'is_featured', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for testimonials.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_title' => 'nullable|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'description' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'location' => 'nullable|string|max:255',
            'project_type' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'testimonial_date' => 'nullable|date|before_or_equal:today',
            'customer_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to only include verified testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to only include unverified testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope to only include published testimonials (active and verified).
     *
     * @param  mixed  $query
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)->where('is_verified', true);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search testimonials by customer name, company, or description.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('customer_name', 'like', '%'.$term.'%')
                ->orWhere('customer_company', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%')
                ->orWhere('location', 'like', '%'.$term.'%');
        });
    }

    /**
     * Scope to get testimonials by location.
     *
     * @param  mixed  $query
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', '%'.$location.'%');
    }

    /**
     * Scope to get testimonials by project type.
     *
     * @param  mixed  $query
     */
    public function scopeByProjectType($query, string $projectType)
    {
        return $query->where('project_type', $projectType);
    }

    /**
     * Scope to get testimonials created within specified days.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old testimonials created before specified days.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Rating & Quality
    // =============================================

    /**
     * Scope to get testimonials by specific rating.
     *
     * @param  mixed  $query
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get high-rated testimonials (4+ stars).
     *
     * @param  mixed  $query
     */
    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope to get low-rated testimonials (3 or below).
     *
     * @param  mixed  $query
     */
    public function scopeLowRated($query)
    {
        return $query->where('rating', '<=', 3);
    }

    /**
     * Scope to get 5-star testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeFiveStars($query)
    {
        return $query->where('rating', 5);
    }

    /**
     * Scope to get 4+ star testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeFourStarsAndAbove($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope to get 3+ star testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeThreeStarsAndAbove($query)
    {
        return $query->where('rating', '>=', 3);
    }

    // =============================================
    // SCOPES - Media & Images
    // =============================================

    /**
     * Scope to get testimonials with customer images.
     *
     * @param  mixed  $query
     */
    public function scopeWithImage($query)
    {
        return $query->whereHas('media', function ($q) {
            $q->where('collection_name', self::CUSTOMER_IMAGE_COLLECTION);
        });
    }

    /**
     * Scope to get testimonials without customer images.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutImage($query)
    {
        return $query->whereDoesntHave('media', function ($q) {
            $q->where('collection_name', self::CUSTOMER_IMAGE_COLLECTION);
        });
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order testimonials alphabetically by customer name.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('customer_name', 'asc');
    }

    /**
     * Scope to order testimonials by sort order and rating.
     *
     * @param  mixed  $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get random testimonials.
     *
     * @param  mixed  $query
     */
    public function scopeRandom($query, int $limit = 5)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    /**
     * Scope to get popular testimonials (featured and high-rated).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query)
    {
        return $query->where('is_featured', true)
            ->where('rating', '>=', 4)
            ->orderBy('rating', 'desc');
    }

    // =============================================
    // CACHE METHODS - Enhanced Caching Strategy
    // =============================================

    /**
     * Get cached active testimonials.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember('testimonials.active', now()->addHours(6), function () {
            return static::active()->published()->ordered()->get();
        });
    }

    /**
     * Get cached featured testimonials.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember('testimonials.featured', now()->addHours(3), function () {
            return static::featured()->published()->ordered()->get();
        });
    }

    /**
     * Get cached random testimonials.
     */
    public static function getCachedRandom(int $limit = 5): Collection
    {
        return Cache::remember("testimonials.random.{$limit}", now()->addMinutes(30), function () use ($limit) {
            return static::published()->random($limit)->get();
        });
    }

    /**
     * Get cached high-rated testimonials.
     */
    public static function getCachedHighRated(): Collection
    {
        return Cache::remember('testimonials.high_rated', now()->addHours(6), function () {
            return static::highRated()->published()->ordered()->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the customer image URL attribute.
     */
    public function getCustomerImageUrlAttribute(): string
    {
        $media = $this->getFirstMedia(self::CUSTOMER_IMAGE_COLLECTION);
        if ($media) {
            return $media->getFullUrl();
        }

        return asset('assets/img/default-avatar.png');
    }

    /**
     * Get the customer full name attribute.
     */
    public function getCustomerFullNameAttribute(): string
    {
        $name = $this->customer_name;
        if ($this->customer_title) {
            $name .= ', '.$this->customer_title;
        }
        if ($this->customer_company) {
            $name .= ' at '.$this->customer_company;
        }

        return $name;
    }

    /**
     * Get the rating stars attribute.
     */
    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }

        return $stars;
    }

    /**
     * Get the rating text attribute.
     */
    public function getRatingTextAttribute(): string
    {
        return match ($this->rating) {
            5 => 'Excellent',
            4 => 'Very Good',
            3 => 'Good',
            2 => 'Fair',
            1 => 'Poor',
            default => 'Not Rated',
        };
    }

    /**
     * Check if testimonial has image.
     */
    public function getHasImageAttribute(): bool
    {
        return $this->hasMedia(self::CUSTOMER_IMAGE_COLLECTION);
    }

    /**
     * Check if testimonial is high rated.
     */
    public function getIsHighRatedAttribute(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Get status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }
        if (! $this->is_verified) {
            return 'Pending Verification';
        }
        if ($this->is_featured) {
            return 'Featured';
        }

        return 'Active';
    }

    /**
     * Get display text attribute (truncated description).
     */
    public function getDisplayTextAttribute(): string
    {
        return \Str::limit($this->description, 150);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if testimonial is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if testimonial is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if testimonial is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Check if testimonial is published.
     */
    public function isPublished(): bool
    {
        return $this->is_active && $this->is_verified;
    }

    /**
     * Check if testimonial is high rated.
     */
    public function isHighRated(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Mark testimonial as verified.
     */
    public function markAsVerified(): bool
    {
        return $this->update(['is_verified' => true]);
    }

    /**
     * Mark testimonial as unverified.
     */
    public function markAsUnverified(): bool
    {
        return $this->update(['is_verified' => false]);
    }

    /**
     * Get rating percentage.
     */
    public function getRatingPercentage(): float
    {
        return ($this->rating / self::RATING_MAX) * 100;
    }

    /**
     * Get rating color based on score.
     */
    public function getRatingColor(): string
    {
        return match ($this->rating) {
            5 => '#28a745',
            4 => '#6f42c1',
            3 => '#ffc107',
            2 => '#fd7e14',
            1 => '#dc3545',
            default => '#6c757d',
        };
    }

    /**
     * Get testimonial snippet.
     */
    public function getSnippet(int $length = 100): string
    {
        return \Str::limit(strip_tags($this->description), $length);
    }

    // =============================================
    // MEDIA LIBRARY METHODS
    // =============================================

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::CUSTOMER_IMAGE_COLLECTION)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Define media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections(self::CUSTOMER_IMAGE_COLLECTION);

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections(self::CUSTOMER_IMAGE_COLLECTION);
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
            'testimonials.active',
            'testimonials.featured',
            'testimonials.high_rated',
        ];

        // Clear random cache variants
        for ($i = 3; $i <= 10; $i++) {
            $cacheKeys[] = "testimonials.random.{$i}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'rating' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'sort_order' => 'integer',
            'testimonial_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
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
