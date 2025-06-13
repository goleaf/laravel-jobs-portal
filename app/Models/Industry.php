<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Industry
 *
 * @version June 20, 2020, 5:43 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read int|null $companies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 * @property-read int|null $candidates_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_count
 * @property-read mixed $formatted_usage_stats
 * @property-read mixed $market_presence
 *
 * @method static Builder|Industry newModelQuery()
 * @method static Builder|Industry newQuery()
 * @method static Builder|Industry query()
 * @method static Builder|Industry whereCreatedAt($value)
 * @method static Builder|Industry whereId($value)
 * @method static Builder|Industry whereName($value)
 * @method static Builder|Industry whereDescription($value)
 * @method static Builder|Industry whereIsDefault($value)
 * @method static Builder|Industry whereIsActive($value)
 * @method static Builder|Industry whereUpdatedAt($value)
 * @method static Builder|Industry active()
 * @method static Builder|Industry inactive()
 * @method static Builder|Industry default()
 * @method static Builder|Industry custom()
 * @method static Builder|Industry withCompanies()
 * @method static Builder|Industry withActiveCompanies()
 * @method static Builder|Industry withCandidates()
 * @method static Builder|Industry withActiveCandidates()
 * @method static Builder|Industry withJobs()
 * @method static Builder|Industry withActiveJobs()
 * @method static Builder|Industry search(string $term)
 * @method static Builder|Industry popular(int $limit = 10)
 * @method static Builder|Industry alphabetical()
 * @method static Builder|Industry recent(int $days = 30)
 * @method static Builder|Industry trending()
 * @method static Builder|Industry highGrowth()
 * @method static Builder|Industry emerging()
 * @method static Builder|Industry established()
 * @method static Builder|Industry minUsage(int $count = 1)
 *
 * @mixin \Eloquent
 */
class Industry extends Model
{
    use HasFactory, LogsActivity;

    public $table = 'industries';

    public $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
        'slug',
        'icon',
        'color',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include old records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("created_at", "asc");
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when industry is updated
        static::updated(function ($industry) {
            cache()->forget("industry.{$industry->id}");
            cache()->forget("industries.popular");
            cache()->forget("industries.trending");
            cache()->tags(['industries', 'industry-' . $industry->id])->flush();
        });

        // Clear cache when industry is deleted
        static::deleted(function ($industry) {
            cache()->forget("industry.{$industry->id}");
            cache()->forget("industries.popular");
            cache()->forget("industries.trending");
            cache()->tags(['industries', 'industry-' . $industry->id])->flush();
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
        return cache()->remember("industry.{$this->id}.usage_count", 3600, function () {
            return $this->companies_count + $this->candidates_count;
        });
    }

    /**
     * Get formatted usage statistics.
     */
    public function getFormattedUsageStatsAttribute(): array
    {
        return cache()->remember("industry.{$this->id}.formatted_usage_stats", 3600, function () {
            return [
                'companies' => $this->companies()->count(),
                'candidates' => $this->candidates()->count(),
                'jobs' => $this->jobs()->count(),
                'total_usage' => $this->usage_count,
                'market_presence' => $this->market_presence,
            ];
        });
    }

    /**
     * Get market presence level.
     */
    public function getMarketPresenceAttribute(): string
    {
        $companiesCount = $this->companies()->count();
        
        return match (true) {
            $companiesCount >= 100 => __('industry.dominant_presence'),
            $companiesCount >= 50 => __('industry.strong_presence'),
            $companiesCount >= 20 => __('industry.moderate_presence'),
            $companiesCount >= 5 => __('industry.emerging_presence'),
            default => __('industry.minimal_presence')
        };
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'industry_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'industry_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasManyThrough(Job::class, Company::class, 'industry_id', 'company_id');
    }

    /**
     * Scope for active industries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive industries.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default industries.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom industries.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for industries with companies.
     */
    public function scopeWithCompanies(Builder $query): Builder
    {
        return $query->whereHas('companies');
    }

    /**
     * Scope for industries with active companies.
     */
    public function scopeWithActiveCompanies(Builder $query): Builder
    {
        return $query->whereHas('companies', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for industries with candidates.
     */
    public function scopeWithCandidates(Builder $query): Builder
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for industries with active candidates.
     */
    public function scopeWithActiveCandidates(Builder $query): Builder
    {
        return $query->whereHas('candidates', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for industries with jobs.
     */
    public function scopeWithJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for industries with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('jobs', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for searching industries by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular industries (with most companies).
     */
    public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->withCount(['companies', 'candidates'])
                    ->orderByDesc('companies_count')
                    ->orderByDesc('candidates_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered industries.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for recent industries.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderByDesc('created_at');
    }

    /**
     * Scope for trending industries.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
                        'companies' => function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30));
                        },
                        'candidates' => function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30));
                        }
                    ])
                    ->orderByDesc('companies_count')
                    ->orderByDesc('candidates_count');
    }

    /**
     * Scope for high growth industries.
     */
    public function scopeHighGrowth(Builder $query): Builder
    {
        return $query->withCount([
                        'companies' => function ($q) {
                            $q->where('created_at', '>=', now()->subDays(90));
                        }
                    ])
                    ->having('companies_count', '>=', 5)
                    ->orderByDesc('companies_count');
    }

    /**
     * Scope for emerging industries.
     */
    public function scopeEmerging(Builder $query): Builder
    {
        return $query->withCount('companies')
                    ->whereBetween('companies_count', [5, 20])
                    ->where('created_at', '>=', now()->subYear())
                    ->orderByDesc('created_at');
    }

    /**
     * Scope for established industries.
     */
    public function scopeEstablished(Builder $query): Builder
    {
        return $query->withCount('companies')
                    ->having('companies_count', '>=', 20)
                    ->where('created_at', '<=', now()->subYears(2))
                    ->orderByDesc('companies_count');
    }

    /**
     * Scope for industries with minimum usage.
     */
    public function scopeMinUsage(Builder $query, int $count = 1): Builder
    {
        return $query->withCount(['companies', 'candidates'])
                    ->havingRaw('(companies_count + candidates_count) >= ?', [$count]);
    }

    /**
     * Check if industry is established.
     */
    public function isEstablished(): bool
    {
        return $this->companies()->count() >= 20 && 
               $this->created_at <= now()->subYears(2);
    }

    /**
     * Check if industry is emerging.
     */
    public function isEmerging(): bool
    {
        $companiesCount = $this->companies()->count();
        return $companiesCount >= 5 && 
               $companiesCount <= 20 && 
               $this->created_at >= now()->subYear();
    }

    /**
     * Get industry growth rate.
     */
    public function getGrowthRate(): float
    {
        return cache()->remember("industry.{$this->id}.growth_rate", 3600, function () {
            $currentQuarter = $this->companies()->where('created_at', '>=', now()->subDays(90))->count();
            $previousQuarter = $this->companies()->whereBetween('created_at', [
                now()->subDays(180),
                now()->subDays(90)
            ])->count();
            
            if ($previousQuarter === 0) {
                return $currentQuarter > 0 ? 100.0 : 0.0;
            }
            
            return round((($currentQuarter - $previousQuarter) / $previousQuarter) * 100, 2);
        });
    }

    /**
     * Get related industries.
     */
    public function getRelatedIndustries(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return cache()->remember("industry.{$this->id}.related", 3600, function () use ($limit) {
            return static::where('id', '!=', $this->id)
                          ->active()
                          ->withCount('companies')
                          ->orderByDesc('companies_count')
                          ->limit($limit)
                          ->get();
        });
    }

    /**
     * Get top companies in this industry.
     */
    public function getTopCompanies(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return cache()->remember("industry.{$this->id}.top_companies", 3600, function () use ($limit) {
            return $this->companies()
                        ->active()
                        ->withCount('jobs')
                        ->orderByDesc('jobs_count')
                        ->limit($limit)
                        ->get();
        });
    }
}
