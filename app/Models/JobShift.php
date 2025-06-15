<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class JobShift.
 *
 * @property int                             $id
 * @property string                          $shift
 * @property string                          $description
 * @property bool                            $is_default
 * @property bool                            $is_active
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property Collection|Job[]                $jobs
 * @property null|int                        $jobs_count
 * @property mixed                           $usage_count
 * @property mixed                           $formatted_usage_stats
 *
 * @method static Builder|JobShift newModelQuery()
 * @method static Builder|JobShift newQuery()
 * @method static Builder|JobShift query()
 * @method static Builder|JobShift whereCreatedAt($value)
 * @method static Builder|JobShift whereId($value)
 * @method static Builder|JobShift whereShift($value)
 * @method static Builder|JobShift whereDescription($value)
 * @method static Builder|JobShift whereIsDefault($value)
 * @method static Builder|JobShift whereIsActive($value)
 * @method static Builder|JobShift whereUpdatedAt($value)
 * @method static Builder|JobShift active()
 * @method static Builder|JobShift inactive()
 * @method static Builder|JobShift default()
 * @method static Builder|JobShift custom()
 * @method static Builder|JobShift withJobs()
 * @method static Builder|JobShift withActiveJobs()
 * @method static Builder|JobShift search(string $term)
 * @method static Builder|JobShift popular(int $limit = 10)
 * @method static Builder|JobShift alphabetical()
 * @method static Builder|JobShift recent(int $days = 30)
 * @method static Builder|JobShift trending()
 * @method static Builder|JobShift minUsage(int $count = 1)
 * @method static Builder|JobShift highDemand(int $minJobs = 10)
 * @method static Builder|JobShift dayShift()
 * @method static Builder|JobShift nightShift()
 * @method static Builder|JobShift morningShift()
 * @method static Builder|JobShift eveningShift()
 * @method static Builder|JobShift flexibleHours()
 * @method static Builder|JobShift fixedShift()
 * @method static Builder|JobShift rotatingShift()
 *
 * @mixin \Eloquent
 */
class JobShift extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift',
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
        'start_time',
        'end_time',
        'duration_hours',
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
        'duration_hours' => 'decimal:2',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['shift', 'description', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("job_shift.{$this->id}.usage_count", 3600, function () {
            return $this->jobs()->count();
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("job_shift.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->where('is_active', true)->count(),
                'total_usage' => $this->usage_count,
                'demand_level' => $this->getDemandLevel(),
                'shift_type' => $this->getShiftType(),
            ];
        });
    }

    /**
     * Relationship: Jobs.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_shift_id');
    }

    /**
     * Scope for active job shifts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive job shifts.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default job shifts.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom job shifts.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for job shifts with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->has('jobs');
    }

    /**
     * Scope for job shifts with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching job shifts.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('shift', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
        ;
    }

    /**
     * Scope for recent job shifts.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for popular job shifts (with most jobs).
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
        return $query->orderBy('shift', 'asc');
    }

    /**
     * Scope for trending job shifts.
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
     * Scope for job shifts with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $count)
        ;
    }

    /**
     * Scope for high demand job shifts.
     */
    public function scopeHighDemand(Builder $query, int $minJobs = 10): Builder
    {
        return $query->withCount('jobs')
            ->having('jobs_count', '>=', $minJobs)
            ->orderByDesc('jobs_count')
        ;
    }

    /**
     * Scope for day shift.
     */
    public function scopeDayShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%day%')
            ->orWhere('shift', 'like', '%morning%')
            ->orWhere('shift', 'like', '%first%')
        ;
    }

    /**
     * Scope for night shift.
     */
    public function scopeNightShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%night%')
            ->orWhere('shift', 'like', '%third%')
            ->orWhere('shift', 'like', '%graveyard%')
        ;
    }

    /**
     * Scope for morning shift.
     */
    public function scopeMorningShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%morning%')
            ->orWhere('shift', 'like', '%first%')
        ;
    }

    /**
     * Scope for evening shift.
     */
    public function scopeEveningShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%evening%')
            ->orWhere('shift', 'like', '%second%')
        ;
    }

    /**
     * Scope for flexible hours.
     */
    public function scopeFlexibleHours(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%flexible%')
            ->orWhere('shift', 'like', '%flex%')
            ->orWhere('shift', 'like', '%variable%')
        ;
    }

    /**
     * Scope for fixed shift.
     */
    public function scopeFixedShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%fixed%')
            ->orWhere('shift', 'like', '%regular%')
            ->orWhere('shift', 'like', '%standard%')
        ;
    }

    /**
     * Scope for rotating shift.
     */
    public function scopeRotatingShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%rotating%')
            ->orWhere('shift', 'like', '%rotation%')
            ->orWhere('shift', 'like', '%alternating%')
        ;
    }

    /**
     * Check if job shift is in high demand.
     */
    public function isHighDemand(): bool
    {
        return $this->jobs()->count() >= 25;
    }

    /**
     * Check if shift is day shift.
     */
    public function isDayShift(): bool
    {
        return 'day' === $this->getShiftType();
    }

    /**
     * Check if shift is night shift.
     */
    public function isNightShift(): bool
    {
        return 'night' === $this->getShiftType();
    }

    /**
     * Check if shift is flexible.
     */
    public function isFlexible(): bool
    {
        return 'flexible' === $this->getShiftType();
    }

    /**
     * Check if shift is rotating.
     */
    public function isRotating(): bool
    {
        return 'rotating' === $this->getShiftType();
    }

    /**
     * Get related job shifts.
     */
    public function getRelatedShifts(int $limit = 5): Collection
    {
        return cache()->remember("job_shift.{$this->id}.related", 3600, function () use ($limit) {
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
     * Get shift duration in hours.
     */
    public function getDurationHours(): ?float
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);

            // Handle overnight shifts
            if ($end->lt($start)) {
                $end->addDay();
            }

            return $start->diffInHours($end);
        }

        return $this->duration_hours;
    }

    /**
     * Check if shift is overnight.
     */
    public function isOvernightShift(): bool
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);

            return $end->lt($start);
        }

        return $this->isNightShift();
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when job shift is updated
        static::updated(function ($jobShift) {
            cache()->forget("job_shift.{$jobShift->id}");
            cache()->forget('job_shifts.popular');
            cache()->forget('job_shifts.trending');
            cache()->tags(['job_shifts', 'job_shift-'.$jobShift->id])->flush();
        });

        // Clear cache when job shift is deleted
        static::deleted(function ($jobShift) {
            cache()->forget("job_shift.{$jobShift->id}");
            cache()->forget('job_shifts.popular');
            cache()->forget('job_shifts.trending');
            cache()->tags(['job_shifts', 'job_shift-'.$jobShift->id])->flush();
        });
    }

    /**
     * Get demand level based on usage.
     */
    private function getDemandLevel(): string
    {
        $jobsCount = $this->jobs()->count();

        return match (true) {
            $jobsCount >= 50 => __('job_shift.high_demand'),
            $jobsCount >= 25 => __('job_shift.medium_demand'),
            $jobsCount >= 5 => __('job_shift.low_demand'),
            default => __('job_shift.minimal_demand')
        };
    }

    /**
     * Get shift type classification.
     */
    private function getShiftType(): string
    {
        $shift = strtolower($this->shift);

        return match (true) {
            str_contains($shift, 'day') || str_contains($shift, 'morning') || str_contains($shift, 'first') => 'day',
            str_contains($shift, 'night') || str_contains($shift, 'third') || str_contains($shift, 'graveyard') => 'night',
            str_contains($shift, 'evening') || str_contains($shift, 'second') => 'evening',
            str_contains($shift, 'flexible') || str_contains($shift, 'flex') => 'flexible',
            str_contains($shift, 'rotating') || str_contains($shift, 'rotation') => 'rotating',
            str_contains($shift, 'fixed') || str_contains($shift, 'regular') => 'fixed',
            str_contains($shift, 'split') => 'split',
            str_contains($shift, 'on-call') || str_contains($shift, 'oncall') => 'on-call',
            str_contains($shift, 'weekend') => 'weekend',
            default => 'standard'
        };
    }
}
