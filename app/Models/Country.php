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
class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    protected $fillable = [
        'short_code',
        'name',
        'phone_code',
    ];

    public static $rules = [
        'name' => 'required|max:180|unique:countries,name',
        'short_code' => 'required|unique:countries,short_code',
        'phone_code' => 'nullable|numeric|unique:countries,phone_code',
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
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

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
     * Scope for countries with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->whereHas('users');
    }

    /**
     * Scope for countries with states.
     */
    public function scopeWithStates($query)
    {
        return $query->whereHas('states');
    }

    /**
     * Scope for searching countries by name.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('short_code', 'like', "%{$term}%");
    }

    /**
     * Scope for countries by short code.
     */
    public function scopeByShortCode($query, string $shortCode)
    {
        return $query->where('short_code', $shortCode);
    }

    /**
     * Scope for countries with phone codes.
     */
    public function scopeWithPhoneCode($query)
    {
        return $query->whereNotNull('phone_code')
                    ->where('phone_code', '!=', '');
    }

    /**
     * Scope for alphabetically ordered countries.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for popular countries (with most users).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('users')
                    ->orderByDesc('users_count')
                    ->limit($limit);
    }
}
