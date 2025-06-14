<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * CmsServices Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property bool $is_active
 * @property bool $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * Enhanced Enhanced Scopes:
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
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_services';

    /**
     * Media collection path constant.
     */
    public const PATH = 'settings';

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
     * Update validation rules for CMS services.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'key' => 'required|string|max:255|unique:cms_services,key,' . $id,
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
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured records.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured records.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to search by key or value.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', '%' . $term . '%')
              ->orWhere('value', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope a query to filter by specific key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope a query to only include recent records.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope a query to order records alphabetically by key.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('key');
    }

    /**
     * Scope a query to include records with media.
     */
    public function scopeWithMedia($query)
    {
        return $query->has('media');
    }

    /**
     * Scope a query to include records without media.
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
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('cms_services_active', 3600, function () {
            return static::active()->get();
        });
    }

    /**
     * Get cached featured CMS services.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
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
        return !empty($this->value);
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
     * Clear cache by pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        if (method_exists(Cache::getStore(), 'flush')) {
            // For stores that support pattern clearing
            $keys = Cache::getStore()->getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getStore()->getRedis()->del($keys);
            }
        }
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
}
