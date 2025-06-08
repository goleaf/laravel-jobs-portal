<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CompanySize
 *
 * @version June 20, 2020, 5:43 am UTC
 *
 * @property string $size
 * @property int $id
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanySize whereSize($value)
 */
class CompanySize extends Model
{
    use HasFactory;

    public $table = 'company_sizes';

    public $fillable = [
        'size',
        'is_default',
        'is_active',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'size' => 'required|unique:company_sizes,size|regex:/^\d*-*\d*$/',
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
        'size' => 'string',
        'is_default' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get companies that belong to this size.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Scope for active company sizes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive company sizes.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default company sizes.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom company sizes.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for company sizes with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->whereHas('companies');
    }

    /**
     * Scope for company sizes with active companies.
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching company sizes by size value.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('size', 'like', "%{$term}%");
    }

    /**
     * Scope for popular company sizes (with most companies).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('companies')
                    ->orderByDesc('companies_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered company sizes.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('size', 'asc');
    }

    /**
     * Scope for recently created company sizes.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for small company sizes (typically 1-50 employees).
     */
    public function scopeSmall($query)
    {
        return $query->where('size', 'like', '%1-%')
                    ->orWhere('size', 'like', '%2-%')
                    ->orWhere('size', 'like', '%3-%')
                    ->orWhere('size', 'like', '%4-%')
                    ->orWhere('size', 'like', '%5-%')
                    ->orWhere('size', '=', '1-10')
                    ->orWhere('size', '=', '11-50');
    }

    /**
     * Scope for medium company sizes (typically 51-250 employees).
     */
    public function scopeMedium($query)
    {
        return $query->where('size', 'like', '%5_-%')
                    ->orWhere('size', 'like', '%1__-%')
                    ->orWhere('size', 'like', '%2__-%')
                    ->orWhere('size', '=', '51-200')
                    ->orWhere('size', '=', '201-500');
    }

    /**
     * Scope for large company sizes (typically 500+ employees).
     */
    public function scopeLarge($query)
    {
        return $query->where('size', 'like', '%500%')
                    ->orWhere('size', 'like', '%1000%')
                    ->orWhere('size', 'like', '%+%');
    }
}
