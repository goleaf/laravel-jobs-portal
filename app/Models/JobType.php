<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class JobType
 *
 * @version June 19, 2020, 7:50 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_count
 * @property-read mixed $formatted_usage_stats
 * @property-read mixed $popularity_level
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
 * @method static Builder|JobType freelance()
 * @method static Builder|JobType remote()
 *
 * @mixin \Eloquent
 */
class JobType extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Default eager loading for performance
     */
    protected $with = [];

    /**
     * Validation rules with multilingual support
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:job_types,name|max:150',
        'description' => 'nullable|string|max:500',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public $table = 'job_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected $appends = [
        'usage_count',
        'formatted_usage_stats',
        'popularity_level'
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
            'name' => 'string',
            'description' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
            cache()->forget("job_types.popular");
            cache()->forget("job_types.trending");
            cache()->tags(['job_types', 'job_type-' . $jobType->id])->flush();
        });

        // Clear cache when job type is deleted
        static::deleted(function ($jobType) {
            cache()->forget("job_type.{$jobType->id}");
            cache()->forget("job_types.popular");
            cache()->forget("job_types.trending");
            cache()->tags(['job_types', 'job_type-' . $jobType->id])->flush();
        });
    }

    /**
     * Activity log options
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
            return $this->jobs_count;
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
                'active_jobs' => $this->jobs()->active()->count(),
                'featured_jobs' => $this->jobs()->featured()->count(),
                'total_usage' => $this->usage_count,
                'popularity_level' => $this->popularity_level,
            ];
        });
    }

    /**
     * Get popularity level based on usage.
     */
    public function getPopularityLevelAttribute(): string
    {
        $jobsCount = $this->jobs()->count();
        
        return match (true) {
            $jobsCount >= 1000 => __('job_type.extremely_popular'),
            $jobsCount >= 500 => __('job_type.very_popular'),
            $jobsCount >= 100 => __('job_type.popular'),
            $jobsCount >= 20 => __('job_type.moderate'),
            $jobsCount >= 5 => __('job_type.limited'),
            default => __('job_type.minimal')
        };
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_type_id');
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
     * Scope for job types without jobs.
     */
    public function scopeWithoutJobs(Builder $query): Builder
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope for job types with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 1);
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
     * Scope for old job types.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
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
     * Scope for full-time job types.
     */
    public function scopeFullTime(Builder $query): Builder
    {
        return $query->where('name', 'like', '%full%time%')
                    ->orWhere('name', 'like', '%full-time%')
                    ->orWhere('name', 'like', '%permanent%');
    }

    /**
     * Scope for part-time job types.
     */
    public function scopePartTime(Builder $query): Builder
    {
        return $query->where('name', 'like', '%part%time%')
                    ->orWhere('name', 'like', '%part-time%');
    }

    /**
     * Scope for contract job types.
     */
    public function scopeContract(Builder $query): Builder
    {
        return $query->where('name', 'like', '%contract%')
                    ->orWhere('name', 'like', '%temporary%')
                    ->orWhere('name', 'like', '%freelance%');
    }

    /**
     * Scope for remote job types.
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('name', 'like', '%remote%')
                    ->orWhere('name', 'like', '%work from home%')
                    ->orWhere('name', 'like', '%telecommute%');
    }

    /**
     * Scope for internship job types.
     */
    public function scopeInternship(Builder $query): Builder
    {
        return $query->where('name', 'like', '%intern%')
                    ->orWhere('name', 'like', '%trainee%')
                    ->orWhere('name', 'like', '%apprentice%');
    }

    /**
     * Check if job type is popular.
     */
    public function isPopular(): bool
    {
        return $this->jobs()->count() >= 100;
    }

    /**
     * Check if job type supports remote work.
     */
    public function supportsRemote(): bool
    {
        return str_contains(strtolower($this->name), 'remote') ||
               str_contains(strtolower($this->name), 'work from home') ||
               str_contains(strtolower($this->description ?? ''), 'remote');
    }

    /**
     * Get job type category.
     */
    public function getCategory(): string
    {
        $name = strtolower($this->name);
        
        return match (true) {
            str_contains($name, 'full') && str_contains($name, 'time') => __('job_type.category.full_time'),
            str_contains($name, 'part') && str_contains($name, 'time') => __('job_type.category.part_time'),
            str_contains($name, 'contract') || str_contains($name, 'temporary') => __('job_type.category.contract'),
            str_contains($name, 'freelance') || str_contains($name, 'consultant') => __('job_type.category.freelance'),
            str_contains($name, 'intern') => __('job_type.category.internship'),
            str_contains($name, 'remote') => __('job_type.category.remote'),
            default => __('job_type.category.other')
        };
    }

    /**
     * Get average salary for this job type.
     */
    public function getAverageSalary(): float
    {
        return cache()->remember("job_type.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                        ->where('hide_salary', false)
                        ->whereNotNull('salary_from')
                        ->whereNotNull('salary_to')
                        ->avg(\DB::raw('(salary_from + salary_to) / 2')) ?? 0.0;
        });
    }

    /**
     * Get top companies for this job type.
     */
    public function getTopCompanies(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return cache()->remember("job_type.{$this->id}.top_companies", 3600, function () use ($limit) {
            return $this->jobs()
                        ->with('company')
                        ->select('company_id', \DB::raw('count(*) as jobs_count'))
                        ->groupBy('company_id')
                        ->orderByDesc('jobs_count')
                        ->limit($limit)
                        ->get()
                        ->pluck('company');
        });
    }
}
