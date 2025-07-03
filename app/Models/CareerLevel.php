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
 * Class CareerLevel.
 *
 * @version June 20, 2020, 5:46 am UTC
 *
 * @property int $id
 * @property string $level_name
 * @property null|string $description
 * @property int $level_order
 * @property bool $is_default
 * @property bool $is_active
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Collection|Job[] $jobs
 * @property null|int $jobs_count
 * @property Candidate[]|Collection $candidates
 * @property null|int $candidates_count
 * @property mixed $usage_count
 * @property mixed $formatted_usage_stats
 * @property mixed $level_category
 *
 * @method static Builder|CareerLevel newModelQuery()
 * @method static Builder|CareerLevel newQuery()
 * @method static Builder|CareerLevel query()
 * @method static Builder|CareerLevel whereCreatedAt($value)
 * @method static Builder|CareerLevel whereId($value)
 * @method static Builder|CareerLevel whereLevelName($value)
 * @method static Builder|CareerLevel whereDescription($value)
 * @method static Builder|CareerLevel whereLevelOrder($value)
 * @method static Builder|CareerLevel whereIsDefault($value)
 * @method static Builder|CareerLevel whereIsActive($value)
 * @method static Builder|CareerLevel whereUpdatedAt($value)
 * @method static Builder|CareerLevel active()
 * @method static Builder|CareerLevel inactive()
 * @method static Builder|CareerLevel default()
 * @method static Builder|CareerLevel custom()
 * @method static Builder|CareerLevel withJobs()
 * @method static Builder|CareerLevel withActiveJobs()
 * @method static Builder|CareerLevel withCandidates()
 * @method static Builder|CareerLevel withActiveCandidates()
 * @method static Builder|CareerLevel search(string $term)
 * @method static Builder|CareerLevel popular(int $limit = 10)
 * @method static Builder|CareerLevel alphabetical()
 * @method static Builder|CareerLevel byOrder()
 * @method static Builder|CareerLevel recent(int $days = 30)
 * @method static Builder|CareerLevel trending()
 * @method static Builder|CareerLevel entryLevel()
 * @method static Builder|CareerLevel midLevel()
 * @method static Builder|CareerLevel seniorLevel()
 * @method static Builder|CareerLevel executiveLevel()
 * @method static Builder|CareerLevel minOrder(int $order)
 * @method static Builder|CareerLevel maxOrder(int $order)
 *
 * @mixin \Eloquent
 */
class CareerLevel extends Model
{
    use HasFactory;
    use LogsActivity;

    public $table = 'career_levels';

    public $fillable = [
        'level_name',
        'description',
        'level_order',
        'is_default',
        'is_active',
        'slug',
        'icon',
        'color',
        'min_experience_years',
        'max_experience_years',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days))
            ->orderBy('created_at', 'asc');
    }

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['level_name', 'description', 'level_order', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return cache()->remember("career_level.{$this->id}.usage_count", 3600, function () {
            return $this->jobs_count + $this->candidates_count;
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("career_level.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'jobs' => $this->jobs()->count(),
                'candidates' => $this->candidates()->count(),
                'active_jobs' => $this->jobs()->active()->count(),
                'active_candidates' => $this->candidates()->active()->count(),
                'total_usage' => $this->usage_count,
                'level_category' => $this->level_category,
                'average_salary' => $this->getAverageSalary(),
            ];
        });
    }

    /**
     * Get level category based on order.
     */
    public function getLevelCategoryAttribute(): string
    {
        return match (true) {
            $this->level_order <= 2 => __('career_level.entry_level'),
            $this->level_order <= 4 => __('career_level.junior_level'),
            $this->level_order <= 6 => __('career_level.mid_level'),
            $this->level_order <= 8 => __('career_level.senior_level'),
            $this->level_order <= 10 => __('career_level.executive_level'),
            default => __('career_level.leadership_level')
        };
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'career_level_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'career_level_id');
    }

    /**
     * Scope for active career levels.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive career levels.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default career levels.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom career levels.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for career levels with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for career levels with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for career levels with candidates.
     */
    public function scopeWithCandidates(Builder $query): Builder
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for career levels with active candidates.
     */
    public function scopeWithActiveCandidates(Builder $query): Builder
    {
        return $query->whereHas('candidates', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for searching career levels by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('level_name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular career levels (with most jobs/candidates).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount(['jobs', 'candidates'])
            ->orderByDesc('jobs_count')
            ->orderByDesc('candidates_count')
            ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered career levels.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('level_name', 'asc');
    }

    /**
     * Scope for career levels ordered by level order.
     */
    public function scopeByOrder(Builder $query): Builder
    {
        return $query->orderBy('level_order', 'asc');
    }

    /**
     * Scope for recent career levels.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');
    }

    /**
     * Scope for trending career levels.
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
            ->orderByDesc('candidates_count');
    }

    /**
     * Scope for entry level positions.
     */
    public function scopeEntryLevel(Builder $query): Builder
    {
        return $query->where('level_order', '<=', 2);
    }

    /**
     * Scope for entry positions (alias for tests).
     */
    public function scopeEntry(Builder $query): Builder
    {
        return $query->where('level_name', 'LIKE', '%Entry%')
            ->orWhere('level_name', 'LIKE', '%Junior%');
    }

    /**
     * Scope for senior positions.
     */
    public function scopeSenior(Builder $query): Builder
    {
        return $query->where('level_name', 'LIKE', '%Senior%');
    }

    /**
     * Scope for management positions.
     */
    public function scopeManagement(Builder $query): Builder
    {
        return $query->where('level_name', 'LIKE', '%Manager%')
            ->orWhere('level_name', 'LIKE', '%Executive%');
    }

    /**
     * Scope for mid level positions.
     */
    public function scopeMidLevel(Builder $query): Builder
    {
        return $query->whereBetween('level_order', [3, 6]);
    }

    /**
     * Scope for senior level positions.
     */
    public function scopeSeniorLevel(Builder $query): Builder
    {
        return $query->whereBetween('level_order', [7, 8]);
    }

    /**
     * Scope for executive level positions.
     */
    public function scopeExecutiveLevel(Builder $query): Builder
    {
        return $query->where('level_order', '>=', 9);
    }

    /**
     * Scope for minimum order.
     */
    public function scopeMinOrder(Builder $query, int $order): Builder
    {
        return $query->where('level_order', '>=', $order);
    }

    /**
     * Scope for maximum order.
     */
    public function scopeMaxOrder(Builder $query, int $order): Builder
    {
        return $query->where('level_order', '<=', $order);
    }

    /**
     * Check if career level is entry level.
     */
    public function isEntryLevel(): bool
    {
        return $this->level_order <= 2;
    }

    /**
     * Check if career level is senior level.
     */
    public function isSeniorLevel(): bool
    {
        return $this->level_order >= 7 && $this->level_order <= 8;
    }

    /**
     * Check if career level is executive level.
     */
    public function isExecutiveLevel(): bool
    {
        return $this->level_order >= 9;
    }

    /**
     * Get average salary for this career level.
     */
    public function getAverageSalary(): float
    {
        return cache()->remember("career_level.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                ->whereNotNull('min_salary')
                ->whereNotNull('max_salary')
                ->selectRaw('AVG((min_salary + max_salary) / 2) as avg_salary')
                ->value('avg_salary') ?? 0.0;
        });
    }

    /**
     * Get next career level.
     */
    public function getNextLevel(): ?CareerLevel
    {
        return static::where('level_order', '>', $this->level_order)
            ->active()
            ->orderBy('level_order', 'asc')
            ->first();
    }

    /**
     * Get previous career level.
     */
    public function getPreviousLevel(): ?CareerLevel
    {
        return static::where('level_order', '<', $this->level_order)
            ->active()
            ->orderBy('level_order', 'desc')
            ->first();
    }

    /**
     * Get career progression path.
     */
    public function getProgressionPath(): Collection
    {
        return cache()->remember("career_level.{$this->id}.progression_path", 3600, function () {
            return static::active()
                ->byOrder()
                ->get();
        });
    }

    /**
     * Get experience range for this level.
     */
    public function getExperienceRange(): array
    {
        return [
            'min' => $this->min_experience_years ?? 0,
            'max' => $this->max_experience_years ?? 0,
            'description' => $this->getExperienceDescription(),
        ];
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
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'min_experience_years' => 'integer',
            'max_experience_years' => 'integer',
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

        // Clear cache when career level is updated
        static::updated(function ($careerLevel) {
            cache()->forget("career_level.{$careerLevel->id}");
            cache()->forget('career_levels.popular');
            cache()->forget('career_levels.trending');
            cache()->tags(['career_levels', 'career_level-'.$careerLevel->id])->flush();
        });

        // Clear cache when career level is deleted
        static::deleted(function ($careerLevel) {
            cache()->forget("career_level.{$careerLevel->id}");
            cache()->forget('career_levels.popular');
            cache()->forget('career_levels.trending');
            cache()->tags(['career_levels', 'career_level-'.$careerLevel->id])->flush();
        });
    }

    /**
     * Get experience description.
     */
    private function getExperienceDescription(): string
    {
        $min = $this->min_experience_years ?? 0;
        $max = $this->max_experience_years ?? 0;

        if ($min === 0 && $max === 0) {
            return __('career_level.no_experience_required');
        }

        if ($min === $max) {
            return __('career_level.exactly_years', ['years' => $min]);
        }

        return __('career_level.years_range', ['min' => $min, 'max' => $max]);
    }
}
