<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class JobShift
 *
 * @version June 19, 2020, 8:51 am UTC
 *
 * @property int $id
 * @property string $shift
 * @property string|null $description
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int|null $duration_hours
 * @property bool $is_default
 * @property bool $is_active
 * @property bool $is_flexible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_count
 * @property-read mixed $formatted_usage_stats
 * @property-read mixed $shift_category
 * @property-read mixed $formatted_time_range
 * @property-read mixed $is_night_shift
 *
 * @method static Builder|JobShift newModelQuery()
 * @method static Builder|JobShift newQuery()
 * @method static Builder|JobShift query()
 * @method static Builder|JobShift whereCreatedAt($value)
 * @method static Builder|JobShift whereId($value)
 * @method static Builder|JobShift whereShift($value)
 * @method static Builder|JobShift whereDescription($value)
 * @method static Builder|JobShift whereStartTime($value)
 * @method static Builder|JobShift whereEndTime($value)
 * @method static Builder|JobShift whereDurationHours($value)
 * @method static Builder|JobShift whereIsDefault($value)
 * @method static Builder|JobShift whereIsActive($value)
 * @method static Builder|JobShift whereIsFlexible($value)
 * @method static Builder|JobShift whereUpdatedAt($value)
 * @method static Builder|JobShift active()
 * @method static Builder|JobShift inactive()
 * @method static Builder|JobShift default()
 * @method static Builder|JobShift custom()
 * @method static Builder|JobShift flexible()
 * @method static Builder|JobShift fixed()
 * @method static Builder|JobShift withJobs()
 * @method static Builder|JobShift withActiveJobs()
 * @method static Builder|JobShift search(string $term)
 * @method static Builder|JobShift popular(int $limit = 10)
 * @method static Builder|JobShift alphabetical()
 * @method static Builder|JobShift recent(int $days = 30)
 * @method static Builder|JobShift trending()
 * @method static Builder|JobShift dayShift()
 * @method static Builder|JobShift nightShift()
 * @method static Builder|JobShift eveningShift()
 * @method static Builder|JobShift weekendShift()
 * @method static Builder|JobShift standardHours()
 * @method static Builder|JobShift extendedHours()
 * @method static Builder|JobShift byDuration(int $hours)
 * @method static Builder|JobShift minDuration(int $hours)
 * @method static Builder|JobShift maxDuration(int $hours)
 *
 * @mixin \Eloquent
 */
class JobShift extends Model
{
    use HasFactory, LogsActivity;

    public $table = 'job_shifts';

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
        'shift' => 'required|unique:job_shifts,shift|max:150',
        'description' => 'nullable|string|max:500',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'duration_hours' => 'nullable|integer|min:1|max:24',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_flexible' => 'boolean',
    ];

    public $fillable = [
        'shift',
        'description',
        'start_time',
        'end_time',
        'duration_hours',
        'is_default',
        'is_active',
        'is_flexible',
    ];

    protected $appends = [
        'usage_count',
        'formatted_usage_stats',
        'shift_category',
        'formatted_time_range',
        'is_night_shift'
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
            'duration_hours' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_flexible' => 'boolean',
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

        // Clear cache when job shift is updated
        static::updated(function ($jobShift) {
            cache()->forget("job_shift.{$jobShift->id}");
            cache()->forget("job_shifts.popular");
            cache()->forget("job_shifts.trending");
            cache()->tags(['job_shifts', 'job_shift-' . $jobShift->id])->flush();
        });

        // Clear cache when job shift is deleted
        static::deleted(function ($jobShift) {
            cache()->forget("job_shift.{$jobShift->id}");
            cache()->forget("job_shifts.popular");
            cache()->forget("job_shifts.trending");
            cache()->tags(['job_shifts', 'job_shift-' . $jobShift->id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['shift', 'description', 'start_time', 'end_time', 'duration_hours', 'is_active', 'is_default', 'is_flexible'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("job_shift.{$this->id}.usage_count", 3600, function () {
            return $this->jobs_count;
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
                'active_jobs' => $this->jobs()->active()->count(),
                'featured_jobs' => $this->jobs()->featured()->count(),
                'total_usage' => $this->usage_count,
                'shift_category' => $this->shift_category,
                'average_salary' => $this->getAverageSalary(),
                'time_range' => $this->formatted_time_range,
            ];
        });
    }

    /**
     * Get shift category based on time and name.
     */
    public function getShiftCategoryAttribute(): string
    {
        $shift = strtolower($this->shift);
        $startHour = $this->start_time ? (int) substr($this->start_time, 0, 2) : null;
        
        return match (true) {
            str_contains($shift, 'night') || ($startHour && ($startHour >= 22 || $startHour <= 5)) => __('job_shift.category.night'),
            str_contains($shift, 'evening') || ($startHour && $startHour >= 15 && $startHour < 22) => __('job_shift.category.evening'),
            str_contains($shift, 'morning') || str_contains($shift, 'day') || ($startHour && $startHour >= 6 && $startHour < 15) => __('job_shift.category.day'),
            str_contains($shift, 'weekend') => __('job_shift.category.weekend'),
            str_contains($shift, 'flexible') || $this->is_flexible => __('job_shift.category.flexible'),
            str_contains($shift, 'rotating') => __('job_shift.category.rotating'),
            default => __('job_shift.category.standard')
        };
    }

    /**
     * Get formatted time range.
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return $this->is_flexible ? __('job_shift.flexible_hours') : __('job_shift.not_specified');
        }

        return $this->start_time . ' - ' . $this->end_time;
    }

    /**
     * Check if this is a night shift.
     */
    public function getIsNightShiftAttribute(): bool
    {
        if (!$this->start_time) {
            return str_contains(strtolower($this->shift), 'night');
        }

        $startHour = (int) substr($this->start_time, 0, 2);
        return $startHour >= 22 || $startHour <= 5;
    }

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
     * Scope for flexible job shifts.
     */
    public function scopeFlexible(Builder $query): Builder
    {
        return $query->where('is_flexible', true);
    }

    /**
     * Scope for fixed schedule job shifts.
     */
    public function scopeFixed(Builder $query): Builder
    {
        return $query->where('is_flexible', false);
    }

    /**
     * Scope for job shifts with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for job shifts with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for searching job shifts by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('shift', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular job shifts (with most jobs).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount('jobs')
                    ->orderByDesc('jobs_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered job shifts.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('shift', 'asc');
    }

    /**
     * Scope for recent job shifts.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderByDesc('created_at');
    }

    /**
     * Scope for trending job shifts.
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
     * Scope for day shifts.
     */
    public function scopeDayShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%day%')
                    ->orWhere('shift', 'like', '%morning%')
                    ->orWhereTime('start_time', '>=', '06:00')
                    ->whereTime('start_time', '<', '15:00');
    }

    /**
     * Scope for night shifts.
     */
    public function scopeNightShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%night%')
                    ->orWhere(function ($q) {
                        $q->whereTime('start_time', '>=', '22:00')
                          ->orWhereTime('start_time', '<=', '05:00');
                    });
    }

    /**
     * Scope for evening shifts.
     */
    public function scopeEveningShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%evening%')
                    ->orWhere(function ($q) {
                        $q->whereTime('start_time', '>=', '15:00')
                          ->whereTime('start_time', '<', '22:00');
                    });
    }

    /**
     * Scope for weekend shifts.
     */
    public function scopeWeekendShift(Builder $query): Builder
    {
        return $query->where('shift', 'like', '%weekend%')
                    ->orWhere('shift', 'like', '%saturday%')
                    ->orWhere('shift', 'like', '%sunday%');
    }

    /**
     * Scope for standard working hours (8 hours).
     */
    public function scopeStandardHours(Builder $query): Builder
    {
        return $query->where('duration_hours', 8);
    }

    /**
     * Scope for extended working hours (more than 8 hours).
     */
    public function scopeExtendedHours(Builder $query): Builder
    {
        return $query->where('duration_hours', '>', 8);
    }

    /**
     * Scope for specific duration.
     */
    public function scopeByDuration(Builder $query, int $hours): Builder
    {
        return $query->where('duration_hours', $hours);
    }

    /**
     * Scope for minimum duration.
     */
    public function scopeMinDuration(Builder $query, int $hours): Builder
    {
        return $query->where('duration_hours', '>=', $hours);
    }

    /**
     * Scope for maximum duration.
     */
    public function scopeMaxDuration(Builder $query, int $hours): Builder
    {
        return $query->where('duration_hours', '<=', $hours);
    }

    /**
     * Get average salary for this job shift.
     */
    public function getAverageSalary(): float
    {
        return cache()->remember("job_shift.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                        ->where('hide_salary', false)
                        ->whereNotNull('salary_from')
                        ->whereNotNull('salary_to')
                        ->avg(\DB::raw('(salary_from + salary_to) / 2')) ?? 0.0;
        });
    }

    /**
     * Check if shift overlaps with another shift.
     */
    public function overlapsWith(JobShift $otherShift): bool
    {
        if (!$this->start_time || !$this->end_time || !$otherShift->start_time || !$otherShift->end_time) {
            return false;
        }

        $thisStart = strtotime($this->start_time);
        $thisEnd = strtotime($this->end_time);
        $otherStart = strtotime($otherShift->start_time);
        $otherEnd = strtotime($otherShift->end_time);

        return $thisStart < $otherEnd && $otherStart < $thisEnd;
    }

    /**
     * Get shift differential percentage.
     */
    public function getShiftDifferential(): float
    {
        return match ($this->shift_category) {
            __('job_shift.category.night') => 15.0, // 15% night differential
            __('job_shift.category.evening') => 10.0, // 10% evening differential
            __('job_shift.category.weekend') => 20.0, // 20% weekend differential
            default => 0.0
        };
    }

    /**
     * Calculate hours per week for this shift.
     */
    public function getHoursPerWeek(): int
    {
        if (!$this->duration_hours) {
            return 40; // Default full-time
        }

        return match ($this->shift_category) {
            __('job_shift.category.weekend') => $this->duration_hours * 2, // Weekend only
            __('job_shift.category.flexible') => $this->duration_hours * 5, // 5 days flexible
            default => $this->duration_hours * 5 // Standard 5-day week
        };
    }
}
