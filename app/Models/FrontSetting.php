<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\ActivityLog\Traits\LogsActivity;
use Spatie\ActivityLog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * FrontSetting Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property bool $is_active
 * @property bool $is_featured
 * @property string|null $description
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read string $display_value
 * @property-read bool $is_recent
 *
 * Context7 Enhanced Scopes:
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder featured()
 * @method static Builder byKey(string $key)
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder search(string $term)
 * @method static Builder alphabetical()
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder withMedia()
 * @method static Builder withoutMedia()
 *
 * @mixin Eloquent
 */
class FrontSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     */
    public $table = 'front_settings';

    /**
     * Featured jobs constant
     */
    const FEATURED_JOBS_ENABLED = 1;

    /**
     * Media collection path constant
     */
    public const PATH = 'advertise_image';

    /**
     * The attributes that are mass assignable.
     */
    public $fillable = [
        'key',
        'value',
        'is_active',
        'is_featured',
        'description',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Validation rules
     */
    public static $rules = [
        'key' => 'required|string|max:255|unique:front_settings,key',
        'value' => 'required|string',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'description' => 'nullable|string|max:500',
        'metadata' => 'nullable|array',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Front setting has been {$eventName}");
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured records.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured records.
     */
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to filter by specific key.
     */
    public function scopeByKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope a query to only include recent records.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query for today's records.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query for this week's records.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query for this month's records.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope a query to search records by key, value, or description.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', "%{$term}%")
              ->orWhere('value', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query for records with media.
     */
    public function scopeWithMedia(Builder $query): Builder
    {
        return $query->whereHas('media');
    }

    /**
     * Scope a query for records without media.
     */
    public function scopeWithoutMedia(Builder $query): Builder
    {
        return $query->whereDoesntHave('media');
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope a query for alphabetical ordering by key.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('key', 'asc');
    }

    /**
     * Scope a query for latest records.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query for oldest records.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query for priority ordering (featured first).
     */
    public function scopePriority(Builder $query): Builder
    {
        return $query->orderBy('is_featured', 'desc')
                    ->orderBy('is_active', 'desc')
                    ->orderBy('key', 'asc');
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Get display value attribute.
     */
    public function getDisplayValueAttribute(): string
    {
        return $this->value ?: 'Not set';
    }

    /**
     * Check if record is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if setting is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if setting is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get setting value by key.
     */
    public static function getValueByKey(string $key): ?string
    {
        return Cache::remember("front_setting.{$key}", 3600, function () use ($key) {
            return static::byKey($key)->value('value');
        });
    }

    /**
     * Set setting value by key.
     */
    public static function setValueByKey(string $key, string $value): bool
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $saved = $setting->save();
        
        if ($saved) {
            Cache::forget("front_setting.{$key}");
        }
        
        return $saved;
    }

    // =============================================
    // CACHING METHODS
    // =============================================

    /**
     * Get cached active settings.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('front_settings.active', 3600, function () {
            return static::active()->get();
        });
    }

    /**
     * Get cached featured settings.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('front_settings.featured', 3600, function () {
            return static::featured()->get();
        });
    }

    /**
     * Clear all front setting caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'front_settings.active',
            'front_settings.featured',
            "front_setting.{$this->key}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
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

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}
