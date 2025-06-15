<?php

namespace App\Models;

use App\Services\FileService;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasTaxonomy;

/**
 * Class Company
 *
 * @version June 22, 2020, 12:34 pm UTC
 *
 * @property int $id
 * @property string $ceo
 * @property int $no_of_offices
 * @property int $industry_id
 * @property int $ownership_type_id
 * @property int $company_size_id
 * @property int $established_in
 * @property string|null $details
 * @property string $website
 * @property string $unique_id
 * @property string $location
 * @property string $location2
 * @property string|null $fax
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $linkedin_url
 * @property string|null $google_plus_url
 * @property string|null $pinterest_url
 * @property bool $is_active
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CompanySize $companySize
 * @property-read Industry $industry
 * @property-read OwnerShipType $ownerShipType
 * @property-read User|null $user
 * @property-read mixed $company_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read \App\Models\FeaturedRecord|null $activeFeatured
 * @property-read \App\Models\FeaturedRecord|null $featured
 * @property-read mixed $city_name
 * @property-read mixed $country_name
 * @property-read mixed $state_name
 */
class Company extends Model implements HasMedia
{
    use HasFactory, LogsActivity, SoftDeletes, InteractsWithMedia, HasSlug, HasTaxonomy;
    
    public $table = 'companies';

    /**
     * Default eager loading for performance
     */
    protected $with = [];

    public const COMPANY_LOGIN_TYPE = 0;
    public const ISACTIVE = 1;
    public const DEACTIVE = 0;
    public const ALL = 2;

    const BTN_BTN_COLOR = [
        'btn btn-green btn-small-effect',
        'btn btn-purple btn-small btn-effect',
        'btn btn-blue btn-small btn-effect',
        'btn btn-orange btn-small btn-effect',
        'btn btn-red btn-small btn-effect',
        'btn btn-blue-grey btn-small btn-effect',
        'btn btn-green btn-small btn-effect',
    ];

    const IS_FEATURED = [
        self::ALL => 'Select Featured Company',
        self::ISACTIVE => 'Yes',
        self::DEACTIVE => 'No',
    ];

    const STATUS = [
        self::ALL => 'ALL',
        self::ISACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public $fillable = [
        'user_id', 'name', 'slug', 'email', 'phone', 'website',
        'description', 'short_description', 'founded_year', 'employee_count',
        'industry_id', 'company_size_id', 'ownership_type_id',
        'country_id', 'state_id', 'city_id', 'address', 'postal_code',
        'latitude', 'longitude', 'is_active', 'is_featured', 'is_verified',
        'is_private', 'logo', 'cover_image', 'social_facebook',
        'social_twitter', 'social_linkedin', 'social_instagram',
        'social_youtube', 'social_github', 'culture_description',
        'benefits', 'technologies', 'certifications', 'awards',
        'office_locations', 'working_hours', 'dress_code',
        'company_type', 'revenue', 'market_cap', 'stock_symbol',
        'headquarters', 'ceo_name', 'mission_statement', 'vision_statement',
        'values', 'company_culture', 'diversity_policy',
        'ceo', 'no_of_offices', 'established_in', 'details', 'fax',
        'facebook_url', 'twitter_url', 'linkedin_url', 'google_plus_url',
        'pinterest_url', 'unique_id', 'location', 'location2', 'size_id', 'founded_at', 'status'
    ];

    protected $dates = ['deleted_at', 'founded_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'industry_id' => 'integer',
            'ownership_type_id' => 'integer',
            'company_size_id' => 'integer',
            'established_in' => 'integer',
            'no_of_employees' => 'integer',
            'founded_year' => 'integer',
            'employee_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'is_private' => 'boolean',
            'verified_at' => 'datetime',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'benefits' => 'array',
            'technologies' => 'array',
            'certifications' => 'array',
            'awards' => 'array',
            'office_locations' => 'array',
            'working_hours' => 'array',
            'values' => 'array',
            'revenue' => 'decimal:2',
            'market_cap' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'founded_at' => 'date',
        ];
    }

    /**
     * Scope a query to only include verified companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include unverified companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope a query to only include active companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('is_active', true)
                  ->orWhere('status', 'active');
        });
    }

    /**
     * Scope a query to only include inactive companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include companies in a specific industry.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $industryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope a query to only include companies of a specific size.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $sizeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSize($query, $sizeId)
    {
        return $query->where('size_id', $sizeId)->orWhere('company_size_id', $sizeId);
    }

    /**
     * Scope a query to only include companies in a specific location.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%')
                     ->orWhere('location2', 'like', '%' . $location . '%')
                     ->orWhere('address', 'like', '%' . $location . '%');
    }

    /**
     * Scope a query to search companies by name or description.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('description', 'like', '%' . $search . '%')
                     ->orWhere('short_description', 'like', '%' . $search . '%')
                     ->orWhere('details', 'like', '%' . $search . '%');
    }

    /**
     * Scope a query to order companies by creation date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope a query to order companies by founding date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByFounded($query, $direction = 'desc')
    {
        return $query->orderBy('founded_at', $direction)->orOrderBy('founded_year', $direction);
    }

    /**
     * Scope a query to only include companies founded within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFoundedBetween($query, $start, $end)
    {
        return $query->whereBetween('founded_at', [$start, $end]);
    }

    /**
     * Scope a query to only include companies with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include featured companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include recent companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Get the number of active job postings for the company.
     *
     * @return int
     */
    public function getActiveJobCountAttribute()
    {
        return $this->jobs()->active()->notExpired()->published()->count();
    }

    /**
     * Get the average salary range across all job postings.
     *
     * @return array
     */
    public function getAverageSalaryRangeAttribute()
    {
        $jobs = $this->jobs()->active()->notExpired()->published()->get();
        if ($jobs->isEmpty()) {
            return ['min' => 0, 'max' => 0];
        }
        $avgMin = $jobs->avg('salary_min');
        $avgMax = $jobs->avg('salary_max');
        return ['min' => round($avgMin, 2), 'max' => round($avgMax, 2)];
    }

    /**
     * Get the most common job type for the company.
     *
     * @return string|null
     */
    public function getMostCommonJobTypeAttribute()
    {
        $jobTypes = $this->jobs()->active()->notExpired()->published()
            ->select('job_type_id')
            ->groupBy('job_type_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->pluck('job_type_id');
        return $jobTypes->first();
    }

    /**
     * Activity log configuration for spatie/laravel-activitylog
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'website', 'is_active', 'is_featured', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Company has been {$eventName}");
    }

    /**
     * Slug configuration for spatie/laravel-sluggable
     */
    public function getSlugOptions(): \Spatie\Sluggable\SlugOptions
    {
        return \Spatie\Sluggable\SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(255);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships

    /**
     * Get the user that owns the company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the jobs for the company.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the featured records.
     */
    public function featured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'record');
    }

    /**
     * Get the active featured records.
     */
    public function activeFeatured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'record')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('is_active', true);
    }

    /**
     * Get the country that the company belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that the company belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that the company belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the industry that the company belongs to.
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * Get the company size that the company belongs to.
     */
    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class);
    }

    /**
     * Get the ownership type that the company belongs to.
     */
    public function ownershipType(): BelongsTo
    {
        return $this->belongsTo(OwnershipType::class);
    }

    // Additional scopes and methods can be added here as needed for the job portal project
} 