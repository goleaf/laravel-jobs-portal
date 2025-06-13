<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class City
 *
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property-read \App\Models\State $state
 */
class City extends Model
{
    use HasFactory;

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
        ];
    }

    /**
     * Scope a query to only include featured records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("is_featured", true);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'city_id');
    }

    public function country()
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

    /**
     * Scope for active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive cities.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for cities by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for cities by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->whereHas('state', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Scope for cities with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope for cities with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for cities with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for searching cities.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope for recent cities.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old cities.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular cities (with most companies).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('companies')
                    ->orderBy('companies_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for cities with coordinates.
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
    }

    /**
     * Scope for cities without coordinates.
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->whereNull('latitude')
                    ->orWhereNull('longitude');
    }

    /**
     * Scope for cities by population range.
     */
    public function scopeByPopulationRange($query, int $min, int $max)
    {
        return $query->whereBetween('population', [$min, $max]);
    }

    /**
     * Scope for major cities (population > 1 million).
     */
    public function scopeMajor($query)
    {
        return $query->where('population', '>', 1000000);
    }

    /**
     * Scope for metropolitan cities.
     */
    public function scopeMetropolitan($query)
    {
        return $query->where('population', '>', 500000);
    }

    /**
     * Scope for cities with active state.
     */
    public function scopeWithActiveState($query)
    {
        return $query->whereHas('state', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for cities with active country.
     */
    public function scopeWithActiveCountry($query)
    {
        return $query->whereHas('state.country', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for cities in specific states.
     */
    public function scopeInStates($query, array $stateIds)
    {
        return $query->whereIn('state_id', $stateIds);
    }

    /**
     * Scope for cities near coordinates.
     */
    public function scopeNear($query, float $latitude, float $longitude, float $radius = 50)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }
}
