<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * MaritalStatus Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $marital_status
 * @property string|null $description
 * @property string|null $display_name
 * @property string|null $short_code
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property int|null $sort_order
 * @property string|null $color
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $candidates
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $activeCandidates
 * @property-read string $status_name
 * @property-read string $status_category
 * @property-read int $candidates_count
 * @property-read int $active_candidates_count
 * @property-read bool $is_single
 * @property-read bool $is_married
 * @property-read bool $is_divorced
 * @property-read bool $is_widowed
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder withCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withoutCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withActiveCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withCandidateCounts()
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder single()
 * @method static \Illuminate\Database\Eloquent\Builder married()
 * @method static \Illuminate\Database\Eloquent\Builder divorced()
 * @method static \Illuminate\Database\Eloquent\Builder widowed()
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder available()
 * @method static \Illuminate\Database\Eloquent\Builder unavailable()
 *
 * @mixin \Eloquent
 */
class MaritalStatus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marital_statuses';

    /**
     * Marital status categories
     */
    public const CATEGORY_SINGLE = 'single';
    public const CATEGORY_MARRIED = 'married';
    public const CATEGORY_DIVORCED = 'divorced';
    public const CATEGORY_WIDOWED = 'widowed';
    public const CATEGORY_SEPARATED = 'separated';
    public const CATEGORY_OTHER = 'other';

    /**
     * Standard marital statuses
     */
    public const STATUS_SINGLE = 'Single';
    public const STATUS_MARRIED = 'Married';
    public const STATUS_DIVORCED = 'Divorced';
    public const STATUS_WIDOWED = 'Widowed';
    public const STATUS_SEPARATED = 'Separated';
    public const STATUS_DOMESTIC_PARTNERSHIP = 'Domestic Partnership';
    public const STATUS_CIVIL_UNION = 'Civil Union';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'marital_status',
        'description',
        'display_name',
        'short_code',
        'is_active',
        'is_default',
        'is_featured',
        'sort_order',
        'color',
        'icon',
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
            ->logOnly(['marital_status', 'description', 'is_active', 'is_default', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating marital statuses.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'marital_status' => 'required|string|max:100|unique:marital_statuses,marital_status',
        'description' => 'nullable|string|max:500',
        'display_name' => 'nullable|string|max:100',
        'short_code' => 'nullable|string|max:10|unique:marital_statuses,short_code',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
        'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'icon' => 'nullable|string|max:50',
    ];

    /**
     * Update validation rules for marital statuses.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'marital_status' => 'required|string|max:100|unique:marital_statuses,marital_status,' . $id,
            'description' => 'nullable|string|max:500',
            'display_name' => 'nullable|string|max:100',
            'short_code' => 'nullable|string|max:10|unique:marital_statuses,short_code,' . $id,
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'icon' => 'nullable|string|max:50',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get candidates that belong to this marital status.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(User::class, 'marital_status_id')->where('user_type', 'candidate');
    }

    /**
     * Get active candidates that belong to this marital status.
     */
    public function activeCandidates(): HasMany
    {
        return $this->candidates()->where('users.is_active', true);
    }

    /**
     * Get featured candidates that belong to this marital status.
     */
    public function featuredCandidates(): HasMany
    {
        return $this->candidates()->where('users.is_featured', true);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active marital statuses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive marital statuses.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured marital statuses.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured marital statuses.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to only include default marital statuses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to only include custom marital statuses.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search marital statuses by name or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('marital_status', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('display_name', 'like', '%' . $term . '%')
              ->orWhere('short_code', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope to get marital statuses created within specified days.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old marital statuses created before specified days.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order marital statuses alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('marital_status', 'asc');
    }

    /**
     * Scope to order marital statuses by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('marital_status', 'asc');
    }

    // =============================================
    // SCOPES - Relationships & Counts
    // =============================================

    /**
     * Scope to get marital statuses with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope to get marital statuses without candidates.
     */
    public function scopeWithoutCandidates($query)
    {
        return $query->doesntHave('candidates');
    }

    /**
     * Scope to get marital statuses with active candidates.
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('users.is_active', true);
        });
    }

    /**
     * Scope to get marital statuses with candidate counts.
     */
    public function scopeWithCandidateCounts($query)
    {
        return $query->withCount([
            'candidates',
            'candidates as active_candidates_count' => function ($q) {
                $q->where('users.is_active', true);
            },
            'candidates as featured_candidates_count' => function ($q) {
                $q->where('users.is_featured', true);
            },
        ]);
    }

    // =============================================
    // SCOPES - Popular & Trending
    // =============================================

    /**
     * Scope to get popular marital statuses by candidate count.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('users.is_active', true);
        }])->orderBy('candidates_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get marital statuses with minimum candidates.
     */
    public function scopeMinCandidates($query, int $minCandidates = 5)
    {
        return $query->withCount('candidates')->having('candidates_count', '>=', $minCandidates);
    }

    // =============================================
    // SCOPES - Marital Status Categories
    // =============================================

    /**
     * Scope to get single marital statuses.
     */
    public function scopeSingle($query)
    {
        return $query->where(function ($q) {
            $q->where('marital_status', 'like', '%single%')
              ->orWhere('marital_status', 'like', '%unmarried%')
              ->orWhere('marital_status', 'like', '%never%married%')
              ->orWhere('marital_status', 'like', '%bachelor%')
              ->orWhere('marital_status', 'like', '%spinster%');
        });
    }

    /**
     * Scope to get married marital statuses.
     */
    public function scopeMarried($query)
    {
        return $query->where(function ($q) {
            $q->where('marital_status', 'like', '%married%')
              ->orWhere('marital_status', 'like', '%spouse%')
              ->orWhere('marital_status', 'like', '%civil%union%')
              ->orWhere('marital_status', 'like', '%domestic%partnership%');
        });
    }

    /**
     * Scope to get divorced/separated marital statuses.
     */
    public function scopeDivorced($query)
    {
        return $query->where(function ($q) {
            $q->where('marital_status', 'like', '%divorced%')
              ->orWhere('marital_status', 'like', '%separated%')
              ->orWhere('marital_status', 'like', '%annulled%');
        });
    }

    /**
     * Scope to get widowed marital statuses.
     */
    public function scopeWidowed($query)
    {
        return $query->where(function ($q) {
            $q->where('marital_status', 'like', '%widowed%')
              ->orWhere('marital_status', 'like', '%widow%')
              ->orWhere('marital_status', 'like', '%widower%');
        });
    }

    /**
     * Scope to get marital statuses by category.
     */
    public function scopeByCategory($query, string $category)
    {
        switch ($category) {
            case self::CATEGORY_SINGLE:
                return $query->single();
            case self::CATEGORY_MARRIED:
                return $query->married();
            case self::CATEGORY_DIVORCED:
            case self::CATEGORY_SEPARATED:
                return $query->divorced();
            case self::CATEGORY_WIDOWED:
                return $query->widowed();
            default:
                return $query;
        }
    }

    /**
     * Scope to get available marital statuses (single, divorced, widowed).
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('id', function ($subQuery) {
                $subQuery->select('id')->from('marital_statuses');
                $subQuery->where(function ($sq) {
                    $sq->where('marital_status', 'like', '%single%')
                       ->orWhere('marital_status', 'like', '%divorced%')
                       ->orWhere('marital_status', 'like', '%widowed%')
                       ->orWhere('marital_status', 'like', '%separated%');
                });
            });
        });
    }

    /**
     * Scope to get unavailable marital statuses (married, partnership).
     */
    public function scopeUnavailable($query)
    {
        return $query->where(function ($q) {
            $q->where('marital_status', 'like', '%married%')
              ->orWhere('marital_status', 'like', '%partnership%')
              ->orWhere('marital_status', 'like', '%civil%union%');
        });
    }

    // =============================================
    // CACHE METHODS - Context7 Caching Strategy
    // =============================================

    /**
     * Get cached active marital statuses.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('marital_statuses.active', now()->addHours(12), function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Get cached featured marital statuses.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('marital_statuses.featured', now()->addHours(6), function () {
            return static::featured()->active()->ordered()->get();
        });
    }

    /**
     * Get cached popular marital statuses.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("marital_statuses.popular.{$limit}", now()->addHours(6), function () use ($limit) {
            return static::popular($limit)->active()->get();
        });
    }

    /**
     * Get cached marital statuses by category.
     */
    public static function getCachedByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("marital_statuses.category.{$category}", now()->addHours(12), function () use ($category) {
            return static::byCategory($category)->active()->ordered()->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the status name attribute.
     */
    public function getStatusNameAttribute(): string
    {
        return $this->display_name ?: $this->marital_status;
    }

    /**
     * Get the status category attribute.
     */
    public function getStatusCategoryAttribute(): string
    {
        $status = strtolower($this->marital_status);

        if (str_contains($status, 'single') || str_contains($status, 'unmarried') || str_contains($status, 'never')) {
            return self::CATEGORY_SINGLE;
        } elseif (str_contains($status, 'married') || str_contains($status, 'spouse') || str_contains($status, 'union')) {
            return self::CATEGORY_MARRIED;
        } elseif (str_contains($status, 'divorced') || str_contains($status, 'separated')) {
            return self::CATEGORY_DIVORCED;
        } elseif (str_contains($status, 'widowed') || str_contains($status, 'widow')) {
            return self::CATEGORY_WIDOWED;
        }

        return self::CATEGORY_OTHER;
    }

    /**
     * Get candidates count attribute.
     */
    public function getCandidatesCountAttribute(): int
    {
        return $this->candidates()->count();
    }

    /**
     * Get active candidates count attribute.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return $this->activeCandidates()->count();
    }

    /**
     * Check if marital status is single category.
     */
    public function getIsSingleAttribute(): bool
    {
        return $this->status_category === self::CATEGORY_SINGLE;
    }

    /**
     * Check if marital status is married category.
     */
    public function getIsMarriedAttribute(): bool
    {
        return $this->status_category === self::CATEGORY_MARRIED;
    }

    /**
     * Check if marital status is divorced category.
     */
    public function getIsDivorcedAttribute(): bool
    {
        return $this->status_category === self::CATEGORY_DIVORCED;
    }

    /**
     * Check if marital status is widowed category.
     */
    public function getIsWidowedAttribute(): bool
    {
        return $this->status_category === self::CATEGORY_WIDOWED;
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if marital status is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if marital status is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if marital status is default.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if marital status has candidates.
     */
    public function hasCandidates(): bool
    {
        return $this->candidates()->count() > 0;
    }

    /**
     * Check if marital status has active candidates.
     */
    public function hasActiveCandidates(): bool
    {
        return $this->activeCandidates()->count() > 0;
    }

    /**
     * Check if status indicates availability for relationships.
     */
    public function isAvailable(): bool
    {
        return in_array($this->status_category, [
            self::CATEGORY_SINGLE,
            self::CATEGORY_DIVORCED,
            self::CATEGORY_WIDOWED,
        ]);
    }

    /**
     * Check if status indicates unavailability for relationships.
     */
    public function isUnavailable(): bool
    {
        return $this->status_category === self::CATEGORY_MARRIED;
    }

    /**
     * Get badge HTML for the marital status.
     */
    public function getBadgeHtml(): string
    {
        $color = $this->color ?: '#6c757d';
        $name = $this->status_name;
        return "<span class=\"badge\" style=\"background-color: {$color};\">{$name}</span>";
    }

    /**
     * Get icon HTML.
     */
    public function getIconHtml(): string
    {
        if ($this->icon) {
            return "<i class=\"{$this->icon}\"></i>";
        }

        // Default icons based on category
        $defaultIcons = [
            self::CATEGORY_SINGLE => 'fas fa-user',
            self::CATEGORY_MARRIED => 'fas fa-heart',
            self::CATEGORY_DIVORCED => 'fas fa-user-times',
            self::CATEGORY_WIDOWED => 'fas fa-user-minus',
        ];

        $icon = $defaultIcons[$this->status_category] ?? 'fas fa-question';
        return "<i class=\"{$icon}\"></i>";
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
            'marital_statuses.active',
            'marital_statuses.featured',
        ];

        // Clear popular cache variants
        for ($i = 5; $i <= 15; $i += 5) {
            $cacheKeys[] = "marital_statuses.popular.{$i}";
        }

        // Clear category cache variants
        $categories = [
            self::CATEGORY_SINGLE,
            self::CATEGORY_MARRIED,
            self::CATEGORY_DIVORCED,
            self::CATEGORY_WIDOWED,
            self::CATEGORY_SEPARATED,
            self::CATEGORY_OTHER,
        ];
        foreach ($categories as $category) {
            $cacheKeys[] = "marital_statuses.category.{$category}";
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
