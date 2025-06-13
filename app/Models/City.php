<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class City
 *
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_metropolitan
 * @property bool $is_major
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $timezone
 * @property int|null $population
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\State $state
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City featured()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City byState(int $stateId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City byCountry(int $countryId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City popular(int $limit = 10)
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

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
        'is_major'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

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
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'state_id', 'is_active', 'is_featured', 'is_metropolitan', 'is_major'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for the model.
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
     * Update validation rules for the model.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:180|unique:cities,name,' . $id,
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

    // RELATIONSHIPS

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

    // SCOPES

    /**
     * Scope a query to only include active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive cities.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured cities.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured cities.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to filter cities by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope a query to filter cities by multiple states.
     */
    public function scopeInStates($query, array $stateIds)
    {
        return $query->whereIn('state_id', $stateIds);
    }

    /**
     * Scope a query to filter cities by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->whereHas('state', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Scope a query to search cities by name.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope a query to get recent cities.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get old cities.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to get popular cities (with most companies).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['companies' => function ($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('companies_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope a query to get popular cities by jobs.
     */
    public function scopePopularByJobs($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('status', 'active');
        }])
        ->orderBy('jobs_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope a query to get popular cities by candidates.
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
     * Scope a query to order cities alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to include cities with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to include cities with active companies.
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include cities with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope a query to include cities with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include cities with active candidates.
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope a query to include cities with active state.
     */
    public function scopeWithActiveState($query)
    {
        return $query->whereHas('state', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include cities with active country.
     */
    public function scopeWithActiveCountry($query)
    {
        return $query->whereHas('state.country', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to get cities with coordinates.
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
    }

    /**
     * Scope a query to get cities without coordinates.
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude');
        });
    }

    /**
     * Scope a query to filter cities by population range.
     */
    public function scopeByPopulationRange($query, int $min, int $max)
    {
        return $query->whereBetween('population', [$min, $max]);
    }

    /**
     * Scope a query to filter cities with population greater than.
     */
    public function scopePopulationGreaterThan($query, int $population)
    {
        return $query->where('population', '>', $population);
    }

    /**
     * Scope a query to filter cities with population less than.
     */
    public function scopePopulationLessThan($query, int $population)
    {
        return $query->where('population', '<', $population);
    }

    /**
     * Scope a query to get major cities (population > 1 million).
     */
    public function scopeMajor($query)
    {
        return $query->where(function ($q) {
            $q->where('is_major', true)
              ->orWhere('population', '>', 1000000);
        });
    }

    /**
     * Scope a query to get metropolitan cities.
     */
    public function scopeMetropolitan($query)
    {
        return $query->where(function ($q) {
            $q->where('is_metropolitan', true)
              ->orWhere('population', '>', 500000);
        });
    }

    /**
     * Scope a query to get non-metropolitan cities.
     */
    public function scopeNonMetropolitan($query)
    {
        return $query->where('is_metropolitan', false)
                    ->where('population', '<=', 500000);
    }

    /**
     * Scope a query to filter cities by timezone.
     */
    public function scopeByTimezone($query, string $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    /**
     * Scope a query for cities near coordinates.
     */
    public function scopeNear($query, float $latitude, float $longitude, float $radius = 50)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }

    /**
     * Scope a query for cities within bounding box.
     */
    public function scopeWithinBounds($query, float $northLat, float $southLat, float $eastLng, float $westLng)
    {
        return $query->whereBetween('latitude', [$southLat, $northLat])
                    ->whereBetween('longitude', [$westLng, $eastLng]);
    }

    /**
     * Scope a query to get small cities.
     */
    public function scopeSmall($query)
    {
        return $query->where('population', '<', 100000);
    }

    /**
     * Scope a query to get medium cities.
     */
    public function scopeMedium($query)
    {
        return $query->whereBetween('population', [100000, 500000]);
    }

    /**
     * Scope a query to get large cities.
     */
    public function scopeLarge($query)
    {
        return $query->whereBetween('population', [500000, 1000000]);
    }

    // HELPER METHODS

    /**
     * Get cached cities for a state.
     */
    public static function getCachedByState(int $stateId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "cities_state_{$stateId}",
            now()->addHours(24),
            fn() => static::active()
                ->byState($stateId)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached cities for a country.
     */
    public static function getCachedByCountry(int $countryId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "cities_country_{$countryId}",
            now()->addHours(24),
            fn() => static::active()
                ->byCountry($countryId)
                ->with('state')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached active cities.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'cities_active',
            now()->addHours(12),
            fn() => static::active()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached featured cities.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'cities_featured',
            now()->addHours(6),
            fn() => static::active()
                ->featured()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached major cities.
     */
    public static function getCachedMajor(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'cities_major',
            now()->addHours(12),
            fn() => static::active()
                ->major()
                ->with(['state', 'country'])
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get full display name with state and country.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->state->name . ', ' . $this->state->country->name;
    }

    /**
     * Get display name with state.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ', ' . $this->state->name;
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
        return $this->is_major || $this->population > 1000000;
    }

    /**
     * Check if city is metropolitan.
     */
    public function isMetropolitan(): bool
    {
        return $this->is_metropolitan || $this->population > 500000;
    }

    /**
     * Get companies count.
     */
    public function getCompaniesCountAttribute(): int
    {
        return $this->companies()->count();
    }

    /**
     * Get active companies count.
     */
    public function getActiveCompaniesCountAttribute(): int
    {
        return $this->companies()->where('is_active', true)->count();
    }

    /**
     * Get jobs count.
     */
    public function getJobsCountAttribute(): int
    {
        return $this->jobs()->count();
    }

    /**
     * Get active jobs count.
     */
    public function getActiveJobsCountAttribute(): int
    {
        return $this->jobs()->where('status', 'active')->count();
    }

    /**
     * Get candidates count.
     */
    public function getCandidatesCountAttribute(): int
    {
        return $this->candidates()->count();
    }

    /**
     * Get active candidates count.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return $this->candidates()->where('is_active', true)->count();
    }

    /**
     * Get users count.
     */
    public function getUsersCountAttribute(): int
    {
        return $this->users()->count();
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

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('cities_active');
        Cache::forget('cities_featured');
        Cache::forget('cities_major');
        Cache::forget("cities_state_{$this->state_id}");
        if ($this->state && $this->state->country_id) {
            Cache::forget("cities_country_{$this->state->country_id}");
        }
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
