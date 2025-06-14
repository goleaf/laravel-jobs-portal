<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Taggable Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property int $tag_id
 * @property string $taggable_type
 * @property int $taggable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Tag $tag
 * @property-read \Illuminate\Database\Eloquent\Model $taggable
 * @property-read string $taggable_type_display
 * @property-read string $display_name
 * @property-read bool $is_recent
 * @property-read int $position
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder forTag(int $tagId)
 * @method static \Illuminate\Database\Eloquent\Builder forModel(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder forEntity(string $type, int $id)
 * @method static \Illuminate\Database\Eloquent\Builder byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder forJobs()
 * @method static \Illuminate\Database\Eloquent\Builder forCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder forCompanies()
 * @method static \Illuminate\Database\Eloquent\Builder forPosts()
 * @method static \Illuminate\Database\Eloquent\Builder withTags()
 * @method static \Illuminate\Database\Eloquent\Builder withTaggable()
 * @method static \Illuminate\Database\Eloquent\Builder withDetails()
 * @method static \Illuminate\Database\Eloquent\Builder trending(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder activeTagged()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder chronological()
 * @method static \Illuminate\Database\Eloquent\Builder byModel(string $model)
 * @method static \Illuminate\Database\Eloquent\Builder uniqueTypes()
 *
 * @mixin \Eloquent
 */
class Taggable extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'taggables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tag_id',
        'taggable_type',
        'taggable_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
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
            'tag_id' => 'integer',
            'taggable_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Validation rules
     *
     * @var array<string, string>
     */
    public static $rules = [
        'tag_id' => 'required|integer|exists:tags,id',
        'taggable_type' => 'required|string|max:255',
        'taggable_id' => 'required|integer|min:1',
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tag_id', 'taggable_type', 'taggable_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the tag associated with this tagging.
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Get the taggable model (polymorphic relation).
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for specific tag.
     */
    public function scopeForTag($query, int $tagId)
    {
        return $query->where('tag_id', $tagId);
    }

    /**
     * Scope for specific model type.
     */
    public function scopeForModel($query, string $type)
    {
        return $query->where('taggable_type', $type);
    }

    /**
     * Scope for specific entity.
     */
    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('taggable_type', $type)
                    ->where('taggable_id', $id);
    }

    /**
     * Scope by taggable type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('taggable_type', $type);
    }

    /**
     * Scope for recent taggings.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old taggings.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope for popular tag-model combinations.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->selectRaw('tag_id, taggable_type, COUNT(*) as usage_count')
                    ->groupBy('tag_id', 'taggable_type')
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for job-related tags.
     */
    public function scopeForJobs($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Job');
    }

    /**
     * Scope for candidate-related tags.
     */
    public function scopeForCandidates($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Candidate');
    }

    /**
     * Scope for company-related tags.
     */
    public function scopeForCompanies($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Company');
    }

    /**
     * Scope for post-related tags.
     */
    public function scopeForPosts($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Post');
    }

    /**
     * Scope with tag relationship.
     */
    public function scopeWithTags($query)
    {
        return $query->with('tag');
    }

    /**
     * Scope with taggable relationship.
     */
    public function scopeWithTaggable($query)
    {
        return $query->with('taggable');
    }

    /**
     * Scope with all relationships.
     */
    public function scopeWithDetails($query)
    {
        return $query->with(['tag', 'taggable']);
    }

    /**
     * Scope for trending tags.
     */
    public function scopeTrending($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('tag_id, COUNT(*) as trend_count')
                    ->groupBy('tag_id')
                    ->having('trend_count', '>=', 3)
                    ->orderBy('trend_count', 'desc');
    }

    /**
     * Scope for active tagged entities.
     */
    public function scopeActiveTagged($query)
    {
        return $query->whereHas('taggable', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for ordered taggings.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for chronological order.
     */
    public function scopeChronological($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope by model class.
     */
    public function scopeByModel($query, string $model)
    {
        return $query->where('taggable_type', $model);
    }

    /**
     * Scope for unique entity types.
     */
    public function scopeUniqueTypes($query)
    {
        return $query->distinct('taggable_type')
                    ->pluck('taggable_type');
    }

    /**
     * Get taggable type display name.
     */
    public function getTaggableTypeDisplayAttribute(): string
    {
        $typeMap = [
            'App\\Models\\Job' => 'Job',
            'App\\Models\\Candidate' => 'Candidate',
            'App\\Models\\Company' => 'Company',
            'App\\Models\\Post' => 'Post',
        ];

        return $typeMap[$this->taggable_type] ?? class_basename($this->taggable_type);
    }

    /**
     * Get display name combining tag and type.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->tag?->name . ' → ' . $this->taggable_type_display;
    }

    /**
     * Check if tagging is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Get position in tag order for entity.
     */
    public function getPositionAttribute(): int
    {
        return static::forEntity($this->taggable_type, $this->taggable_id)
                    ->where('created_at', '<=', $this->created_at)
                    ->count();
    }

    /**
     * Check if this is the first tag for the entity.
     */
    public function isFirstTag(): bool
    {
        return $this->position === 1;
    }

    /**
     * Check if this is a duplicate tagging.
     */
    public function isDuplicate(): bool
    {
        return static::forEntity($this->taggable_type, $this->taggable_id)
                    ->forTag($this->tag_id)
                    ->where('id', '!=', $this->id ?? 0)
                    ->exists();
    }

    /**
     * Get cached popular tags for a type.
     */
    public static function getCachedPopularForType(string $type, int $limit = 10)
    {
        $cacheKey = "taggables.popular.{$type}.{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($type, $limit) {
            return static::forModel($type)
                         ->popular($limit)
                         ->withTags()
                         ->get();
        });
    }

    /**
     * Get cached trending tags.
     */
    public static function getCachedTrending(int $days = 30, int $limit = 10)
    {
        $cacheKey = "taggables.trending.{$days}.{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(3), function () use ($days, $limit) {
            return static::trending($days)
                         ->withTags()
                         ->limit($limit)
                         ->get();
        });
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        $types = ['App\\Models\\Job', 'App\\Models\\Candidate', 'App\\Models\\Company', 'App\\Models\\Post'];
        
        foreach ($types as $type) {
            for ($i = 5; $i <= 20; $i += 5) {
                Cache::forget("taggables.popular.{$type}.{$i}");
            }
        }
        
        for ($d = 7; $d <= 90; $d += 7) {
            for ($l = 5; $l <= 20; $l += 5) {
                Cache::forget("taggables.trending.{$d}.{$l}");
            }
        }
    }

    /**
     * Model boot method.
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

        static::restored(function ($model) {
            $model->clearCaches();
        });
    }
} 