<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class RequiredDegreeLevel
 *
 * @version September 7, 2020, 7:42 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $level_order
 * @property int $years_required
 * @property bool $is_active
 * @property bool $is_default
 * @property string|null $certification_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_count
 * @property-read mixed $formatted_usage_stats
 * @property-read mixed $education_category
 * @property-read mixed $career_progression_level
 * @property-read mixed $salary_range_multiplier
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
 * @method static Builder|RequiredDegreeLevel whereIsActive($value)
 * @method static Builder|RequiredDegreeLevel whereIsDefault($value)
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
    use HasFactory, LogsActivity;

    public $table = 'required_degree_levels';

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
        'name' => 'required|unique:required_degree_levels,name|max:100',
        'description' => 'nullable|string|max:500',
        'level_order' => 'required|integer|min:1|max:100|unique:required_degree_levels,level_order',
        'years_required' => 'required|integer|min:0|max:20',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'certification_required' => 'nullable|string|max:255',
    ];

    public $fillable = [
        'name',
        'description',
        'level_order',
        'years_required',
        'is_active',
        'is_default',
        'certification_required',
    ];

    protected $appends = [
        'usage_count',
        'formatted_usage_stats',
        'education_category',
        'career_progression_level',
        'salary_range_multiplier'
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
            'level_order' => 'integer',
            'years_required' => 'integer',
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

        // Clear cache when degree level is updated
        static::updated(function ($degreeLevel) {
            cache()->forget("required_degree_level.{$degreeLevel->id}");
            cache()->forget("required_degree_levels.popular");
            cache()->forget("required_degree_levels.trending");
            cache()->tags(['required_degree_levels', 'required_degree_level-' . $degreeLevel->id])->flush();
        });

        // Clear cache when degree level is deleted
        static::deleted(function ($degreeLevel) {
            cache()->forget("required_degree_level.{$degreeLevel->id}");
            cache()->forget("required_degree_levels.popular");
            cache()->forget("required_degree_levels.trending");
            cache()->tags(['required_degree_levels', 'required_degree_level-' . $degreeLevel->id])->flush();
        });
    }

    /**
     * Activity log options
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
     * Scope for ordered by level (hierarchical).
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
                        }
                    ])
                    ->orderByDesc('jobs_count');
    }

    /**
     * Scope for entry level education requirements.
     */
    public function scopeEntryLevel(Builder $query): Builder
    {
        return $query->where('level_order', '<=', 2);
    }

    /**
     * Scope for intermediate level education requirements.
     */
    public function scopeIntermediate(Builder $query): Builder
    {
        return $query->whereBetween('level_order', [3, 6]);
    }

    /**
     * Scope for advanced level education requirements.
     */
    public function scopeAdvanced(Builder $query): Builder
    {
        return $query->where('level_order', '>=', 7);
    }

    /**
     * Scope for postgraduate level requirements.
     */
    public function scopePostgraduate(Builder $query): Builder
    {
        return $query->where('level_order', '>=', 7)
                    ->where(function ($q) {
                        $q->where('name', 'like', '%master%')
                          ->orWhere('name', 'like', '%doctoral%')
                          ->orWhere('name', 'like', '%phd%');
                    });
    }

    /**
     * Scope for undergraduate level requirements.
     */
    public function scopeUndergraduate(Builder $query): Builder
    {
        return $query->whereBetween('level_order', [3, 6])
                    ->where(function ($q) {
                        $q->where('name', 'like', '%bachelor%')
                          ->orWhere('name', 'like', '%associate%')
                          ->orWhere('name', 'like', '%undergraduate%');
                    });
    }

    /**
     * Scope for doctoral level requirements.
     */
    public function scopeDoctoral(Builder $query): Builder
    {
        return $query->where('name', 'like', '%doctoral%')
                    ->orWhere('name', 'like', '%phd%')
                    ->orWhere('name', 'like', '%doctorate%');
    }

    /**
     * Scope for professional certification requirements.
     */
    public function scopeProfessional(Builder $query): Builder
    {
        return $query->where('name', 'like', '%professional%')
                    ->orWhere('name', 'like', '%license%')
                    ->orWhereNotNull('certification_required');
    }

    /**
     * Scope for certification-based requirements.
     */
    public function scopeCertification(Builder $query): Builder
    {
        return $query->where('name', 'like', '%certificate%')
                    ->orWhere('name', 'like', '%certification%')
                    ->orWhereNotNull('certification_required');
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
        return $query->whereNotNull('certification_required');
    }

    /**
     * Get average salary for this degree level.
     */
    public function getAverageSalary(): float
    {
        return cache()->remember("required_degree_level.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                        ->where('hide_salary', false)
                        ->whereNotNull('salary_from')
                        ->whereNotNull('salary_to')
                        ->avg(\DB::raw('(salary_from + salary_to) / 2')) ?? 0.0;
        });
    }

    /**
     * Check if this degree level is equivalent to another.
     */
    public function isEquivalentTo(RequiredDegreeLevel $otherLevel): bool
    {
        return abs($this->level_order - $otherLevel->level_order) <= 1;
    }

    /**
     * Check if this degree level is higher than another.
     */
    public function isHigherThan(RequiredDegreeLevel $otherLevel): bool
    {
        return $this->level_order > $otherLevel->level_order;
    }

    /**
     * Get progression path to next level.
     */
    public function getNextLevel(): ?RequiredDegreeLevel
    {
        return static::where('level_order', '>', $this->level_order)
                    ->where('is_active', true)
                    ->orderBy('level_order')
                    ->first();
    }

    /**
     * Get previous level in hierarchy.
     */
    public function getPreviousLevel(): ?RequiredDegreeLevel
    {
        return static::where('level_order', '<', $this->level_order)
                    ->where('is_active', true)
                    ->orderByDesc('level_order')
                    ->first();
    }

    /**
     * Calculate education ROI based on salary difference.
     */
    public function getEducationROI(): float
    {
        $previousLevel = $this->getPreviousLevel();
        if (!$previousLevel) {
            return 0.0;
        }

        $currentAvgSalary = $this->getAverageSalary();
        $previousAvgSalary = $previousLevel->getAverageSalary();
        
        if ($previousAvgSalary == 0) {
            return 0.0;
        }

        return (($currentAvgSalary - $previousAvgSalary) / $previousAvgSalary) * 100;
    }
}
