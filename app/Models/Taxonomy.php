<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Taxonomy Model - Enhanced with Context7 patterns
 * 
 * Organizes terms into logical categories for the job portal system.
 * Supports hierarchical structures and flexible metadata.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $type
 * @property bool $is_hierarchical
 * @property bool $is_active
 * @property bool $is_public
 * @property array|null $meta
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $terms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $activeTerms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $rootTerms
 * @property-read int $terms_count
 * @property-read int $active_terms_count
 * @property-read string $display_name
 * @property-read string $type_label
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder public()
 * @method static \Illuminate\Database\Eloquent\Builder private()
 * @method static \Illuminate\Database\Eloquent\Builder hierarchical()
 * @method static \Illuminate\Database\Eloquent\Builder flat()
 * @method static \Illuminate\Database\Eloquent\Builder byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder withTermCounts()
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 *
 * @mixin \Eloquent
 */
class Taxonomy extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'is_hierarchical',
        'is_active',
        'is_public',
        'meta',
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
     */
    protected function casts(): array
    {
        return [
            'is_hierarchical' => 'boolean',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'meta' => 'array',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Configure activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
                'type',
                'is_hierarchical',
                'is_active',
                'is_public',
                'meta',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating taxonomies.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:taxonomies,slug',
        'description' => 'nullable|string|max:1000',
        'type' => 'required|string|max:100',
        'is_hierarchical' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'meta' => 'nullable|array',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * Update validation rules for taxonomies.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug,' . $id,
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|max:100',
            'is_hierarchical' => 'boolean',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'meta' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // CONSTANTS
    // =============================================

    public const TYPES = [
        'job_category' => 'Job Categories',
        'job_type' => 'Job Types',
        'skill' => 'Skills',
        'industry' => 'Industries',
        'location' => 'Locations',
        'experience_level' => 'Experience Levels',
        'education_level' => 'Education Levels',
        'company_size' => 'Company Sizes',
        'salary_range' => 'Salary Ranges',
        'benefit' => 'Benefits',
        'tag' => 'Tags',
        'custom' => 'Custom',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get all terms for this taxonomy.
     */
    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get only active terms for this taxonomy.
     */
    public function activeTerms(): HasMany
    {
        return $this->hasMany(Term::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get root terms (no parent) for this taxonomy.
     */
    public function rootTerms(): HasMany
    {
        return $this->hasMany(Term::class)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get featured terms for this taxonomy.
     */
    public function featuredTerms(): HasMany
    {
        return $this->hasMany(Term::class)->where('is_featured', true)->orderBy('sort_order')->orderBy('name');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active taxonomies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive taxonomies.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include public taxonomies.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include private taxonomies.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope a query to only include hierarchical taxonomies.
     */
    public function scopeHierarchical($query)
    {
        return $query->where('is_hierarchical', true);
    }

    /**
     * Scope a query to only include flat taxonomies.
     */
    public function scopeFlat($query)
    {
        return $query->where('is_hierarchical', false);
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to search taxonomies.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('type', 'like', "%{$term}%");
        });
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope a query to order alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    // =============================================
    // SCOPES - Advanced
    // =============================================

    /**
     * Scope a query to include term counts.
     */
    public function scopeWithTermCounts($query)
    {
        return $query->withCount(['terms', 'activeTerms']);
    }

    /**
     * Scope a query to get popular taxonomies.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('terms')
                    ->orderBy('terms_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope a query to get recent taxonomies.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc');
    }

    // =============================================
    // CACHE METHODS
    // =============================================

    /**
     * Get cached active taxonomies.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('taxonomies.active', 3600, function () {
            return self::active()->ordered()->get();
        });
    }

    /**
     * Get cached taxonomies by type.
     */
    public static function getCachedByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("taxonomies.type.{$type}", 3600, function () use ($type) {
            return self::active()->byType($type)->ordered()->get();
        });
    }

    /**
     * Get cached public taxonomies.
     */
    public static function getCachedPublic(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('taxonomies.public', 3600, function () {
            return self::active()->public()->ordered()->get();
        });
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Get the display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the type label attribute.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get the terms count attribute.
     */
    public function getTermsCountAttribute(): int
    {
        return $this->terms()->count();
    }

    /**
     * Get the active terms count attribute.
     */
    public function getActiveTermsCountAttribute(): int
    {
        return $this->activeTerms()->count();
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if taxonomy has terms.
     */
    public function hasTerms(): bool
    {
        return $this->terms()->exists();
    }

    /**
     * Check if taxonomy has active terms.
     */
    public function hasActiveTerms(): bool
    {
        return $this->activeTerms()->exists();
    }

    /**
     * Get or create a term for this taxonomy.
     */
    public function getOrCreateTerm(string $name, ?int $parentId = null): Term
    {
        $slug = Str::slug($name);
        
        return $this->terms()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'parent_id' => $parentId,
                'is_active' => true,
            ]
        );
    }

    /**
     * Clear all caches for this taxonomy.
     */
    public function clearCaches(): void
    {
        Cache::forget('taxonomies.active');
        Cache::forget("taxonomies.type.{$this->type}");
        Cache::forget('taxonomies.public');
        Cache::forget("taxonomy.{$this->id}.terms");
        Cache::forget("taxonomy.{$this->slug}.terms");
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
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
