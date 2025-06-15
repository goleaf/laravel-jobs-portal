<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
    use HasFactory, LogsActivity;

    public $table = 'company_sizes';

    /**
     * Status constants
     */
    public const ACTIVE = 1;
    public const INACTIVE = 0;

    /**
     * Company size categories
     */
    public const CATEGORY_STARTUP = 'startup';
    public const CATEGORY_SMALL = 'small';
    public const CATEGORY_MEDIUM = 'medium';
    public const CATEGORY_LARGE = 'large';
    public const CATEGORY_ENTERPRISE = 'enterprise';

    /**
     * Standard employee ranges
     */
    public const STARTUP_MAX = 10;
    public const SMALL_MAX = 50;
    public const MEDIUM_MAX = 250;
    public const LARGE_MAX = 1000;

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
        'short_description',
        'is_featured',
        'color',
        'icon',
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
     * Scope a query to only include default company sizes.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom (non-default) company sizes.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope a query to only include recently created company sizes.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old company sizes.
     */
    public function scopeOld($query, $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope a query to only include company sizes that have companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to order company sizes alphabetically by name.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to get most popular company sizes by company count.
     */
    public function scopePopular($query, $limit = null)
    {
        $query = $query->withCount('companies')
                       ->orderBy('companies_count', 'desc');
        
        if ($limit) {
            $query = $query->limit($limit);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include small company sizes.
     */
    public function scopeSmall($query)
    {
        return $query->where('size', 'like', '%small%')
                     ->orWhere('size', 'like', '%startup%');
    }

    /**
     * Scope a query to only include medium company sizes.
     */
    public function scopeMedium($query)
    {
        return $query->where('size', 'like', '%medium%')
                     ->orWhere('size', 'like', '%mid%');
    }

    /**
     * Scope a query to only include large company sizes.
     */
    public function scopeLarge($query)
    {
        return $query->where('size', 'like', '%large%')
                     ->orWhere('size', 'like', '%enterprise%');
    }

    /**
     * Get the company count for this size.
     */
    public function getCompanyCountAttribute()
    {
        return $this->companies()->count();
    }

    /**
     * Get the active companies for the company size.
     */
    public function activeCompanies(): HasMany
    {
        return $this->companies()->where('companies.is_active', true);
    }

    /**
     * Get the featured companies for the company size.
     */
    public function featuredCompanies(): HasMany
    {
        return $this->companies()->where('companies.is_featured', true);
    }

    /**
     * Get the recent companies for the company size.
     */
    public function recentCompanies(): HasMany
    {
        return $this->companies()->where('companies.created_at', '>=', now()->subDays(30));
    }

    /**
     * Get the range description attribute.
     */
    public function getRangeDescriptionAttribute(): string
    {
        if ($this->min_employees && $this->max_employees) {
            return "{$this->min_employees}-{$this->max_employees} employees";
        } elseif ($this->min_employees) {
            return "{$this->min_employees}+ employees";
        } elseif ($this->max_employees) {
            return "Up to {$this->max_employees} employees";
        }
        return $this->name ?? $this->size;
    }

    /**
     * Get the employee range attribute.
     */
    public function getEmployeeRangeAttribute(): string
    {
        if ($this->min_employees && $this->max_employees) {
            return "{$this->min_employees}-{$this->max_employees}";
        } elseif ($this->min_employees) {
            return "{$this->min_employees}+";
        } elseif ($this->max_employees) {
            return "≤{$this->max_employees}";
        }
        return 'N/A';
    }

    /**
     * Get the size category attribute.
     */
    public function getSizeCategoryAttribute(): string
    {
        if (!$this->max_employees) {
            return self::CATEGORY_ENTERPRISE;
        }

        if ($this->max_employees <= self::STARTUP_MAX) {
            return self::CATEGORY_STARTUP;
        } elseif ($this->max_employees <= self::SMALL_MAX) {
            return self::CATEGORY_SMALL;
        } elseif ($this->max_employees <= self::MEDIUM_MAX) {
            return self::CATEGORY_MEDIUM;
        } elseif ($this->max_employees <= self::LARGE_MAX) {
            return self::CATEGORY_LARGE;
        }

        return self::CATEGORY_ENTERPRISE;
    }

    /**
     * Get companies count attribute.
     */
    public function getCompaniesCountAttribute(): int
    {
        return $this->companies()->count();
    }

    /**
     * Get active companies count attribute.
     */
    public function getActiveCompaniesCountAttribute(): int
    {
        return $this->activeCompanies()->count();
    }

    /**
     * Get featured companies count attribute.
     */
    public function getFeaturedCompaniesCountAttribute(): int
    {
        return $this->featuredCompanies()->count();
    }

    /**
     * Check if company size is startup category.
     */
    public function getIsStartupAttribute(): bool
    {
        return $this->size_category === self::CATEGORY_STARTUP;
    }

    /**
     * Check if company size is small category.
     */
    public function getIsSmallAttribute(): bool
    {
        return $this->size_category === self::CATEGORY_SMALL;
    }

    /**
     * Check if company size is medium category.
     */
    public function getIsMediumAttribute(): bool
    {
        return $this->size_category === self::CATEGORY_MEDIUM;
    }

    /**
     * Check if company size is large category.
     */
    public function getIsLargeAttribute(): bool
    {
        return $this->size_category === self::CATEGORY_LARGE;
    }

    /**
     * Check if company size is enterprise category.
     */
    public function getIsEnterpriseAttribute(): bool
    {
        return $this->size_category === self::CATEGORY_ENTERPRISE;
    }

    /**
     * Check if company size is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if company size is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if company size is default.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if company size has companies.
     */
    public function hasCompanies(): bool
    {
        return $this->companies()->count() > 0;
    }

    /**
     * Check if company size has active companies.
     */
    public function hasActiveCompanies(): bool
    {
        return $this->activeCompanies()->count() > 0;
    }

    /**
     * Check if company size has featured companies.
     */
    public function hasFeaturedCompanies(): bool
    {
        return $this->featuredCompanies()->count() > 0;
    }

    /**
     * Check if company size has a color.
     */
    public function hasColor(): bool
    {
        return !empty($this->color);
    }

    /**
     * Check if company size has an icon.
     */
    public function hasIcon(): bool
    {
        return !empty($this->icon);
    }

    /**
     * Check if employee count fits this size.
     */
    public function fitsEmployeeCount(int $employeeCount): bool
    {
        if ($this->min_employees && $employeeCount < $this->min_employees) {
            return false;
        }
        if ($this->max_employees && $employeeCount > $this->max_employees) {
            return false;
        }
        return true;
    }

    /**
     * Get badge HTML for the company size.
     */
    public function getBadgeHtml(): string
    {
        $color = $this->color ?: '#6c757d';
        $name = $this->display_name ?: $this->name;
        return "<span class=\"badge\" style=\"background-color: {$color};\">{$name}</span>";
    }

    /**
     * Get icon HTML.
     */
    public function getIconHtml(): string
    {
        if ($this->icon) {
            return "<i class=\"{$this->icon}\"></i>";
        }
        return '';
    }

    /**
     * Get cached active company sizes.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('company_sizes.active', now()->addHours(12), function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Get cached featured company sizes.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('company_sizes.featured', now()->addHours(6), function () {
            return static::featured()->active()->ordered()->get();
        });
    }

    /**
     * Get cached popular company sizes.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("company_sizes.popular.{$limit}", now()->addHours(6), function () use ($limit) {
            return static::popular($limit)->active()->get();
        });
    }

    /**
     * Get cached company sizes by category.
     */
    public static function getCachedByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("company_sizes.category.{$category}", now()->addHours(12), function () use ($category) {
            return static::byCategory($category)->active()->ordered()->get();
        });
    }

    /**
     * Clear all related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'company_sizes.active',
            'company_sizes.featured',
        ];

        // Clear popular cache variants
        for ($i = 5; $i <= 20; $i += 5) {
            $cacheKeys[] = "company_sizes.popular.{$i}";
        }

        // Clear category cache variants
        $categories = [
            self::CATEGORY_STARTUP,
            self::CATEGORY_SMALL,
            self::CATEGORY_MEDIUM,
            self::CATEGORY_LARGE,
            self::CATEGORY_ENTERPRISE,
        ];
        foreach ($categories as $category) {
            $cacheKeys[] = "company_sizes.category.{$category}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });

    }
}
