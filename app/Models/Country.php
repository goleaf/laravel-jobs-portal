<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 *
 * @property int $id
 * @property string $name
 * @property string $short_code
 * @property string|null $phone_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country wherePhoneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

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




    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }

    /**
     * Scope for active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive countries.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default countries.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom countries.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for countries with states.
     */
    public function scopeWithStates($query)
    {
        return $query->has('states');
    }

    /**
     * Scope for countries without states.
     */
    public function scopeWithoutStates($query)
    {
        return $query->doesntHave('states');
    }

    /**
     * Scope for countries with cities.
     */
    public function scopeWithCities($query)
    {
        return $query->has('cities');
    }

    /**
     * Scope for countries with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope for countries with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for searching countries.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('iso_code', 'like', "%{$term}%");
    }

    /**
     * Scope for recent countries.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old countries.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular countries (with most companies).
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
     * Scope for countries by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope for countries by ISO code.
     */
    public function scopeByIsoCode($query, string $isoCode)
    {
        return $query->where('iso_code', $isoCode);
    }

    /**
     * Scope for countries by phone code.
     */
    public function scopeByPhoneCode($query, string $phoneCode)
    {
        return $query->where('phone_code', $phoneCode);
    }

    /**
     * Scope for countries by currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope for European countries.
     */
    public function scopeEuropean($query)
    {
        $europeanCodes = ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'];
        return $query->whereIn('iso_code', $europeanCodes);
    }

    /**
     * Scope for North American countries.
     */
    public function scopeNorthAmerican($query)
    {
        $northAmericanCodes = ['US', 'CA', 'MX'];
        return $query->whereIn('iso_code', $northAmericanCodes);
    }
}
