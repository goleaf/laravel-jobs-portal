<?php

namespace App\Models;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class JobCategory.
 *
 * @property int              $id
 * @property string           $name
 * @property null|string      $description
 * @property bool             $is_featured
 * @property bool             $is_default
 * @property bool             $is_active
 * @property null|Carbon      $created_at
 * @property null|Carbon      $updated_at
 * @property Collection|Job[] $jobs
 * @property null|int         $jobs_count
 * @property mixed            $usage_count
 * @property mixed            $formatted_usage_stats
 *
 * @method static Builder|JobCategory newModelQuery()
 * @method static Builder|JobCategory newQuery()
 * @method static Builder|JobCategory query()
 * @method static Builder|JobCategory whereCreatedAt($value)
 * @method static Builder|JobCategory whereId($value)
 * @method static Builder|JobCategory whereName($value)
 * @method static Builder|JobCategory whereDescription($value)
 * @method static Builder|JobCategory whereIsFeatured($value)
 * @method static Builder|JobCategory whereIsDefault($value)
 * @method static Builder|JobCategory whereIsActive($value)
 * @method static Builder|JobCategory whereUpdatedAt($value)
 * @method static Builder|JobCategory active()
 * @method static Builder|JobCategory inactive()
 * @method static Builder|JobCategory featured()
 * @method static Builder|JobCategory notFeatured()
 * @method static Builder|JobCategory default()
 * @method static Builder|JobCategory custom()
 * @method static Builder|JobCategory withJobs()
 * @method static Builder|JobCategory withActiveJobs()
 * @method static Builder|JobCategory search(string $term)
 * @method static Builder|JobCategory popular(int $limit = 10)
 * @method static Builder|JobCategory alphabetical()
 * @method static Builder|JobCategory recent(int $days = 30)
 * @method static Builder|JobCategory trending()
 * @method static Builder|JobCategory minUsage(int $count = 1)
 * @method static Builder|JobCategory highDemand(int $minJobs = 10)
 * @method static Builder|JobCategory technology()
 * @method static Builder|JobCategory healthcare()
 * @method static Builder|JobCategory finance()
 * @method static Builder|JobCategory education()
 * @method static Builder|JobCategory engineering()
 *
 * @mixin \Eloquent
 */
class JobCategory extends Model
{
    use HasFactory;
    use LogsActivity;
    use HasSettingsField;

    /**
     * Media path constant for file uploads
     */
    public const PATH = 'job-categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_featured',
        'is_default',
        'is_active',
        'sort_order',
        'icon',
        'color',
        'meta_title',
        'meta_description',
        'slug',
        'image_path',
        'parent_id',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
        'description' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default settings for job category model.
     */
    public $defaultSettings = [
        'display' => [
            'show_in_navigation' => true,
            'show_job_count' => true,
            'show_description' => true,
            'show_icon' => true,
            'layout' => 'grid', // grid, list, card
            'color_scheme' => 'default',
            'featured_placement' => false,
            'priority_order' => 0,
        ],
        'filtering' => [
            'enable_filtering' => true,
            'default_sort' => 'name', // name, job_count, recent, popular
            'show_subcategories' => true,
            'group_by_parent' => false,
            'min_jobs_to_show' => 1,
            'hide_empty_categories' => false,
        ],
        'seo' => [
            'custom_meta_title' => '',
            'custom_meta_description' => '',
            'custom_keywords' => '',
            'canonical_url' => '',
            'robots_index' => true,
            'robots_follow' => true,
            'structured_data_enabled' => true,
        ],
        'content' => [
            'show_related_categories' => true,
            'show_trending_jobs' => true,
            'show_salary_insights' => true,
            'show_location_breakdown' => true,
            'enable_category_blog' => false,
            'custom_description' => '',
        ],
        'notifications' => [
            'notify_new_jobs' => false,
            'notify_trending_changes' => false,
            'weekly_digest' => false,
            'admin_alerts' => true,
        ],
        'analytics' => [
            'track_views' => true,
            'track_job_clicks' => true,
            'track_search_queries' => true,
            'google_analytics_enabled' => false,
            'custom_tracking_code' => '',
        ],
        'features' => [
            'enable_job_alerts' => true,
            'enable_saved_searches' => true,
            'enable_category_following' => false,
            'enable_expert_advice' => false,
            'premium_features_enabled' => false,
        ],
        'moderation' => [
            'auto_approve_jobs' => true,
            'require_admin_review' => false,
            'spam_detection_enabled' => true,
            'quality_score_threshold' => 70,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'display.show_in_navigation' => 'boolean',
        'display.show_job_count' => 'boolean',
        'display.show_description' => 'boolean',
        'display.show_icon' => 'boolean',
        'display.layout' => 'string|in:grid,list,card',
        'display.color_scheme' => 'string|in:default,blue,green,red,purple,orange',
        'display.featured_placement' => 'boolean',
        'display.priority_order' => 'integer|min:0|max:100',
        
        'filtering.enable_filtering' => 'boolean',
        'filtering.default_sort' => 'string|in:name,job_count,recent,popular',
        'filtering.show_subcategories' => 'boolean',
        'filtering.group_by_parent' => 'boolean',
        'filtering.min_jobs_to_show' => 'integer|min:0|max:100',
        'filtering.hide_empty_categories' => 'boolean',
        
        'seo.custom_meta_title' => 'string|max:60',
        'seo.custom_meta_description' => 'string|max:160',
        'seo.custom_keywords' => 'string|max:255',
        'seo.canonical_url' => 'url|nullable',
        'seo.robots_index' => 'boolean',
        'seo.robots_follow' => 'boolean',
        'seo.structured_data_enabled' => 'boolean',
        
        'content.show_related_categories' => 'boolean',
        'content.show_trending_jobs' => 'boolean',
        'content.show_salary_insights' => 'boolean',
        'content.show_location_breakdown' => 'boolean',
        'content.enable_category_blog' => 'boolean',
        'content.custom_description' => 'string|max:1000',
        
        'notifications.notify_new_jobs' => 'boolean',
        'notifications.notify_trending_changes' => 'boolean',
        'notifications.weekly_digest' => 'boolean',
        'notifications.admin_alerts' => 'boolean',
        
        'analytics.track_views' => 'boolean',
        'analytics.track_job_clicks' => 'boolean',
        'analytics.track_search_queries' => 'boolean',
        'analytics.google_analytics_enabled' => 'boolean',
        'analytics.custom_tracking_code' => 'string|max:500',
        
        'features.enable_job_alerts' => 'boolean',
        'features.enable_saved_searches' => 'boolean',
        'features.enable_category_following' => 'boolean',
        'features.enable_expert_advice' => 'boolean',
        'features.premium_features_enabled' => 'boolean',
        
        'moderation.auto_approve_jobs' => 'boolean',
        'moderation.require_admin_review' => 'boolean',
        'moderation.spam_detection_enabled' => 'boolean',
        'moderation.quality_score_threshold' => 'integer|min:0|max:100',
    ];

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'is_featured', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("job_category.{$this->id}.usage_count", 3600, function () {
            return $this->jobs()->count();
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("job_category.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->where('is_active', true)->count(),
                'featured_jobs' => $this->jobs()->where('is_featured', true)->count(),
                'total_usage' => $this->usage_count,
                'demand_level' => $this->getDemandLevel(),
                'category_type' => $this->getCategoryType(),
            ];
        });
    }

    /**
     * Relationship: Jobs.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_category_id');
    }

    /**
     * Relationship: Parent Category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'parent_id');
    }

    /**
     * Relationship: Child Categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(JobCategory::class, 'parent_id');
    }

    /**
     * Scope for active job categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive job categories.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured job categories.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured job categories.
     */
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for default job categories.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom job categories.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for job categories with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->has('jobs');
    }

    /**
     * Scope for job categories with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching job categories.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
        ;
    }

    /**
     * Scope for recent job categories.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for popular job categories (with most jobs).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount('jobs')
            ->orderBy('jobs_count', 'desc')
            ->limit($limit)
        ;
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for trending job categories.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
            'jobs' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
        ])
            ->orderByDesc('jobs_count')
        ;
    }

    /**
     * Scope for job categories with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $count)
        ;
    }

    /**
     * Scope for high demand job categories.
     */
    public function scopeHighDemand(Builder $query, int $minJobs = 10): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $minJobs)
            ->orderByDesc('jobs_count')
        ;
    }

    /**
     * Scope for technology categories.
     */
    public function scopeTechnology(Builder $query): Builder
    {
        return $query->where('name', 'like', '%technology%')
            ->orWhere('name', 'like', '%IT%')
            ->orWhere('name', 'like', '%software%')
            ->orWhere('name', 'like', '%computer%')
            ->orWhere('name', 'like', '%programming%')
        ;
    }

    /**
     * Scope for healthcare categories.
     */
    public function scopeHealthcare(Builder $query): Builder
    {
        return $query->where('name', 'like', '%healthcare%')
            ->orWhere('name', 'like', '%medical%')
            ->orWhere('name', 'like', '%health%')
            ->orWhere('name', 'like', '%nursing%')
            ->orWhere('name', 'like', '%biomedical%')
        ;
    }

    /**
     * Scope for finance categories.
     */
    public function scopeFinance(Builder $query): Builder
    {
        return $query->where('name', 'like', '%finance%')
            ->orWhere('name', 'like', '%accounting%')
            ->orWhere('name', 'like', '%banking%')
            ->orWhere('name', 'like', '%actuaries%')
        ;
    }

    /**
     * Scope for education categories.
     */
    public function scopeEducation(Builder $query): Builder
    {
        return $query->where('name', 'like', '%education%')
            ->orWhere('name', 'like', '%teaching%')
            ->orWhere('name', 'like', '%training%')
            ->orWhere('name', 'like', '%coaches%')
        ;
    }

    /**
     * Scope for engineering categories.
     */
    public function scopeEngineering(Builder $query): Builder
    {
        return $query->where('name', 'like', '%engineer%')
            ->orWhere('name', 'like', '%civil%')
            ->orWhere('name', 'like', '%biomedical%')
        ;
    }

    /**
     * Scope for parent categories only.
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for child categories only.
     */
    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Check if job category is in high demand.
     */
    public function isHighDemand(): bool
    {
        return $this->jobs()->count() >= 50;
    }

    /**
     * Check if category is technology-related.
     */
    public function isTechnology(): bool
    {
        return 'technology' === $this->getCategoryType();
    }

    /**
     * Check if category is healthcare-related.
     */
    public function isHealthcare(): bool
    {
        return 'healthcare' === $this->getCategoryType();
    }

    /**
     * Check if category is finance-related.
     */
    public function isFinance(): bool
    {
        return 'finance' === $this->getCategoryType();
    }

    /**
     * Check if category has parent.
     */
    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get related job categories.
     */
    public function getRelatedCategories(int $limit = 5): Collection
    {
        return cache()->remember("job_category.{$this->id}.related", 3600, function () use ($limit) {
            return static::where('id', '!=', $this->id)
                ->active()
                ->withCount('jobs')
                ->orderByDesc('jobs_count')
                ->limit($limit)
                ->get()
            ;
        });
    }

    /**
     * Get category hierarchy path.
     */
    public function getHierarchyPath(): string
    {
        $path = [$this->name];

        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get all descendants (children and their children).
     */
    public function getAllDescendants(): Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Get category statistics.
     */
    public function getCategoryStats(): array
    {
        return cache()->remember("job_category.{$this->id}.stats", 3600, function () {
            $jobs = $this->jobs();

            return [
                'total_jobs' => $jobs->count(),
                'active_jobs' => $jobs->where('is_active', true)->count(),
                'featured_jobs' => $jobs->where('is_featured', true)->count(),
                'recent_jobs' => $jobs->where('created_at', '>=', now()->subDays(30))->count(),
                'avg_salary' => $jobs->whereNotNull('salary_from')->avg('salary_from'),
                'companies_count' => $jobs->distinct('company_id')->count('company_id'),
                'demand_level' => $this->getDemandLevel(),
                'category_type' => $this->getCategoryType(),
            ];
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when job category is updated
        static::updated(function ($jobCategory) {
            cache()->forget("job_category.{$jobCategory->id}");
            cache()->forget('job_categories.popular');
            cache()->forget('job_categories.trending');
            cache()->forget('job_categories.featured');
            cache()->tags(['job_categories', 'job_category-'.$jobCategory->id])->flush();
        });

        // Clear cache when job category is deleted
        static::deleted(function ($jobCategory) {
            cache()->forget("job_category.{$jobCategory->id}");
            cache()->forget('job_categories.popular');
            cache()->forget('job_categories.trending');
            cache()->forget('job_categories.featured');
            cache()->tags(['job_categories', 'job_category-'.$jobCategory->id])->flush();
        });
    }

    /**
     * Get demand level based on usage.
     */
    private function getDemandLevel(): string
    {
        $jobsCount = $this->jobs()->count();

        return match (true) {
            $jobsCount >= 100 => __('job_category.high_demand'),
            $jobsCount >= 50 => __('job_category.medium_demand'),
            $jobsCount >= 10 => __('job_category.low_demand'),
            default => __('job_category.minimal_demand')
        };
    }

    /**
     * Get category type classification.
     */
    private function getCategoryType(): string
    {
        $name = strtolower($this->name);

        return match (true) {
            str_contains($name, 'technology') || str_contains($name, 'it') || str_contains($name, 'software') || str_contains($name, 'computer') => 'technology',
            str_contains($name, 'healthcare') || str_contains($name, 'medical') || str_contains($name, 'health') || str_contains($name, 'biomedical') => 'healthcare',
            str_contains($name, 'finance') || str_contains($name, 'accounting') || str_contains($name, 'banking') || str_contains($name, 'actuaries') => 'finance',
            str_contains($name, 'education') || str_contains($name, 'teaching') || str_contains($name, 'training') || str_contains($name, 'coaches') => 'education',
            str_contains($name, 'engineer') || str_contains($name, 'civil') => 'engineering',
            str_contains($name, 'marketing') || str_contains($name, 'sales') => 'marketing',
            str_contains($name, 'design') || str_contains($name, 'creative') => 'creative',
            str_contains($name, 'legal') || str_contains($name, 'law') => 'legal',
            str_contains($name, 'research') || str_contains($name, 'development') => 'research',
            str_contains($name, 'operations') || str_contains($name, 'management') => 'operations',
            default => 'general'
        };
    }
}
