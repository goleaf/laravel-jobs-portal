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
    
    protected $table = 'cities';
    const STATE = '';

    protected $fillable = [
        'state_id',
        'name',
    ];

    public static $rules = [
        'name' => 'required|max:180|unique:cities,name',
        'state_id' => 'required',
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
            'state_id' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
     * Scope for cities by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for cities by country through state.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->whereHas('state', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Scope for cities with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->whereHas('users');
    }

    /**
     * Scope for searching cities by name.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope for alphabetically ordered cities.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for popular cities (with most users).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('users')
                    ->orderByDesc('users_count')
                    ->limit($limit);
    }

    /**
     * Scope for major cities (with many users).
     */
    public function scopeMajor($query, int $minUsers = 50)
    {
        return $query->withCount('users')
                    ->having('users_count', '>=', $minUsers)
                    ->orderByDesc('users_count');
    }
}
