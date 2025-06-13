<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Skill Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $category
 * @property string|null $level
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property bool $is_technical
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read string $display_name
 * @property-read string $category_label
 * @property-read string $level_label
 * @property-read int $candidates_count
 * @property-read int $jobs_count
 * @property-read int $usage_count
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder technical()
 * @method static \Illuminate\Database\Eloquent\Builder nonTechnical()
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder byLevel(string $level)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder bySortOrder()
 * @method static \Illuminate\Database\Eloquent\Builder withCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withJobs()
 * @method static \Illuminate\Database\Eloquent\Builder trending()
 * @method static \Illuminate\Database\Eloquent\Builder inDemand()
 * @method static \Illuminate\Database\Eloquent\Builder programming()
 * @method static \Illuminate\Database\Eloquent\Builder design()
 * @method static \Illuminate\Database\Eloquent\Builder management()
 * @method static \Illuminate\Database\Eloquent\Builder communication()
 * @method static \Illuminate\Database\Eloquent\Builder beginner()
 * @method static \Illuminate\Database\Eloquent\Builder intermediate()
 * @method static \Illuminate\Database\Eloquent\Builder advanced()
 * @method static \Illuminate\Database\Eloquent\Builder expert()
 *
 * @mixin \Eloquent
 */
class Skill extends Model
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
        'description',
        'category',
        'level',
        'is_active',
        'is_default',
        'is_featured',
        'is_technical',
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
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'is_technical' => 'boolean',
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
                'description',
                'category',
                'level',
                'is_active',
                'is_default',
                'is_featured',
                'is_technical',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating skills.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:255|unique:skills,name',
        'description' => 'nullable|string|max:500',
        'category' => 'nullable|string|max:100',
        'level' => 'nullable|string|in:beginner,intermediate,advanced,expert',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
        'is_technical' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * Update validation rules for skills.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:255|unique:skills,name,' . $id,
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|in:beginner,intermediate,advanced,expert',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'is_technical' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // CONSTANTS
    // =============================================

    public const CATEGORIES = [
        'programming' => 'Programming & Development',
        'design' => 'Design & Creative',
        'management' => 'Management & Leadership',
        'communication' => 'Communication & Soft Skills',
        'marketing' => 'Marketing & Sales',
        'finance' => 'Finance & Accounting',
        'technical' => 'Technical & Engineering',
        'language' => 'Language Skills',
        'other' => 'Other Skills',
    ];

    public const LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the candidates that have this skill.
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidate_skills')
                    ->withPivot(['level', 'years_of_experience'])
                    ->withTimestamps();
    }

    /**
     * Get the jobs that require this skill.
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills')
                    ->withPivot(['level', 'is_required'])
                    ->withTimestamps();
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive skills.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include default skills.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom skills.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope a query to only include featured skills.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured skills.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to only include technical skills.
     */
    public function scopeTechnical($query)
    {
        return $query->where('is_technical', true);
    }

    /**
     * Scope a query to only include non-technical skills.
     */
    public function scopeNonTechnical($query)
    {
        return $query->where('is_technical', false);
    }

    // =============================================
    // SCOPES - Filtering & Search
    // =============================================

    /**
     * Scope for skills by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for skills by level.
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for searching skills.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for recent skills.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old skills.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular skills (most used).
     */
    public function scopePopular($query)
    {
        return $query->withCount(['candidates', 'jobs'])
                    ->orderByRaw('(candidates_count + jobs_count) DESC');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeBySortOrder($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('name', 'asc');
    }

    // =============================================
    // SCOPES - Relationships
    // =============================================

    /**
     * Scope for skills with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for skills with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for trending skills (recently added to jobs).
     */
    public function scopeTrending($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        })->withCount(['jobs' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        }])->orderBy('jobs_count', 'desc');
    }

    /**
     * Scope for in-demand skills (high job count).
     */
    public function scopeInDemand($query)
    {
        return $query->withCount('jobs')
                    ->having('jobs_count', '>', 5)
                    ->orderBy('jobs_count', 'desc');
    }

    // =============================================
    // SCOPES - Category Specific
    // =============================================

    /**
     * Scope for programming skills.
     */
    public function scopeProgramming($query)
    {
        return $query->where('category', 'programming');
    }

    /**
     * Scope for design skills.
     */
    public function scopeDesign($query)
    {
        return $query->where('category', 'design');
    }

    /**
     * Scope for management skills.
     */
    public function scopeManagement($query)
    {
        return $query->where('category', 'management');
    }

    /**
     * Scope for communication skills.
     */
    public function scopeCommunication($query)
    {
        return $query->where('category', 'communication');
    }

    // =============================================
    // SCOPES - Level Specific
    // =============================================

    /**
     * Scope for beginner level skills.
     */
    public function scopeBeginner($query)
    {
        return $query->where('level', 'beginner');
    }

    /**
     * Scope for intermediate level skills.
     */
    public function scopeIntermediate($query)
    {
        return $query->where('level', 'intermediate');
    }

    /**
     * Scope for advanced level skills.
     */
    public function scopeAdvanced($query)
    {
        return $query->where('level', 'advanced');
    }

    /**
     * Scope for expert level skills.
     */
    public function scopeExpert($query)
    {
        return $query->where('level', 'expert');
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached skills by category.
     */
    public static function getCachedByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "skills_category_{$category}",
            now()->addHours(12),
            fn() => static::active()
                ->byCategory($category)
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached active skills.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'skills_active',
            now()->addHours(6),
            fn() => static::active()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached featured skills.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'skills_featured',
            now()->addHours(6),
            fn() => static::active()
                ->featured()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached popular skills.
     */
    public static function getCachedPopular(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "skills_popular_{$limit}",
            now()->addHours(4),
            fn() => static::active()
                ->popular()
                ->limit($limit)
                ->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->level ? " ({$this->level_label})" : '');
    }

    /**
     * Get category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucwords($this->category ?? 'Other');
    }

    /**
     * Get level label attribute.
     */
    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->level] ?? ucwords($this->level ?? 'Not Specified');
    }

    /**
     * Get candidates count with caching.
     */
    public function getCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "skill_{$this->id}_candidates_count",
            now()->addHours(6),
            fn() => $this->candidates()->count()
        );
    }

    /**
     * Get jobs count with caching.
     */
    public function getJobsCountAttribute(): int
    {
        return Cache::remember(
            "skill_{$this->id}_jobs_count",
            now()->addHours(6),
            fn() => $this->jobs()->count()
        );
    }

    /**
     * Get usage count (candidates + jobs).
     */
    public function getUsageCountAttribute(): int
    {
        return $this->candidates_count + $this->jobs_count;
    }

    /**
     * Check if skill is in demand.
     */
    public function isInDemand(): bool
    {
        return $this->jobs_count > 5;
    }

    /**
     * Check if skill is trending.
     */
    public function isTrending(): bool
    {
        $recentJobsCount = $this->jobs()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        return $recentJobsCount > 2;
    }

    /**
     * Check if skill is popular.
     */
    public function isPopular(): bool
    {
        return $this->usage_count > 10;
    }

    /**
     * Get skill level numeric value.
     */
    public function getLevelNumeric(): int
    {
        return match ($this->level) {
            'beginner' => 1,
            'intermediate' => 2,
            'advanced' => 3,
            'expert' => 4,
            default => 0,
        };
    }

    /**
     * Clear skill-related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'skills_active',
            'skills_featured',
            "skill_{$this->id}_candidates_count",
            "skill_{$this->id}_jobs_count",
        ];

        if ($this->category) {
            $cacheKeys[] = "skills_category_{$this->category}";
        }

        // Clear popular skills cache
        for ($i = 10; $i <= 50; $i += 10) {
            $cacheKeys[] = "skills_popular_{$i}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($skill) {
            $skill->clearCaches();
        });

        static::deleted(function ($skill) {
            $skill->clearCaches();
        });

        static::restored(function ($skill) {
            $skill->clearCaches();
        });
    }
}
