<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class CompanySize
 *
 * @property int $id
 * @property string $size
 * @property string $name
 * @property string $description
 * @property bool $is_active
 * @property bool $is_default
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read int|null $companies_count
 */
class CompanySize extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public $table = 'company_sizes';

    /**
     * Status constants
     */
    public const ACTIVE = 1;
    public const INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'size',
        'name', 
        'description',
        'is_active',
        'is_default',
        'order',
        'min_employees',
        'max_employees',
        'display_name',
        'short_description'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'order' => 'integer',
            'min_employees' => 'integer',
            'max_employees' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Activity log configuration for spatie/laravel-activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['size', 'name', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Company size has been {$eventName}");
    }

    /**
     * Get the companies for the company size.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'company_size_id');
    }

    /**
     * Scope a query to only include active company sizes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive company sizes.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to order by order field.
     */
    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('order', $direction)->orderBy('name', $direction);
    }

    /**
     * Scope a query to search company sizes by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('size', 'like', '%' . $search . '%')
                     ->orWhere('description', 'like', '%' . $search . '%');
    }

    /**
     * Get the company count for this size.
     */
    public function getCompanyCountAttribute()
    {
        return $this->companies()->count();
    }
}
