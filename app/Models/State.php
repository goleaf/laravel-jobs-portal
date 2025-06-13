<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class State
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $code
 * @property bool $is_active
 * @property bool $is_featured
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $timezone
 * @property int|null $population
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\City[] $cities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State featured()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State byCountry(int $countryId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State popular(int $limit = 10)
 *
 * @mixin \Eloquent
 */
class State extends Model
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
        'country_id',
        'name',
        'code',
        'is_active',
        'is_featured',
        'latitude',
        'longitude',
        'timezone',
        'population'
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
            ->logOnly(['name', 'code', 'is_active', 'is_featured', 'country_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for the model.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:180|unique:states,name',
        'country_id' => 'required|integer|exists:countries,id',
        'code' => 'nullable|string|max:10|unique:states,code',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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
            'name' => 'required|string|max:180|unique:states,name,' . $id,
            'country_id' => 'required|integer|exists:countries,id',
            'code' => 'nullable|string|max:10|unique:states,code,' . $id,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'timezone' => 'nullable|string|max:50',
            'population' => 'nullable|integer|min:0',
        ];
    }

    // RELATIONSHIPS

    /**
     * Get the country that owns the state.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the cities for the state.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'state_id');
    }

    /**
     * Get the users for the state.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'state_id');
    }

    /**
     * Get the companies for the state.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'state_id');
    }

    /**
     * Get the jobs for the state.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'state_id');
    }

    /**
     * Get the candidates for the state.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'state_id');
    }

    // SCOPES

    /**
     * Scope a query to only include active states.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive states.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured states.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured states.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to filter states by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope a query to filter states by multiple countries.
     */
    public function scopeInCountries($query, array $countryIds)
    {
        return $query->whereIn('country_id', $countryIds);
    }

    /**
     * Scope a query to filter states by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope a query to search states by name or code.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to get recent states.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get old states.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to get popular states (with most companies).
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
     * Scope a query to get popular states by jobs.
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
     * Scope a query to get popular states by candidates.
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
     * Scope a query to order states alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to include states with cities.
     */
    public function scopeWithCities($query)
    {
        return $query->has('cities');
    }

    /**
     * Scope a query to include states without cities.
     */
    public function scopeWithoutCities($query)
    {
        return $query->doesntHave('cities');
    }

    /**
     * Scope a query to include states with active cities.
     */
    public function scopeWithActiveCities($query)
    {
        return $query->whereHas('cities', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include states with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to include states with active companies.
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include states with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include states with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope a query to include states with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include states with active candidates.
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include states with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope a query to include states with active country.
     */
    public function scopeWithActiveCountry($query)
    {
        return $query->whereHas('country', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to get states with most cities.
     */
    public function scopeWithMostCities($query, int $limit = 10)
    {
        return $query->withCount(['cities' => function ($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('cities_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope a query to get metropolitan states.
     */
    public function scopeMetropolitan($query)
    {
        return $query->where(function ($q) {
            $q->where('name', 'like', '%metro%')
              ->orWhere('name', 'like', '%capital%')
              ->orWhere('name', 'like', '%city%');
        });
    }

    /**
     * Scope a query to filter states by timezone.
     */
    public function scopeByTimezone($query, string $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    /**
     * Scope a query to filter states with population greater than.
     */
    public function scopePopulationGreaterThan($query, int $population)
    {
        return $query->where('population', '>', $population);
    }

    /**
     * Scope a query to filter states with population less than.
     */
    public function scopePopulationLessThan($query, int $population)
    {
        return $query->where('population', '<', $population);
    }

    /**
     * Scope a query to filter states by population range.
     */
    public function scopePopulationBetween($query, int $min, int $max)
    {
        return $query->whereBetween('population', [$min, $max]);
    }

    /**
     * Scope a query to get states with coordinates.
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
    }

    /**
     * Scope a query to get states without coordinates.
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude');
        });
    }

    // HELPER METHODS

    /**
     * Get cached states for a country.
     */
    public static function getCachedByCountry(int $countryId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "states_country_{$countryId}",
            now()->addHours(24),
            fn() => static::active()
                ->byCountry($countryId)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached active states.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'states_active',
            now()->addHours(12),
            fn() => static::active()
                ->with('country')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached featured states.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'states_featured',
            now()->addHours(6),
            fn() => static::active()
                ->featured()
                ->with('country')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get full display name with country.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->country->name;
    }

    /**
     * Get display name with code.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->code ? "{$this->name} ({$this->code})" : $this->name;
    }

    /**
     * Check if state has coordinates.
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
     * Get cities count.
     */
    public function getCitiesCountAttribute(): int
    {
        return $this->cities()->count();
    }

    /**
     * Get active cities count.
     */
    public function getActiveCitiesCountAttribute(): int
    {
        return $this->cities()->where('is_active', true)->count();
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
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('states_active');
        Cache::forget('states_featured');
        Cache::forget("states_country_{$this->country_id}");
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($state) {
            $state->clearCaches();
        });

        static::deleted(function ($state) {
            $state->clearCaches();
        });
    }
}
