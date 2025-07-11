<?php

namespace App\Models;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Skill Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $name
 * @property null|string $description
 * @property null|string $category
 * @property null|string $level
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property bool $is_technical
 * @property null|int $sort_order
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property Candidate[]|Collection $candidates
 * @property Collection|Job[] $jobs
 * @property string $display_name
 * @property string $category_label
 * @property string $level_label
 * @property int $candidates_count
 * @property int $jobs_count
 * @property int $usage_count
 *
 * Enhanced Enhanced Scopes:
 *
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
    use HasSettingsField;
    use LogsActivity;
    use SoftDeletes;

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

    /**
     * Default settings for skill model.
     */
    public $defaultSettings = [
        'display' => [
            'show_in_filters' => true,
            'show_endorsement_count' => true,
            'show_level_indicator' => true,
            'show_category_badge' => true,
            'featured_placement' => false,
            'color_scheme' => 'default',
            'icon_display' => true,
        ],
        'endorsement' => [
            'enable_endorsements' => true,
            'require_verification' => false,
            'auto_approve_endorsements' => true,
            'max_endorsements_per_user' => 5,
            'endorsement_weight_system' => false,
            'show_endorser_details' => true,
        ],
        'filtering' => [
            'enable_skill_search' => true,
            'group_by_category' => true,
            'show_related_skills' => true,
            'min_endorsements_to_show' => 0,
            'hide_inactive_skills' => true,
            'sort_by_popularity' => false,
        ],
        'analytics' => [
            'track_skill_views' => true,
            'track_endorsements' => true,
            'track_job_matches' => true,
            'popularity_metrics' => true,
            'trend_analysis' => false,
        ],
        'matching' => [
            'enable_skill_matching' => true,
            'fuzzy_matching' => true,
            'synonym_matching' => false,
            'weight_by_level' => true,
            'boost_verified_skills' => true,
        ],
        'notifications' => [
            'new_endorsement' => true,
            'skill_trending' => false,
            'skill_in_demand' => true,
            'related_opportunities' => true,
        ],
        'privacy' => [
            'public_endorsements' => true,
            'show_skill_level' => true,
            'hide_from_search' => false,
            'anonymous_endorsements' => false,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'display.show_in_filters' => 'boolean',
        'display.show_endorsement_count' => 'boolean',
        'display.show_level_indicator' => 'boolean',
        'display.show_category_badge' => 'boolean',
        'display.featured_placement' => 'boolean',
        'display.color_scheme' => 'string|in:default,blue,green,red,purple,orange',
        'display.icon_display' => 'boolean',

        'endorsement.enable_endorsements' => 'boolean',
        'endorsement.require_verification' => 'boolean',
        'endorsement.auto_approve_endorsements' => 'boolean',
        'endorsement.max_endorsements_per_user' => 'integer|min:1|max:20',
        'endorsement.endorsement_weight_system' => 'boolean',
        'endorsement.show_endorser_details' => 'boolean',

        'filtering.enable_skill_search' => 'boolean',
        'filtering.group_by_category' => 'boolean',
        'filtering.show_related_skills' => 'boolean',
        'filtering.min_endorsements_to_show' => 'integer|min:0|max:100',
        'filtering.hide_inactive_skills' => 'boolean',
        'filtering.sort_by_popularity' => 'boolean',

        'analytics.track_skill_views' => 'boolean',
        'analytics.track_endorsements' => 'boolean',
        'analytics.track_job_matches' => 'boolean',
        'analytics.popularity_metrics' => 'boolean',
        'analytics.trend_analysis' => 'boolean',

        'matching.enable_skill_matching' => 'boolean',
        'matching.fuzzy_matching' => 'boolean',
        'matching.synonym_matching' => 'boolean',
        'matching.weight_by_level' => 'boolean',
        'matching.boost_verified_skills' => 'boolean',

        'notifications.new_endorsement' => 'boolean',
        'notifications.skill_trending' => 'boolean',
        'notifications.skill_in_demand' => 'boolean',
        'notifications.related_opportunities' => 'boolean',

        'privacy.public_endorsements' => 'boolean',
        'privacy.show_skill_level' => 'boolean',
        'privacy.hide_from_search' => 'boolean',
        'privacy.anonymous_endorsements' => 'boolean',
    ];

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
     * Update validation rules for skills.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:255|unique:skills,name,'.$id,
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
        return $this->belongsToMany(Job::class, 'jobs_skill')
            ->withPivot(['level', 'is_required'])
            ->withTimestamps();
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active skills.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive skills.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include default skills.
     *
     * @param  mixed  $query
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom skills.
     *
     * @param  mixed  $query
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope a query to only include featured skills.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured skills.
     *
     * @param  mixed  $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to only include technical skills.
     *
     * @param  mixed  $query
     */
    public function scopeTechnical($query)
    {
        return $query->where('is_technical', true);
    }

    /**
     * Scope a query to only include non-technical skills.
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for skills by level.
     *
     * @param  mixed  $query
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for searching skills.
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old skills.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular skills (most used).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query)
    {
        return $query->withCount(['candidates', 'jobs'])
            ->orderByRaw('(candidates_count + jobs_count) DESC');
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for ordering by sort order.
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for skills with jobs.
     *
     * @param  mixed  $query
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for trending skills (recently added to jobs).
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
     */
    public function scopeProgramming($query)
    {
        return $query->where('category', 'programming');
    }

    /**
     * Scope for design skills.
     *
     * @param  mixed  $query
     */
    public function scopeDesign($query)
    {
        return $query->where('category', 'design');
    }

    /**
     * Scope for management skills.
     *
     * @param  mixed  $query
     */
    public function scopeManagement($query)
    {
        return $query->where('category', 'management');
    }

    /**
     * Scope for communication skills.
     *
     * @param  mixed  $query
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
     *
     * @param  mixed  $query
     */
    public function scopeBeginner($query)
    {
        return $query->where('level', 'beginner');
    }

    /**
     * Scope for intermediate level skills.
     *
     * @param  mixed  $query
     */
    public function scopeIntermediate($query)
    {
        return $query->where('level', 'intermediate');
    }

    /**
     * Scope for advanced level skills.
     *
     * @param  mixed  $query
     */
    public function scopeAdvanced($query)
    {
        return $query->where('level', 'advanced');
    }

    /**
     * Scope for expert level skills.
     *
     * @param  mixed  $query
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
    public static function getCachedByCategory(string $category): Collection
    {
        return Cache::remember(
            "skills_category_{$category}",
            now()->addHours(12),
            fn () => static::active()
                ->byCategory($category)
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached active skills.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember(
            'skills_active',
            now()->addHours(6),
            fn () => static::active()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached featured skills.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember(
            'skills_featured',
            now()->addHours(6),
            fn () => static::active()
                ->featured()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached popular skills.
     */
    public static function getCachedPopular(int $limit = 20): Collection
    {
        return Cache::remember(
            "skills_popular_{$limit}",
            now()->addHours(4),
            fn () => static::active()
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
        return $this->name.($this->level ? " ({$this->level_label})" : '');
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
            fn () => $this->candidates()->count()
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
            fn () => $this->jobs()->count()
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
