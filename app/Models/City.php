<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * City Model - Enhanced with Enhanced patterns.
 *
 * @property int                    $id
 * @property int                    $state_id
 * @property string                 $name
 * @property bool                   $is_active
 * @property bool                   $is_featured
 * @property bool                   $is_metropolitan
 * @property bool                   $is_major
 * @property null|float             $latitude
 * @property null|float             $longitude
 * @property null|string            $timezone
 * @property null|int               $population
 * @property null|Carbon            $created_at
 * @property null|Carbon            $updated_at
 * @property State                  $state
 * @property Country                $country
 * @property Collection|User[]      $users
 * @property Collection|Company[]   $companies
 * @property Collection|Job[]       $jobs
 * @property Candidate[]|Collection $candidates
 * @property string                 $full_name
 * @property string                 $display_name
 * @property null|string            $coordinates
 * @property string                 $population_category
 * @property int                    $companies_count
 * @property int                    $active_companies_count
 * @property int                    $jobs_count
 * @property int                    $active_jobs_count
 * @property int                    $candidates_count
 * @property int                    $active_candidates_count
 * @property int                    $users_count
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder byState(int $stateId)
 * @method static \Illuminate\Database\Eloquent\Builder byCountry(int $countryId)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder popularByJobs(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder popularByCandidates(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder withCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder withoutCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder major()
 * @method static \Illuminate\Database\Eloquent\Builder metropolitan()
 * @method static \Illuminate\Database\Eloquent\Builder nonMetropolitan()
 * @method static \Illuminate\Database\Eloquent\Builder small()
 * @method static \Illuminate\Database\Eloquent\Builder medium()
 * @method static \Illuminate\Database\Eloquent\Builder large()
 * @method static \Illuminate\Database\Eloquent\Builder populationGreaterThan(int $population)
 * @method static \Illuminate\Database\Eloquent\Builder populationLessThan(int $population)
 * @method static \Illuminate\Database\Eloquent\Builder byPopulationRange(int $min, int $max)
 * @method static \Illuminate\Database\Eloquent\Builder byTimezone(string $timezone)
 * @method static \Illuminate\Database\Eloquent\Builder near(float $latitude, float $longitude, float $radius = 50)
 * @method static \Illuminate\Database\Eloquent\Builder withinBounds(float $northLat, float $southLat, float $eastLng, float $westLng)
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Validation rules for creating cities.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:180|unique:cities,name',
        'state_id' => 'required|integer|exists:states,id',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_metropolitan' => 'boolean',
        'is_major' => 'boolean',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'timezone' => 'nullable|string|max:50',
        'population' => 'nullable|integer|min:0',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_id',
        'name',
        'is_active',
        'is_featured',
        'latitude',
        'longitude',
        'timezone',
        'population',
        'is_metropolitan',
        'is_major',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['deleted_at'];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'state_id', 'is_active', 'is_featured', 'is_metropolitan', 'is_major'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Update validation rules for cities.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:180|unique:cities,name,'.$id,
            'state_id' => 'required|integer|exists:states,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_metropolitan' => 'boolean',
            'is_major' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'timezone' => 'nullable|string|max:50',
            'population' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the state that owns the city.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Get the country through the state.
     */
    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

    /**
     * Get the users for the city.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'city_id');
    }

    /**
     * Get the companies for the city.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'city_id');
    }

    /**
     * Get the jobs for the city.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'city_id');
    }

    /**
     * Get the candidates for the city.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'city_id');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active cities.
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive cities.
     *
     * @param mixed $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured cities.
     *
     * @param mixed $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured cities.
     *
     * @param mixed $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    // =============================================
    // SCOPES - Location & Geography
    // =============================================

    /**
     * Scope a query to filter cities by state.
     *
     * @param mixed $query
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope a query to filter cities by multiple states.
     *
     * @param mixed $query
     */
    public function scopeInStates($query, array $stateIds)
    {
        return $query->whereIn('state_id', $stateIds);
    }

    /**
     * Scope a query to filter cities by country.
     *
     * @param mixed $query
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->whereHas('state', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Scope a query to get cities with coordinates.
     *
     * @param mixed $query
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
        ;
    }

    /**
     * Scope a query to get cities without coordinates.
     *
     * @param mixed $query
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('latitude')
                ->orWhereNull('longitude')
            ;
        });
    }

    /**
     * Scope a query to filter cities by timezone.
     *
     * @param mixed $query
     */
    public function scopeByTimezone($query, string $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    /**
     * Scope a query for cities near coordinates.
     *
     * @param mixed $query
     */
    public function scopeNear($query, float $latitude, float $longitude, float $radius = 50)
    {
        return $query->whereRaw(
            'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?',
            [$longitude, $latitude, $radius * 1000]
        );
    }

    /**
     * Scope a query for cities within bounding box.
     *
     * @param mixed $query
     */
    public function scopeWithinBounds($query, float $northLat, float $southLat, float $eastLng, float $westLng)
    {
        return $query->whereBetween('latitude', [$southLat, $northLat])
            ->whereBetween('longitude', [$westLng, $eastLng])
        ;
    }

    // =============================================
    // SCOPES - Population & Size
    // =============================================

    /**
     * Scope a query to filter cities by population range.
     *
     * @param mixed $query
     */
    public function scopeByPopulationRange($query, int $min, int $max)
    {
        return $query->whereBetween('population', [$min, $max]);
    }

    /**
     * Scope a query to filter cities with population greater than.
     *
     * @param mixed $query
     */
    public function scopePopulationGreaterThan($query, int $population)
    {
        return $query->where('population', '>', $population);
    }

    /**
     * Scope a query to filter cities with population less than.
     *
     * @param mixed $query
     */
    public function scopePopulationLessThan($query, int $population)
    {
        return $query->where('population', '<', $population);
    }

    /**
     * Scope a query to get major cities (population > 1 million).
     *
     * @param mixed $query
     */
    public function scopeMajor($query)
    {
        return $query->where(function ($q) {
            $q->where('is_major', true)
                ->orWhere('population', '>', 1000000)
            ;
        });
    }

    /**
     * Scope a query to get metropolitan cities.
     *
     * @param mixed $query
     */
    public function scopeMetropolitan($query)
    {
        return $query->where(function ($q) {
            $q->where('is_metropolitan', true)
                ->orWhere('population', '>', 500000)
            ;
        });
    }

    /**
     * Scope a query to get non-metropolitan cities.
     *
     * @param mixed $query
     */
    public function scopeNonMetropolitan($query)
    {
        return $query->where('is_metropolitan', false)
            ->where('population', '<=', 500000)
        ;
    }

    /**
     * Scope a query to get small cities.
     *
     * @param mixed $query
     */
    public function scopeSmall($query)
    {
        return $query->where('population', '<', 100000);
    }

    /**
     * Scope a query to get medium cities.
     *
     * @param mixed $query
     */
    public function scopeMedium($query)
    {
        return $query->whereBetween('population', [100000, 500000]);
    }

    /**
     * Scope a query to get large cities.
     *
     * @param mixed $query
     */
    public function scopeLarge($query)
    {
        return $query->whereBetween('population', [500000, 1000000]);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope a query to search cities by name.
     *
     * @param mixed $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope a query to get recent cities.
     *
     * @param mixed $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get old cities.
     *
     * @param mixed $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to order cities alphabetically.
     *
     * @param mixed $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    // =============================================
    // SCOPES - Relationships & Popularity
    // =============================================

    /**
     * Scope a query to include cities with companies.
     *
     * @param mixed $query
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to include cities with active companies.
     *
     * @param mixed $query
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with jobs.
     *
     * @param mixed $query
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include cities with active jobs.
     *
     * @param mixed $query
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope a query to include cities with candidates.
     *
     * @param mixed $query
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include cities with active candidates.
     *
     * @param mixed $query
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with users.
     *
     * @param mixed $query
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope a query to include cities with active state.
     *
     * @param mixed $query
     */
    public function scopeWithActiveState($query)
    {
        return $query->whereHas('state', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with active country.
     *
     * @param mixed $query
     */
    public function scopeWithActiveCountry($query)
    {
        return $query->whereHas('state.country', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to get popular cities (with most companies).
     *
     * @param mixed $query
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['companies' => function ($q) {
            $q->where('is_active', true);
        }])
            ->orderBy('companies_count', 'desc')
            ->limit($limit)
        ;
    }

    /**
     * Scope a query to get popular cities by jobs.
     *
     * @param mixed $query
     */
    public function scopePopularByJobs($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('status', 'active');
        }])
            ->orderBy('jobs_count', 'desc')
            ->limit($limit)
        ;
    }

    /**
     * Scope a query to get popular cities by candidates.
     *
     * @param mixed $query
     */
    public function scopePopularByCandidates($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('is_active', true);
        }])
            ->orderBy('candidates_count', 'desc')
            ->limit($limit)
        ;
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached cities for a state.
     */
    public static function getCachedByState(int $stateId): Collection
    {
        return Cache::remember(
            "cities_state_{$stateId}",
            now()->addHours(24),
            fn () => static::active()
                ->byState($stateId)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached cities for a country.
     */
    public static function getCachedByCountry(int $countryId): Collection
    {
        return Cache::remember(
            "cities_country_{$countryId}",
            now()->addHours(24),
            fn () => static::active()
                ->byCountry($countryId)
                ->with('state')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached active cities.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember(
            'cities_active',
            now()->addHours(12),
            fn () => static::active()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached featured cities.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember(
            'cities_featured',
            now()->addHours(6),
            fn () => static::active()
                ->featured()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached major cities.
     */
    public static function getCachedMajor(): Collection
    {
        return Cache::remember(
            'cities_major',
            now()->addHours(12),
            fn () => static::active()
                ->major()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get full display name with state and country.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name.', '.$this->state->name.', '.$this->state->country->name;
    }

    /**
     * Get display name with state.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name.', '.$this->state->name;
    }

    /**
     * Check if city has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get coordinate string.
     */
    public function getCoordinatesAttribute(): ?string
    {
        return $this->hasCoordinates() ? "{$this->latitude}, {$this->longitude}" : null;
    }

    /**
     * Get population size category.
     */
    public function getPopulationCategoryAttribute(): string
    {
        if (!$this->population) {
            return 'unknown';
        }

        if ($this->population >= 1000000) {
            return 'major';
        }
        if ($this->population >= 500000) {
            return 'metropolitan';
        }
        if ($this->population >= 100000) {
            return 'large';
        }
        if ($this->population >= 50000) {
            return 'medium';
        }

        return 'small';
    }

    /**
     * Check if city is major.
     */
    public function isMajor(): bool
    {
        return $this->is_major || ($this->population && $this->population > 1000000);
    }

    /**
     * Check if city is metropolitan.
     */
    public function isMetropolitan(): bool
    {
        return $this->is_metropolitan || ($this->population && $this->population > 500000);
    }

    /**
     * Get companies count.
     */
    public function getCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "city_{$this->id}_companies_count",
            now()->addHours(6),
            fn () => $this->companies()->count()
        );
    }

    /**
     * Get active companies count.
     */
    public function getActiveCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "city_{$this->id}_active_companies_count",
            now()->addHours(6),
            fn () => $this->companies()->where('is_active', true)->count()
        );
    }

    /**
     * Get jobs count.
     */
    public function getJobsCountAttribute(): int
    {
        return Cache::remember(
            "city_{$this->id}_jobs_count",
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
            "city_{$this->id}_active_jobs_count",
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
            "city_{$this->id}_candidates_count",
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
            "city_{$this->id}_active_candidates_count",
            now()->addHours(6),
            fn () => $this->candidates()->where('is_active', true)->count()
        );
    }

    /**
     * Get users count.
     */
    public function getUsersCountAttribute(): int
    {
        return Cache::remember(
            "city_{$this->id}_users_count",
            now()->addHours(6),
            fn () => $this->users()->count()
        );
    }

    /**
     * Calculate distance to another city in kilometers.
     */
    public function distanceTo(City $city): ?float
    {
        if (!$this->hasCoordinates() || !$city->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($city->latitude);
        $lon2 = deg2rad($city->longitude);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2)
             + cos($lat1) * cos($lat2)
             * sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        $patterns = [
            'cities_active',
            'cities_featured',
            'cities_major',
            "cities_state_{$this->state_id}",
            "city_{$this->id}_companies_count",
            "city_{$this->id}_active_companies_count",
            "city_{$this->id}_jobs_count",
            "city_{$this->id}_active_jobs_count",
            "city_{$this->id}_candidates_count",
            "city_{$this->id}_active_candidates_count",
            "city_{$this->id}_users_count",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        if ($this->state && $this->state->country_id) {
            Cache::forget("cities_country_{$this->state->country_id}");
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
            'is_featured' => 'boolean',
            'is_metropolitan' => 'boolean',
            'is_major' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'population' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($city) {
            $city->clearCaches();
        });

        static::deleted(function ($city) {
            $city->clearCaches();
        });
    }
}
