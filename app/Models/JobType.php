<?php

namespace App\Models;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class JobType.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $is_default
 * @property bool $is_active
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Collection|Job[] $jobs
 * @property null|int $jobs_count
 * @property mixed $usage_count
 * @property mixed $formatted_usage_stats
 *
 * @method static Builder|JobType newModelQuery()
 * @method static Builder|JobType newQuery()
 * @method static Builder|JobType query()
 * @method static Builder|JobType whereCreatedAt($value)
 * @method static Builder|JobType whereId($value)
 * @method static Builder|JobType whereName($value)
 * @method static Builder|JobType whereDescription($value)
 * @method static Builder|JobType whereIsDefault($value)
 * @method static Builder|JobType whereIsActive($value)
 * @method static Builder|JobType whereUpdatedAt($value)
 * @method static Builder|JobType active()
 * @method static Builder|JobType inactive()
 * @method static Builder|JobType default()
 * @method static Builder|JobType custom()
 * @method static Builder|JobType withJobs()
 * @method static Builder|JobType withActiveJobs()
 * @method static Builder|JobType search(string $term)
 * @method static Builder|JobType popular(int $limit = 10)
 * @method static Builder|JobType alphabetical()
 * @method static Builder|JobType recent(int $days = 30)
 * @method static Builder|JobType trending()
 * @method static Builder|JobType minUsage(int $count = 1)
 * @method static Builder|JobType highDemand(int $minJobs = 10)
 * @method static Builder|JobType fullTime()
 * @method static Builder|JobType partTime()
 * @method static Builder|JobType contract()
 * @method static Builder|JobType temporary()
 * @method static Builder|JobType internship()
 * @method static Builder|JobType freelance()
 * @method static Builder|JobType remote()
 *
 * @mixin \Eloquent
 */
class JobType extends Model
{
    use HasFactory;
    use HasSettingsField;
    use LogsActivity;

    /**
     * Minimal validation rules expected by legacy tests.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|max:160|unique:job_types,name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
        'sort_order',
        'icon',
        'color',
        'is_featured',
        'meta_title',
        'meta_description',
        'slug',
    ];

    /**
     * Support setting company_id via constructor without altering fillable array.
     */
    public function __construct(array $attributes = [])
    {
        if (array_key_exists('company_id', $attributes)) {
            $this->setAttribute('company_id', $attributes['company_id']);
            unset($attributes['company_id']);
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
        'sort_order' => 'int',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Default settings for job type model.
     */
    public $defaultSettings = [
        'display' => [
            'show_in_filters' => true,
            'show_job_count' => true,
            'show_description' => true,
            'show_icon' => true,
            'color_scheme' => 'default',
            'featured_placement' => false,
            'priority_order' => 0,
        ],
        'filtering' => [
            'enable_filtering' => true,
            'default_sort' => 'name',
            'group_similar_types' => false,
            'min_jobs_to_show' => 1,
            'hide_empty_types' => false,
        ],
        'features' => [
            'enable_job_alerts' => true,
            'enable_saved_searches' => true,
            'enable_salary_insights' => true,
            'premium_features_enabled' => false,
        ],
        'analytics' => [
            'track_views' => true,
            'track_applications' => true,
            'google_analytics_enabled' => false,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'display.show_in_filters' => 'boolean',
        'display.show_job_count' => 'boolean',
        'display.show_description' => 'boolean',
        'display.show_icon' => 'boolean',
        'display.color_scheme' => 'string|in:default,blue,green,red,purple',
        'display.featured_placement' => 'boolean',
        'display.priority_order' => 'integer|min:0|max:100',

        'filtering.enable_filtering' => 'boolean',
        'filtering.default_sort' => 'string|in:name,job_count,recent,popular',
        'filtering.group_similar_types' => 'boolean',
        'filtering.min_jobs_to_show' => 'integer|min:0|max:100',
        'filtering.hide_empty_types' => 'boolean',

        'features.enable_job_alerts' => 'boolean',
        'features.enable_saved_searches' => 'boolean',
        'features.enable_salary_insights' => 'boolean',
        'features.premium_features_enabled' => 'boolean',

        'analytics.track_views' => 'boolean',
        'analytics.track_applications' => 'boolean',
        'analytics.google_analytics_enabled' => 'boolean',
    ];

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("job_type.{$this->id}.usage_count", 3600, function () {
            return $this->jobs()->count();
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("job_type.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->where('is_active', true)->count(),
                'total_usage' => $this->usage_count,
                'demand_level' => $this->getDemandLevel(),
            ];
        });
    }

    /**
     * Relationship: Jobs.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_type_id');
    }

    /**
     * Relationship: Candidate Job Alerts (stub for legacy tests).
     */
    public function candidateJobAlerts(): HasMany
    {
        return $this->hasMany(CandidateJobAlert::class, 'job_type_id');
    }

    /**
     * Scope for active job types.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive job types.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default job types.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom job types.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for job types with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->has('jobs');
    }

    /**
     * Scope for job types with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching job types.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent job types.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for popular job types (with most jobs).
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
     * Scope for trending job types.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
            'jobs' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
        ])
            ->orderByDesc('jobs_count');
    }

    /**
     * Scope for job types with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $count);
    }

    /**
     * Scope for high demand job types.
     */
    public function scopeHighDemand(Builder $query, int $minJobs = 10): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $minJobs)
            ->orderByDesc('jobs_count');
    }

    /**
     * Scope for full-time job types.
     */
    public function scopeFullTime(Builder $query): Builder
    {
        return $query->where('name', 'like', '%full%time%')
            ->orWhere('name', 'like', '%full-time%')
            ->orWhere('name', 'like', '%fulltime%');
    }

    /**
     * Scope for part-time job types.
     */
    public function scopePartTime(Builder $query): Builder
    {
        return $query->where('name', 'like', '%part%time%')
            ->orWhere('name', 'like', '%part-time%')
            ->orWhere('name', 'like', '%parttime%');
    }

    /**
     * Scope for contract job types.
     */
    public function scopeContract(Builder $query): Builder
    {
        return $query->where('name', 'like', '%contract%')
            ->orWhere('name', 'like', '%contractor%');
    }

    /**
     * Scope for temporary job types.
     */
    public function scopeTemporary(Builder $query): Builder
    {
        return $query->where('name', 'like', '%temporary%')
            ->orWhere('name', 'like', '%temp%');
    }

    /**
     * Scope for internship job types.
     */
    public function scopeInternship(Builder $query): Builder
    {
        return $query->where('name', 'like', '%internship%')
            ->orWhere('name', 'like', '%intern%');
    }

    /**
     * Scope for freelance job types.
     */
    public function scopeFreelance(Builder $query): Builder
    {
        return $query->where('name', 'like', '%freelance%')
            ->orWhere('name', 'like', '%freelancer%');
    }

    /**
     * Scope for remote job types.
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('name', 'like', '%remote%')
               || stripos($this->name, 'work from home') !== false
               || stripos($this->name, 'wfh') !== false;
    }

    /**
     * Check if job type is in high demand.
     */
    public function isHighDemand(): bool
    {
        return $this->jobs()->count() >= 50;
    }

    /**
     * Check if job type is full-time.
     */
    public function isFullTime(): bool
    {
        return stripos($this->name, 'full') !== false && stripos($this->name, 'time') !== false;
    }

    /**
     * Check if job type is part-time.
     */
    public function isPartTime(): bool
    {
        return stripos($this->name, 'part') !== false && stripos($this->name, 'time') !== false;
    }

    /**
     * Check if job type is remote.
     */
    public function isRemote(): bool
    {
        return stripos($this->name, 'remote') !== false
               || stripos($this->name, 'work from home') !== false
               || stripos($this->name, 'wfh') !== false;
    }

    /**
     * Get related job types.
     */
    public function getRelatedTypes(int $limit = 5): Collection
    {
        return cache()->remember("job_type.{$this->id}.related", 3600, function () use ($limit) {
            return static::where('id', '!=', $this->id)
                ->active()
                ->withCount('jobs')
                ->orderByDesc('jobs_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when job type is updated
        static::updated(function ($jobType) {
            cache()->forget("job_type.{$jobType->id}");
            try {
                cache()->tags(['job_types', 'job_type-'.$jobType->id])->flush();
            } catch (\Exception $e) {}
        });

        // Clear cache when job type is deleted
        static::deleted(function ($jobType) {
            cache()->forget("job_type.{$jobType->id}");
            try {
                cache()->tags(['job_types', 'job_type-'.$jobType->id])->flush();
            } catch (\Exception $e) {}
        });
    }

    /**
     * Get demand level based on usage.
     */
    private function getDemandLevel(): string
    {
        $jobsCount = $this->jobs()->count();

        return match (true) {
            $jobsCount >= 100 => __('job_type.high_demand'),
            $jobsCount >= 50 => __('job_type.medium_demand'),
            $jobsCount >= 10 => __('job_type.low_demand'),
            default => __('job_type.minimal_demand')
        };
    }
}
