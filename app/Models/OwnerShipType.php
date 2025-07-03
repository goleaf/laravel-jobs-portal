<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class OwnerShipType.
 *
 * @version June 22, 2020, 9:47 am UTC
 *
 * @property int $id
 * @property string $name
 * @property null|string $description
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OwnerShipType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OwnerShipType extends Model
{
    use HasFactory;

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:150|unique:ownership_types,name',
        'description' => 'nullable',
    ];

    public $table = 'ownership_types';

    public $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    /**
     * Get companies that use this ownership type.
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'ownership_type_id');
    }

    /**
     * Scope for active ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for featured ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for searching by name or description.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for recent ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old ownership types.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for ownership types with companies.
     *
     * @param  mixed  $query
     */
    public function scopeWithCompanies($query)
    {
        return $query->whereHas('companies');
    }

    /**
     * Scope for ownership types with active companies.
     *
     * @param  mixed  $query
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for popular ownership types (most used by companies).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('companies')
            ->orderByDesc('companies_count')
            ->limit($limit);
    }

    /**
     * Scope for private ownership types.
     *
     * @param  mixed  $query
     */
    public function scopePrivate($query)
    {
        return $query->where('name', 'like', '%private%')
            ->orWhere('name', 'like', '%ltd%')
            ->orWhere('name', 'like', '%llc%');
    }

    /**
     * Scope for public ownership types.
     *
     * @param  mixed  $query
     */
    public function scopePublic($query)
    {
        return $query->where('name', 'like', '%public%')
            ->orWhere('name', 'like', '%plc%')
            ->orWhere('name', 'like', '%corp%');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',

            'id' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
