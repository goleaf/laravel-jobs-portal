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

/**
 * CmsServices Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $key
 * @property null|string $value
 * @property bool $is_active
 * @property bool $is_featured
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder byKey(string $key)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder withMedia()
 * @method static \Illuminate\Database\Eloquent\Builder withoutMedia()
 *
 * @mixin \Eloquent
 */
class CmsServices extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Media collection path constant.
     */
    public const PATH = 'settings';

    /**
     * Validation rules for creating CMS services.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'key' => 'required|string|max:255|unique:cms_services,key',
        'value' => 'nullable|string',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'is_active',
        'is_featured',
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
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for CMS services.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'key' => 'required|string|max:255|unique:cms_services,key,'.$id,
            'value' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope a query to only include active records.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured records.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured records.
     *
     * @param  mixed  $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to search by key or value.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', '%'.$term.'%')
                ->orWhere('value', 'like', '%'.$term.'%');
        });
    }

    /**
     * Scope a query to filter by specific key.
     *
     * @param  mixed  $query
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope a query to only include recent records.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old records.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope a query to order records alphabetically by key.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('key');
    }

    /**
     * Scope a query to include records with media.
     *
     * @param  mixed  $query
     */
    public function scopeWithMedia($query)
    {
        return $query->has('media');
    }

    /**
     * Scope a query to include records without media.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutMedia($query)
    {
        return $query->doesntHave('media');
    }

    // =============================================
    // STATIC METHODS & CACHING
    // =============================================

    /**
     * Get cached active CMS services.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember('cms_services_active', 3600, function () {
            return static::active()->get();
        });
    }

    /**
     * Get cached featured CMS services.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember('cms_services_featured', 3600, function () {
            return static::featured()->active()->get();
        });
    }

    /**
     * Get service value by key with caching.
     */
    public static function getValueByKey(string $key): ?string
    {
        return Cache::remember("cms_service_key_{$key}", 3600, function () use ($key) {
            $service = static::where('key', $key)->first();

            return $service ? $service->value : null;
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the display name for the service.
     */
    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $this->key));
    }

    /**
     * Check if the service has a value.
     */
    public function hasValue(): bool
    {
        return ! empty($this->value);
    }

    /**
     * Get formatted value.
     */
    public function getFormattedValueAttribute(): string
    {
        if (empty($this->value)) {
            return 'Not set';
        }

        // Try to decode JSON values
        $decoded = json_decode($this->value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return implode(', ', array_values($decoded));
        }

        return $this->value;
    }

    // =============================================
    // UTILITY METHODS
    // =============================================

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('cms_services_active');
        Cache::forget('cms_services_featured');
        Cache::forget("cms_service_key_{$this->key}");

        // Clear pattern-based caches
        $this->clearCachePattern('cms_services_*');
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

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }

    /**
     * Clear cache by pattern - Fixed to handle different cache stores.
     */
    private function clearCachePattern(string $pattern): void
    {
        $store = Cache::getStore();

        // Only use Redis pattern clearing if we are actually using Redis
        if (method_exists($store, 'getRedis') && method_exists($store, 'connection')) {
            try {
                $redis = $store->getRedis();
                if (method_exists($redis, 'keys')) {
                    $keys = $redis->keys($pattern);
                    if (! empty($keys)) {
                        $redis->del($keys);
                    }
                }
            } catch (\Exception $e) {
                // Fallback to individual cache clearing
                $this->fallbackCacheClear();
            }
        } else {
            // For non-Redis stores (like array store in tests), use individual clearing
            $this->fallbackCacheClear();
        }
    }

    /**
     * Fallback cache clearing method.
     */
    private function fallbackCacheClear(): void
    {
        // Clear specific known cache keys instead of using patterns
        Cache::forget('cms_services_active');
        Cache::forget('cms_services_featured');
        Cache::forget("cms_service_key_{$this->key}");
    }
}
