<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class State
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\State whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property-read \App\Models\Country $country
 */
class State extends Model
{
    use HasFactory;
    const COUNTIES = '';
    protected $table = 'states';

    protected $fillable = [
        'id',
        'country_id',
        'name',
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
            'country_id' => 'integer',
            'name' => 'string',
            'code' => 'string',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public static $rules = [
        'name' => 'required|max:180|unique:states,name',
        'country_id' => 'required',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'state_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'state_id');
    }

    /**
     * Scope for active states.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive states.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for states by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for states with cities.
     */
    public function scopeWithCities($query)
    {
        return $query->has('cities');
    }

    /**
     * Scope for states without cities.
     */
    public function scopeWithoutCities($query)
    {
        return $query->doesntHave('cities');
    }

    /**
     * Scope for states with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope for states with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for searching states.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
    }

    /**
     * Scope for recent states.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old states.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular states (with most companies).
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
     * Scope for states by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope for states with active country.
     */
    public function scopeWithActiveCountry($query)
    {
        return $query->whereHas('country', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for states in specific countries.
     */
    public function scopeInCountries($query, array $countryIds)
    {
        return $query->whereIn('country_id', $countryIds);
    }

    /**
     * Scope for states with most cities.
     */
    public function scopeWithMostCities($query, int $limit = 10)
    {
        return $query->withCount('cities')
                    ->orderBy('cities_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for metropolitan states.
     */
    public function scopeMetropolitan($query)
    {
        return $query->where('name', 'like', '%metro%')
                    ->orWhere('name', 'like', '%capital%')
                    ->orWhere('name', 'like', '%city%');
    }
}
