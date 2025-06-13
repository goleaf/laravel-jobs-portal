<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class JobCategory
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_featured
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_count
 * @property-read mixed $formatted_usage_stats
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
    use HasFactory, LogsActivity;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when job category is updated
        static::updated(function ($jobCategory) {
            cache()->forget("job_category.{$jobCategory->id}");
            cache()->forget("job_categories.popular");
            cache()->forget("job_categories.trending");
            cache()->forget("job_categories.featured");
            cache()->tags(['job_categories', 'job_category-' . $jobCategory->id])->flush();
        });

        // Clear cache when job category is deleted
        static::deleted(function ($jobCategory) {
            cache()->forget("job_category.{$jobCategory->id}");
            cache()->forget("job_categories.popular");
            cache()->forget("job_categories.trending");
            cache()->forget("job_categories.featured");
            cache()->tags(['job_categories', 'job_category-' . $jobCategory->id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'is_featured', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
     * Relationship: Jobs
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_category_id');
    }

    /**
     * Relationship: Parent Category
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'parent_id');
    }

    /**
     * Relationship: Child Categories
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
                    ->orWhere('description', 'like', "%{$term}%");
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
                    ->limit($limit);
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
                        }
                    ])
                    ->orderByDesc('jobs_count');
    }

    /**
     * Scope for job categories with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount('jobs')
                    ->having('jobs_count', '>=', $count);
    }

    /**
     * Scope for high demand job categories.
     */
    public function scopeHighDemand(Builder $query, int $minJobs = 10): Builder
    {
        return $query->withCount('jobs')
                    ->having('jobs_count', '>=', $minJobs)
                    ->orderByDesc('jobs_count');
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
                    ->orWhere('name', 'like', '%programming%');
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
                    ->orWhere('name', 'like', '%biomedical%');
    }

    /**
     * Scope for finance categories.
     */
    public function scopeFinance(Builder $query): Builder
    {
        return $query->where('name', 'like', '%finance%')
                    ->orWhere('name', 'like', '%accounting%')
                    ->orWhere('name', 'like', '%banking%')
                    ->orWhere('name', 'like', '%actuaries%');
    }

    /**
     * Scope for education categories.
     */
    public function scopeEducation(Builder $query): Builder
    {
        return $query->where('name', 'like', '%education%')
                    ->orWhere('name', 'like', '%teaching%')
                    ->orWhere('name', 'like', '%training%')
                    ->orWhere('name', 'like', '%coaches%');
    }

    /**
     * Scope for engineering categories.
     */
    public function scopeEngineering(Builder $query): Builder
    {
        return $query->where('name', 'like', '%engineer%')
                    ->orWhere('name', 'like', '%civil%')
                    ->orWhere('name', 'like', '%biomedical%');
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
        return $this->getCategoryType() === 'technology';
    }

    /**
     * Check if category is healthcare-related.
     */
    public function isHealthcare(): bool
    {
        return $this->getCategoryType() === 'healthcare';
    }

    /**
     * Check if category is finance-related.
     */
    public function isFinance(): bool
    {
        return $this->getCategoryType() === 'finance';
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
    public function getRelatedCategories(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return cache()->remember("job_category.{$this->id}.related", 3600, function () use ($limit) {
            return static::where('id', '!=', $this->id)
                          ->active()
                          ->withCount('jobs')
                          ->orderByDesc('jobs_count')
                          ->limit($limit)
                          ->get();
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
    public function getAllDescendants(): \Illuminate\Database\Eloquent\Collection
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
} 