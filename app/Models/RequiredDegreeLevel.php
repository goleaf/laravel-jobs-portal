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
 * Class RequiredDegreeLevel.
 *
 * @version June 20, 2020, 5:50 am UTC
 *
 * @property int $id
 * @property string $name
 * @property null|string $description
 * @property int $level_order
 * @property null|int $years_required
 * @property bool $is_default
 * @property bool $is_active
 * @property null|string $certification_required
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Collection|Job[] $jobs
 * @property null|int $jobs_count
 * @property mixed $usage_count
 * @property mixed $formatted_usage_stats
 * @property mixed $education_category
 * @property mixed $career_progression_level
 * @property mixed $salary_range_multiplier
 *
 * @method static Builder|RequiredDegreeLevel newModelQuery()
 * @method static Builder|RequiredDegreeLevel newQuery()
 * @method static Builder|RequiredDegreeLevel query()
 * @method static Builder|RequiredDegreeLevel whereCreatedAt($value)
 * @method static Builder|RequiredDegreeLevel whereId($value)
 * @method static Builder|RequiredDegreeLevel whereName($value)
 * @method static Builder|RequiredDegreeLevel whereDescription($value)
 * @method static Builder|RequiredDegreeLevel whereLevelOrder($value)
 * @method static Builder|RequiredDegreeLevel whereYearsRequired($value)
 * @method static Builder|RequiredDegreeLevel whereIsDefault($value)
 * @method static Builder|RequiredDegreeLevel whereIsActive($value)
 * @method static Builder|RequiredDegreeLevel whereCertificationRequired($value)
 * @method static Builder|RequiredDegreeLevel whereUpdatedAt($value)
 * @method static Builder|RequiredDegreeLevel active()
 * @method static Builder|RequiredDegreeLevel inactive()
 * @method static Builder|RequiredDegreeLevel default()
 * @method static Builder|RequiredDegreeLevel custom()
 * @method static Builder|RequiredDegreeLevel withJobs()
 * @method static Builder|RequiredDegreeLevel withActiveJobs()
 * @method static Builder|RequiredDegreeLevel search(string $term)
 * @method static Builder|RequiredDegreeLevel popular(int $limit = 10)
 * @method static Builder|RequiredDegreeLevel alphabetical()
 * @method static Builder|RequiredDegreeLevel byLevel()
 * @method static Builder|RequiredDegreeLevel recent(int $days = 30)
 * @method static Builder|RequiredDegreeLevel trending()
 * @method static Builder|RequiredDegreeLevel entryLevel()
 * @method static Builder|RequiredDegreeLevel intermediate()
 * @method static Builder|RequiredDegreeLevel advanced()
 * @method static Builder|RequiredDegreeLevel postgraduate()
 * @method static Builder|RequiredDegreeLevel undergraduate()
 * @method static Builder|RequiredDegreeLevel doctoral()
 * @method static Builder|RequiredDegreeLevel professional()
 * @method static Builder|RequiredDegreeLevel certification()
 * @method static Builder|RequiredDegreeLevel byYearsRequired(int $years)
 * @method static Builder|RequiredDegreeLevel minYears(int $years)
 * @method static Builder|RequiredDegreeLevel maxYears(int $years)
 * @method static Builder|RequiredDegreeLevel requiresCertification()
 *
 * @mixin \Eloquent
 */
class RequiredDegreeLevel extends Model
{
    use HasFactory;
    use LogsActivity;

    public $table = 'required_degree_levels';

    public $fillable = [
        'name',
        'description',
        'level_order',
        'years_required',
        'is_default',
        'is_active',
        'certification_required',
        'slug',
        'icon',
        'color',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'level_order', 'years_required', 'is_active', 'is_default', 'certification_required'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("required_degree_level.{$this->id}.usage_count", 3600, function () {
            return $this->jobs_count ?? $this->jobs()->count();
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("required_degree_level.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->active()->count(),
                'featured_jobs' => $this->jobs()->featured()->count(),
                'total_usage' => $this->usage_count,
                'education_category' => $this->education_category,
                'average_salary' => $this->getAverageSalary(),
                'career_progression' => $this->career_progression_level,
                'salary_multiplier' => $this->salary_range_multiplier,
            ];
        });
    }

    /**
     * Get education category based on level and name.
     */
    public function getEducationCategoryAttribute(): string
    {
        $name = strtolower($this->name);

        return match (true) {
            str_contains($name, 'high school') || str_contains($name, 'diploma') || $this->level_order <= 2 => __('education.category.high_school'),
            str_contains($name, 'certificate') || str_contains($name, 'certification') => __('education.category.certificate'),
            str_contains($name, 'associate') || $this->level_order <= 4 => __('education.category.associate'),
            str_contains($name, 'bachelor') || str_contains($name, 'undergraduate') || $this->level_order <= 6 => __('education.category.bachelor'),
            str_contains($name, 'master') || str_contains($name, 'graduate') || $this->level_order <= 8 => __('education.category.master'),
            str_contains($name, 'doctoral') || str_contains($name, 'phd') || str_contains($name, 'doctorate') => __('education.category.doctoral'),
            str_contains($name, 'professional') || str_contains($name, 'license') => __('education.category.professional'),
            default => __('education.category.other')
        };
    }

    /**
     * Get career progression level.
     */
    public function getCareerProgressionLevelAttribute(): string
    {
        return match ($this->level_order) {
            1, 2 => __('career.level.entry'),
            3, 4, 5 => __('career.level.junior'),
            6, 7 => __('career.level.mid'),
            8, 9 => __('career.level.senior'),
            default => __('career.level.executive')
        };
    }

    /**
     * Get salary range multiplier based on education level.
     */
    public function getSalaryRangeMultiplierAttribute(): float
    {
        return match ($this->education_category) {
            __('education.category.high_school') => 1.0,
            __('education.category.certificate') => 1.1,
            __('education.category.associate') => 1.2,
            __('education.category.bachelor') => 1.4,
            __('education.category.master') => 1.7,
            __('education.category.doctoral') => 2.1,
            __('education.category.professional') => 1.9,
            default => 1.0
        };
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'degree_level_id');
    }

    /**
     * Scope for active degree levels.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive degree levels.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default degree levels.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom degree levels.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for degree levels with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for degree levels with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for searching degree levels by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->orWhere('certification_required', 'like', "%{$term}%");
    }

    /**
     * Scope for popular degree levels (with most jobs).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount('jobs')
            ->orderByDesc('jobs_count')
            ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered degree levels.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for degree levels ordered by level.
     */
    public function scopeByLevel(Builder $query): Builder
    {
        return $query->orderBy('level_order', 'asc');
    }

    /**
     * Scope for recent degree levels.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');
    }

    /**
     * Scope for trending degree levels.
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
     * Scope for entry level education.
     */
    public function scopeEntryLevel(Builder $query): Builder
    {
        return $query->where('level_order', '<=', 2);
    }

    /**
     * Scope for intermediate level education.
     */
    public function scopeIntermediate(Builder $query): Builder
    {
        return $query->whereBetween('level_order', [3, 6]);
    }

    /**
     * Scope for advanced level education.
     */
    public function scopeAdvanced(Builder $query): Builder
    {
        return $query->where('level_order', '>=', 7);
    }

    /**
     * Scope for postgraduate education.
     */
    public function scopePostgraduate(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('name', 'like', '%master%')
                ->orWhere('name', 'like', '%doctoral%')
                ->orWhere('name', 'like', '%phd%')
                ->orWhere('level_order', '>=', 7);
        });
    }

    /**
     * Scope for undergraduate education.
     */
    public function scopeUndergraduate(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('name', 'like', '%bachelor%')
                ->orWhere('name', 'like', '%associate%')
                ->orWhere('name', 'like', '%diploma%')
                ->orWhereBetween('level_order', [3, 6]);
        });
    }

    /**
     * Scope for doctoral level education.
     */
    public function scopeDoctoral(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('name', 'like', '%doctoral%')
                ->orWhere('name', 'like', '%phd%')
                ->orWhere('name', 'like', '%doctorate%');
        });
    }

    /**
     * Scope for professional certifications.
     */
    public function scopeProfessional(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('name', 'like', '%professional%')
                ->orWhere('name', 'like', '%license%')
                ->orWhere('name', 'like', '%certification%')
                ->orWhereNotNull('certification_required');
        });
    }

    /**
     * Scope for certification requirements.
     */
    public function scopeCertification(Builder $query): Builder
    {
        return $query->whereNotNull('certification_required');
    }

    /**
     * Scope for specific years required.
     */
    public function scopeByYearsRequired(Builder $query, int $years): Builder
    {
        return $query->where('years_required', $years);
    }

    /**
     * Scope for minimum years required.
     */
    public function scopeMinYears(Builder $query, int $years): Builder
    {
        return $query->where('years_required', '>=', $years);
    }

    /**
     * Scope for maximum years required.
     */
    public function scopeMaxYears(Builder $query, int $years): Builder
    {
        return $query->where('years_required', '<=', $years);
    }

    /**
     * Scope for degrees that require certification.
     */
    public function scopeRequiresCertification(Builder $query): Builder
    {
        return $query->whereNotNull('certification_required')
            ->where('certification_required', '!=', '');
    }

    /**
     * Get average salary for this degree level.
     */
    public function getAverageSalary(): float
    {
        return cache()->remember("required_degree_level.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                ->whereNotNull('min_salary')
                ->whereNotNull('max_salary')
                ->selectRaw('AVG((min_salary + max_salary) / 2) as avg_salary')
                ->value('avg_salary') ?? 0.0;
        });
    }

    /**
     * Check if this degree level is equivalent to another.
     */
    public function isEquivalentTo(RequiredDegreeLevel $otherLevel): bool
    {
        return $this->level_order === $otherLevel->level_order
               || abs($this->level_order - $otherLevel->level_order) <= 1;
    }

    /**
     * Check if this degree level is higher than another.
     */
    public function isHigherThan(RequiredDegreeLevel $otherLevel): bool
    {
        return $this->level_order > $otherLevel->level_order;
    }

    /**
     * Get next higher degree level.
     */
    public function getNextLevel(): ?RequiredDegreeLevel
    {
        return static::where('level_order', '>', $this->level_order)
            ->active()
            ->orderBy('level_order', 'asc')
            ->first();
    }

    /**
     * Get previous lower degree level.
     */
    public function getPreviousLevel(): ?RequiredDegreeLevel
    {
        return static::where('level_order', '<', $this->level_order)
            ->active()
            ->orderBy('level_order', 'desc')
            ->first();
    }

    /**
     * Get education ROI (Return on Investment).
     */
    public function getEducationROI(): float
    {
        $averageSalary = $this->getAverageSalary();
        $yearsRequired = $this->years_required ?? 4;
        $estimatedCost = $yearsRequired * 15000; // Estimated annual education cost

        if ($estimatedCost === 0) {
            return 0.0;
        }

        return round(($averageSalary * 10) / $estimatedCost, 2); // 10-year ROI
    }

    /**
     * Get career advancement opportunities.
     */
    public function getCareerAdvancementOpportunities(): array
    {
        return cache()->remember("required_degree_level.{$this->id}.career_advancement", 3600, function () {
            $nextLevel = $this->getNextLevel();
            $averageSalary = $this->getAverageSalary();
            $nextLevelSalary = $nextLevel ? $nextLevel->getAverageSalary() : 0;

            return [
                'current_level' => $this->name,
                'next_level' => $nextLevel?->name,
                'salary_increase_potential' => $nextLevelSalary - $averageSalary,
                'salary_increase_percentage' => $averageSalary > 0 ? round((($nextLevelSalary - $averageSalary) / $averageSalary) * 100, 2) : 0,
                'career_progression_level' => $this->career_progression_level,
                'education_roi' => $this->getEducationROI(),
            ];
        });
    }

    /**
     * Get industry demand for this degree level.
     */
    public function getIndustryDemand(): array
    {
        return cache()->remember("required_degree_level.{$this->id}.industry_demand", 3600, function () {
            $jobsByIndustry = $this->jobs()
                ->with('company.industry')
                ->get()
                ->groupBy('company.industry.name')
                ->map(function ($jobs) {
                    return $jobs->count();
                })
                ->sortDesc()
                ->take(10);

            return [
                'total_jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->active()->count(),
                'top_industries' => $jobsByIndustry->toArray(),
                'demand_trend' => $this->getDemandTrend(),
            ];
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level_order' => 'integer',
            'years_required' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
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

        // Clear cache when degree level is updated
        static::updated(function ($degreeLevel) {
            cache()->forget("required_degree_level.{$degreeLevel->id}");
            cache()->forget('required_degree_levels.popular');
            cache()->forget('required_degree_levels.trending');
            cache()->tags(['required_degree_levels', 'required_degree_level-'.$degreeLevel->id])->flush();
        });

        // Clear cache when degree level is deleted
        static::deleted(function ($degreeLevel) {
            cache()->forget("required_degree_level.{$degreeLevel->id}");
            cache()->forget('required_degree_levels.popular');
            cache()->forget('required_degree_levels.trending');
            cache()->tags(['required_degree_levels', 'required_degree_level-'.$degreeLevel->id])->flush();
        });
    }

    /**
     * Get demand trend for this degree level.
     */
    private function getDemandTrend(): string
    {
        $currentMonth = $this->jobs()->where('created_at', '>=', now()->subDays(30))->count();
        $previousMonth = $this->jobs()->whereBetween('created_at', [
            now()->subDays(60),
            now()->subDays(30),
        ])->count();

        if ($previousMonth === 0) {
            return $currentMonth > 0 ? 'increasing' : 'stable';
        }

        $changePercentage = (($currentMonth - $previousMonth) / $previousMonth) * 100;

        return match (true) {
            $changePercentage > 10 => 'rapidly_increasing',
            $changePercentage > 0 => 'increasing',
            $changePercentage < -10 => 'rapidly_decreasing',
            $changePercentage < 0 => 'decreasing',
            default => 'stable'
        };
    }
}
