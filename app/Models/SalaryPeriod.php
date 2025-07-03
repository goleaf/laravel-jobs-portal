<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * SalaryPeriod Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $period
 * @property null|string $description
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property null|int $sort_order
 * @property null|float $multiplier_hours
 * @property null|float $multiplier_days
 * @property null|float $multiplier_months
 * @property null|float $multiplier_years
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Collection|Job[] $jobs
 * @property Candidate[]|Collection $candidates
 * @property string $display_name
 * @property string $period_type
 * @property int $jobs_count
 * @property int $active_jobs_count
 * @property int $candidates_count
 * @property int $active_candidates_count
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder byOrder()
 * @method static \Illuminate\Database\Eloquent\Builder hourly()
 * @method static \Illuminate\Database\Eloquent\Builder daily()
 * @method static \Illuminate\Database\Eloquent\Builder weekly()
 * @method static \Illuminate\Database\Eloquent\Builder monthly()
 * @method static \Illuminate\Database\Eloquent\Builder yearly()
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder withJobs()
 * @method static \Illuminate\Database\Eloquent\Builder withActiveJobs()
 * @method static \Illuminate\Database\Eloquent\Builder withCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withActiveCandidates()
 *
 * @mixin \Eloquent
 */
class SalaryPeriod extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * Validation rules for creating salary periods.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'period' => 'required|string|max:150|unique:salary_periods,period',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'nullable|integer|min:0|max:999',
        'multiplier_hours' => 'nullable|numeric|min:0',
        'multiplier_days' => 'nullable|numeric|min:0',
        'multiplier_months' => 'nullable|numeric|min:0',
        'multiplier_years' => 'nullable|numeric|min:0',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'salary_periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period',
        'description',
        'is_active',
        'is_default',
        'is_featured',
        'sort_order',
        'multiplier_hours',
        'multiplier_days',
        'multiplier_months',
        'multiplier_years',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['period', 'description', 'is_active', 'is_default', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for salary periods.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'period' => 'required|string|max:150|unique:salary_periods,period,'.$id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'multiplier_hours' => 'nullable|numeric|min:0',
            'multiplier_days' => 'nullable|numeric|min:0',
            'multiplier_months' => 'nullable|numeric|min:0',
            'multiplier_years' => 'nullable|numeric|min:0',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobs for the salary period.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'salary_period_id');
    }

    /**
     * Get the candidates for the salary period.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'salary_period_id');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to only include default salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    // =============================================
    // SCOPES - Period Types
    // =============================================

    /**
     * Scope a query to get hourly periods.
     *
     * @param  mixed  $query
     */
    public function scopeHourly($query)
    {
        return $query->where(function ($q) {
            $q->where('period', 'like', '%hour%')
                ->orWhere('period', 'like', '%hr%')
                ->orWhere('period', 'like', '%hourly%');
        });
    }

    /**
     * Scope a query to get daily periods.
     *
     * @param  mixed  $query
     */
    public function scopeDaily($query)
    {
        return $query->where(function ($q) {
            $q->where('period', 'like', '%day%')
                ->orWhere('period', 'like', '%daily%');
        });
    }

    /**
     * Scope a query to get weekly periods.
     *
     * @param  mixed  $query
     */
    public function scopeWeekly($query)
    {
        return $query->where(function ($q) {
            $q->where('period', 'like', '%week%')
                ->orWhere('period', 'like', '%weekly%');
        });
    }

    /**
     * Scope a query to get monthly periods.
     *
     * @param  mixed  $query
     */
    public function scopeMonthly($query)
    {
        return $query->where(function ($q) {
            $q->where('period', 'like', '%month%')
                ->orWhere('period', 'like', '%monthly%');
        });
    }

    /**
     * Scope a query to get yearly periods.
     *
     * @param  mixed  $query
     */
    public function scopeYearly($query)
    {
        return $query->where(function ($q) {
            $q->where('period', 'like', '%year%')
                ->orWhere('period', 'like', '%annual%')
                ->orWhere('period', 'like', '%yearly%');
        });
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope a query to search salary periods by name or description.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('period', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to get recent salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get old salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to order salary periods alphabetically.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('period', 'asc');
    }

    /**
     * Scope a query to order salary periods by sort order.
     *
     * @param  mixed  $query
     */
    public function scopeByOrder($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('period', 'asc');
    }

    // =============================================
    // SCOPES - Relationships & Popularity
    // =============================================

    /**
     * Scope a query to include salary periods with jobs.
     *
     * @param  mixed  $query
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include salary periods with active jobs.
     *
     * @param  mixed  $query
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active')
                ->where('expire_date', '>', now());
        });
    }

    /**
     * Scope a query to include salary periods with candidates.
     *
     * @param  mixed  $query
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include salary periods with active candidates.
     *
     * @param  mixed  $query
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to get popular salary periods (with most jobs).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('status', 'active');
        }])
            ->orderBy('jobs_count', 'desc')
            ->limit($limit);
    }

    /**
     * Scope a query to get popular salary periods by candidates.
     *
     * @param  mixed  $query
     */
    public function scopePopularByCandidates($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('is_active', true);
        }])
            ->orderBy('candidates_count', 'desc')
            ->limit($limit);
    }

    /**
     * Scope a query to get trending salary periods.
     *
     * @param  mixed  $query
     */
    public function scopeTrending($query, int $days = 30, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) use ($days) {
            $q->where('status', 'active')
                ->where('created_at', '>=', now()->subDays($days));
        }])
            ->having('jobs_count', '>', 0)
            ->orderBy('jobs_count', 'desc')
            ->limit($limit);
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached active salary periods.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember(
            'salary_periods_active',
            now()->addHours(24),
            fn () => static::active()->byOrder()->get()
        );
    }

    /**
     * Get cached featured salary periods.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember(
            'salary_periods_featured',
            now()->addHours(12),
            fn () => static::active()->featured()->byOrder()->get()
        );
    }

    /**
     * Get cached default salary periods.
     */
    public static function getCachedDefault(): Collection
    {
        return Cache::remember(
            'salary_periods_default',
            now()->addHours(24),
            fn () => static::active()->default()->byOrder()->get()
        );
    }

    /**
     * Get cached popular salary periods.
     */
    public static function getCachedPopular(int $limit = 10): Collection
    {
        return Cache::remember(
            "salary_periods_popular_{$limit}",
            now()->addHours(6),
            fn () => static::active()->popular($limit)->get()
        );
    }

    /**
     * Get cached salary periods by type.
     */
    public static function getCachedByType(string $type): Collection
    {
        return Cache::remember(
            "salary_periods_type_{$type}",
            now()->addHours(12),
            fn () => static::active()->{$type}()->byOrder()->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get display name with description.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->description ? "{$this->period} ({$this->description})" : $this->period;
    }

    /**
     * Get period type based on name.
     */
    public function getPeriodTypeAttribute(): string
    {
        $period = strtolower($this->period);

        if (str_contains($period, 'hour') || str_contains($period, 'hr')) {
            return 'hourly';
        }
        if (str_contains($period, 'day') || str_contains($period, 'daily')) {
            return 'daily';
        }
        if (str_contains($period, 'week') || str_contains($period, 'weekly')) {
            return 'weekly';
        }
        if (str_contains($period, 'month') || str_contains($period, 'monthly')) {
            return 'monthly';
        }
        if (str_contains($period, 'year') || str_contains($period, 'annual') || str_contains($period, 'yearly')) {
            return 'yearly';
        }

        return 'custom';
    }

    /**
     * Get jobs count.
     */
    public function getJobsCountAttribute(): int
    {
        return Cache::remember(
            "salary_period_{$this->id}_jobs_count",
            now()->addHours(6),
            fn () => $this->jobs()->count()
        );
    }

    /**
     * Get active jobs count.
     */
    public function getActiveJobsCountAttribute(): int
    {
        return Cache::remember(
            "salary_period_{$this->id}_active_jobs_count",
            now()->addHours(6),
            fn () => $this->jobs()->where('status', 'active')->count()
        );
    }

    /**
     * Get candidates count.
     */
    public function getCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "salary_period_{$this->id}_candidates_count",
            now()->addHours(6),
            fn () => $this->candidates()->count()
        );
    }

    /**
     * Get active candidates count.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "salary_period_{$this->id}_active_candidates_count",
            now()->addHours(6),
            fn () => $this->candidates()->where('is_active', true)->count()
        );
    }

    /**
     * Check if period is hourly.
     */
    public function isHourly(): bool
    {
        return $this->period_type === 'hourly';
    }

    /**
     * Check if period is daily.
     */
    public function isDaily(): bool
    {
        return $this->period_type === 'daily';
    }

    /**
     * Check if period is weekly.
     */
    public function isWeekly(): bool
    {
        return $this->period_type === 'weekly';
    }

    /**
     * Check if period is monthly.
     */
    public function isMonthly(): bool
    {
        return $this->period_type === 'monthly';
    }

    /**
     * Check if period is yearly.
     */
    public function isYearly(): bool
    {
        return $this->period_type === 'yearly';
    }

    /**
     * Convert salary to yearly equivalent.
     */
    public function convertToYearly(float $amount): float
    {
        if ($this->multiplier_years) {
            return $amount * $this->multiplier_years;
        }

        // Default conversions
        switch ($this->period_type) {
            case 'hourly':
                return $amount * 40 * 52; // 40 hours/week * 52 weeks

            case 'daily':
                return $amount * 5 * 52; // 5 days/week * 52 weeks

            case 'weekly':
                return $amount * 52;

            case 'monthly':
                return $amount * 12;

            case 'yearly':
            default:
                return $amount;
        }
    }

    /**
     * Convert salary to monthly equivalent.
     */
    public function convertToMonthly(float $amount): float
    {
        if ($this->multiplier_months) {
            return $amount * $this->multiplier_months;
        }

        return $this->convertToYearly($amount) / 12;
    }

    /**
     * Convert salary to hourly equivalent.
     */
    public function convertToHourly(float $amount): float
    {
        if ($this->multiplier_hours) {
            return $amount * $this->multiplier_hours;
        }

        return $this->convertToYearly($amount) / (40 * 52);
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        $patterns = [
            'salary_periods_active',
            'salary_periods_featured',
            'salary_periods_default',
            "salary_period_{$this->id}_*",
            'salary_periods_type_*',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $this->clearCachePattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }

        // Clear popular caches
        for ($i = 5; $i <= 20; $i += 5) {
            Cache::forget("salary_periods_popular_{$i}");
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'multiplier_hours' => 'decimal:4',
            'multiplier_days' => 'decimal:4',
            'multiplier_months' => 'decimal:4',
            'multiplier_years' => 'decimal:4',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($salaryPeriod) {
            $salaryPeriod->clearCaches();
        });

        static::deleted(function ($salaryPeriod) {
            $salaryPeriod->clearCaches();
        });
    }

    /**
     * Clear cache keys matching pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        if (str_contains($pattern, 'salary_period_'.$this->id)) {
            $prefix = "salary_period_{$this->id}_";
            $keys = [
                $prefix.'jobs_count',
                $prefix.'active_jobs_count',
                $prefix.'candidates_count',
                $prefix.'active_candidates_count',
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }

        if (str_contains($pattern, 'salary_periods_type_')) {
            $types = ['hourly', 'daily', 'weekly', 'monthly', 'yearly'];
            foreach ($types as $type) {
                Cache::forget("salary_periods_type_{$type}");
            }
        }
    }
}
