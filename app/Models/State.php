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
     * Scope for states by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for states with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->whereHas('users');
    }

    /**
     * Scope for states with cities.
     */
    public function scopeWithCities($query)
    {
        return $query->whereHas('cities');
    }

    /**
     * Scope for searching states by name.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope for alphabetically ordered states.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for popular states (with most users).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('users')
                    ->orderByDesc('users_count')
                    ->limit($limit);
    }
}
