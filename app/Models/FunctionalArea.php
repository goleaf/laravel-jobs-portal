<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class FunctionalArea.
 *
 * @version July 4, 2020, 7:26 am UTC
 *
 * @property int                    $id
 * @property string                 $name
 * @property null|string            $description
 * @property bool                   $is_default
 * @property bool                   $is_active
 * @property null|Carbon            $created_at
 * @property null|Carbon            $updated_at
 * @property Collection|Job[]       $jobs
 * @property null|int               $jobs_count
 * @property Candidate[]|Collection $candidates
 * @property null|int               $candidates_count
 * @property mixed                  $usage_count
 * @property mixed                  $formatted_usage_stats
 *
 * @method static Builder|FunctionalArea newModelQuery()
 * @method static Builder|FunctionalArea newQuery()
 * @method static Builder|FunctionalArea query()
 * @method static Builder|FunctionalArea whereCreatedAt($value)
 * @method static Builder|FunctionalArea whereId($value)
 * @method static Builder|FunctionalArea whereName($value)
 * @method static Builder|FunctionalArea whereDescription($value)
 * @method static Builder|FunctionalArea whereIsDefault($value)
 * @method static Builder|FunctionalArea whereIsActive($value)
 * @method static Builder|FunctionalArea whereUpdatedAt($value)
 * @method static Builder|FunctionalArea active()
 * @method static Builder|FunctionalArea inactive()
 * @method static Builder|FunctionalArea default()
 * @method static Builder|FunctionalArea custom()
 * @method static Builder|FunctionalArea withJobs()
 * @method static Builder|FunctionalArea withActiveJobs()
 * @method static Builder|FunctionalArea withCandidates()
 * @method static Builder|FunctionalArea withActiveCandidates()
 * @method static Builder|FunctionalArea search(string $term)
 * @method static Builder|FunctionalArea popular(int $limit = 10)
 * @method static Builder|FunctionalArea alphabetical()
 * @method static Builder|FunctionalArea recent(int $days = 30)
 * @method static Builder|FunctionalArea trending()
 * @method static Builder|FunctionalArea minUsage(int $count = 1)
 * @method static Builder|FunctionalArea highDemand(int $minJobs = 10)
 *
 * @mixin \Eloquent
 */
class FunctionalArea extends Model
{
    use HasFactory;
    use LogsActivity;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include featured records.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("functional_area.{$this->id}.usage_count", 3600, function () {
            return $this->jobs_count + $this->candidates_count;
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("functional_area.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'candidates' => $this->candidates()->count(),
                'total_usage' => $this->usage_count,
                'demand_level' => $this->getDemandLevel(),
            ];
        });
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'functional_area_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'functional_area_id');
    }

    /**
     * Scope for active functional areas.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive functional areas.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default functional areas.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom functional areas.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for functional areas with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->has('jobs');
    }

    /**
     * Scope for functional areas without jobs.
     */
    public function scopeWithoutJobs(Builder $query): Builder
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope for functional areas with candidates.
     */
    public function scopeWithCandidates(Builder $query): Builder
    {
        return $query->has('candidates');
    }

    /**
     * Scope for searching functional areas.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
        ;
    }

    /**
     * Scope for recent functional areas.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old functional areas.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular functional areas (with most jobs).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount('jobs')
            ->orderBy('jobs_count', 'desc')
            ->limit($limit)
        ;
    }

    /**
     * Scope for ordered functional areas.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for technology areas.
     */
    public function scopeTechnology(Builder $query): Builder
    {
        return $query->where('name', 'like', '%technology%')
            ->orWhere('name', 'like', '%IT%')
            ->orWhere('name', 'like', '%software%')
        ;
    }

    /**
     * Scope for business areas.
     */
    public function scopeBusiness(Builder $query): Builder
    {
        return $query->where('name', 'like', '%business%')
            ->orWhere('name', 'like', '%management%')
            ->orWhere('name', 'like', '%admin%')
        ;
    }

    /**
     * Scope for trending functional areas.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
            'jobs' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
            'candidates' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
        ])
            ->orderByDesc('jobs_count')
            ->orderByDesc('candidates_count')
        ;
    }

    /**
     * Scope for functional areas with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount(['jobs', 'candidates'])
            ->havingRaw('(jobs_count + candidates_count) >= ?', [$count])
        ;
    }

    /**
     * Scope for high demand functional areas.
     */
    public function scopeHighDemand(Builder $query, int $minJobs = 10): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $minJobs)
            ->orderByDesc('jobs_count')
        ;
    }

    /**
     * Check if functional area is in high demand.
     */
    public function isHighDemand(): bool
    {
        return $this->jobs()->count() >= 20;
    }

    /**
     * Get related functional areas.
     */
    public function getRelatedAreas(int $limit = 5): Collection
    {
        return cache()->remember("functional_area.{$this->id}.related", 3600, function () use ($limit) {
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
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when functional area is updated
        static::updated(function ($functionalArea) {
            cache()->forget("functional_area.{$functionalArea->id}");
            cache()->forget('functional_areas.popular');
            cache()->forget('functional_areas.trending');
            cache()->tags(['functional_areas', 'functional_area-'.$functionalArea->id])->flush();
        });

        // Clear cache when functional area is deleted
        static::deleted(function ($functionalArea) {
            cache()->forget("functional_area.{$functionalArea->id}");
            cache()->forget('functional_areas.popular');
            cache()->forget('functional_areas.trending');
            cache()->tags(['functional_areas', 'functional_area-'.$functionalArea->id])->flush();
        });
    }

    /**
     * Get demand level based on usage.
     */
    private function getDemandLevel(): string
    {
        $jobsCount = $this->jobs()->count();

        return match (true) {
            $jobsCount >= 50 => __('functional_area.high_demand'),
            $jobsCount >= 20 => __('functional_area.medium_demand'),
            $jobsCount >= 5 => __('functional_area.low_demand'),
            default => __('functional_area.minimal_demand')
        };
    }
}
