<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Term Model - Enhanced with Context7 patterns
 * 
 * Individual terms within taxonomies with hierarchical support.
 * Can be attached to any model via polymorphic relationships.
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $color
 * @property string|null $icon
 * @property string|null $image
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 * @property array|null $meta
 * @property int|null $parent_id
 * @property int $level
 * @property string|null $path
 * @property int $usage_count
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Taxonomy $taxonomy
 * @property-read \App\Models\Term|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $descendants
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $ancestors
 * @property-read string $display_name
 * @property-read string $full_path
 * @property-read bool $has_children
 * @property-read bool $is_root
 * @property-read bool $is_leaf
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder notFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder roots()
 * @method static \Illuminate\Database\Eloquent\Builder leaves()
 * @method static \Illuminate\Database\Eloquent\Builder byTaxonomy(int $taxonomyId)
 * @method static \Illuminate\Database\Eloquent\Builder byParent(?int $parentId)
 * @method static \Illuminate\Database\Eloquent\Builder byLevel(int $level)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder withUsageStats()
 *
 * @mixin \Eloquent
 */
class Term extends Model
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
        'taxonomy_id',
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'image',
        'is_active',
        'is_featured',
        'sort_order',
        'meta',
        'parent_id',
        'level',
        'path',
        'usage_count',
        'last_used_at',
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
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'meta' => 'array',
            'level' => 'integer',
            'usage_count' => 'integer',
            'last_used_at' => 'datetime',
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
                'taxonomy_id',
                'name',
                'slug',
                'description',
                'color',
                'icon',
                'image',
                'is_active',
                'is_featured',
                'sort_order',
                'meta',
                'parent_id',
                'level',
                'path',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating terms.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'taxonomy_id' => 'required|integer|exists:taxonomies,id',
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'icon' => 'nullable|string|max:255',
        'image' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
        'meta' => 'nullable|array',
        'parent_id' => 'nullable|integer|exists:terms,id',
    ];

    /**
     * Update validation rules for terms.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'taxonomy_id' => 'required|integer|exists:taxonomies,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta' => 'nullable|array',
            'parent_id' => 'nullable|integer|exists:terms,id',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the taxonomy that owns this term.
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    /**
     * Get the parent term.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    /**
     * Get the child terms.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get all descendant terms.
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id')->with('descendants');
    }

    /**
     * Get all ancestor terms.
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get all jobs tagged with this term.
     */
    public function jobs(): MorphToMany
    {
        return $this->morphedByMany(Job::class, 'taggable');
    }

    /**
     * Get all companies tagged with this term.
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'taggable');
    }

    /**
     * Get all candidates tagged with this term.
     */
    public function candidates(): MorphToMany
    {
        return $this->morphedByMany(Candidate::class, 'taggable');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active terms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive terms.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured terms.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured terms.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    // =============================================
    // SCOPES - Hierarchy
    // =============================================

    /**
     * Scope a query to only include root terms.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include leaf terms.
     */
    public function scopeLeaves($query)
    {
        return $query->whereDoesntHave('children');
    }

    /**
     * Scope a query to filter by taxonomy.
     */
    public function scopeByTaxonomy($query, int $taxonomyId)
    {
        return $query->where('taxonomy_id', $taxonomyId);
    }

    /**
     * Scope a query to filter by parent.
     */
    public function scopeByParent($query, ?int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope a query to search terms.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
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

    /**
     * Scope a query to order by popularity.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Scope a query to get recent terms.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc');
    }

    // =============================================
    // SCOPES - Advanced
    // =============================================

    /**
     * Scope a query to include usage statistics.
     */
    public function scopeWithUsageStats($query)
    {
        return $query->withCount(['jobs', 'companies', 'candidates']);
    }

    // =============================================
    // CACHE METHODS
    // =============================================

    /**
     * Get cached terms by taxonomy.
     */
    public static function getCachedByTaxonomy(int $taxonomyId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("terms.taxonomy.{$taxonomyId}", 3600, function () use ($taxonomyId) {
            return self::active()->byTaxonomy($taxonomyId)->ordered()->get();
        });
    }

    /**
     * Get cached root terms by taxonomy.
     */
    public static function getCachedRootsByTaxonomy(int $taxonomyId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("terms.roots.taxonomy.{$taxonomyId}", 3600, function () use ($taxonomyId) {
            return self::active()->byTaxonomy($taxonomyId)->roots()->ordered()->get();
        });
    }

    /**
     * Get cached popular terms.
     */
    public static function getCachedPopular(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("terms.popular.{$limit}", 3600, function () use ($limit) {
            return self::active()->popular($limit)->get();
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
     * Get the full path attribute.
     */
    public function getFullPathAttribute(): string
    {
        if ($this->path) {
            return $this->path . ' > ' . $this->name;
        }
        
        return $this->name;
    }

    /**
     * Get the has children attribute.
     */
    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get the is root attribute.
     */
    public function getIsRootAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get the is leaf attribute.
     */
    public function getIsLeafAttribute(): bool
    {
        return !$this->has_children;
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
        $this->clearCaches();
    }

    /**
     * Update hierarchy path.
     */
    public function updatePath(): void
    {
        $ancestors = $this->ancestors();
        $this->path = $ancestors->pluck('name')->implode(' > ');
        $this->level = $ancestors->count();
        $this->save();
    }

    /**
     * Get all siblings.
     */
    public function getSiblings()
    {
        return self::where('parent_id', $this->parent_id)
                  ->where('id', '!=', $this->id)
                  ->ordered()
                  ->get();
    }

    /**
     * Check if term is ancestor of another term.
     */
    public function isAncestorOf(Term $term): bool
    {
        return $term->ancestors()->contains('id', $this->id);
    }

    /**
     * Check if term is descendant of another term.
     */
    public function isDescendantOf(Term $term): bool
    {
        return $this->ancestors()->contains('id', $term->id);
    }

    /**
     * Clear all caches for this term.
     */
    public function clearCaches(): void
    {
        Cache::forget("terms.taxonomy.{$this->taxonomy_id}");
        Cache::forget("terms.roots.taxonomy.{$this->taxonomy_id}");
        Cache::forget("terms.popular.20");
        Cache::forget("term.{$this->id}.children");
        Cache::forget("term.{$this->slug}.data");
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
            
            // Ensure unique slug within taxonomy
            $originalSlug = $model->slug;
            $counter = 1;
            
            while (self::where('taxonomy_id', $model->taxonomy_id)
                      ->where('slug', $model->slug)
                      ->exists()) {
                $model->slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        });

        static::created(function ($model) {
            $model->updatePath();
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            
            if ($model->isDirty('parent_id')) {
                $model->updatePath();
            }
        });

        static::saved(function ($model) {
            $model->clearCaches();
            
            // Update children paths if parent changed
            if ($model->wasChanged('parent_id') || $model->wasChanged('name')) {
                $model->children->each(function ($child) {
                    $child->updatePath();
                });
            }
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}
