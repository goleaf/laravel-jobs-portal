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
 *
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @method static Builder|Company whereCeo($value)
 * @method static Builder|Company whereCompanySizeId($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereDetails($value)
 * @method static Builder|Company whereEstablishedIn($value)
 * @method static Builder|Company whereFacebookUrl($value)
 * @method static Builder|Company whereFax($value)
 * @method static Builder|Company whereGooglePlusUrl($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereIndustryId($value)
 * @method static Builder|Company whereIsFeatured($value)
 * @method static Builder|Company whereIsActive($value)
 * @method static Builder|Company whereLinkedinUrl($value)
 * @method static Builder|Company whereLocation($value)
 * @method static Builder|Company whereLocation2($value)
 * @method static Builder|Company whereNoOfOffices($value)
 * @method static Builder|Company whereOwnershipTypeId($value)
 * @method static Builder|Company wherePinterestUrl($value)
 * @method static Builder|Company whereTwitterUrl($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereWebsite($value)
 * @method static Builder|Company whereUniqueId($value)
 * @method static Builder|Company whereUserId($value)
 * @method static Builder|Company active()
 * @method static Builder|Company inactive()
 * @method static Builder|Company featured()
 * @method static Builder|Company byIndustry(int $industryId)
 * @method static Builder|Company bySize(int $sizeId)
 * @method static Builder|Company establishedBetween(int $startYear, int $endYear)
 * @method static Builder|Company withWebsite()
 * @method static Builder|Company byLocation(?int $countryId = null, ?int $stateId = null, ?int $cityId = null)
 * @method static Builder|Company withJobs()
 * @method static Builder|Company withActiveJobs()
 * @method static Builder|Company search(string $term)
 * @method static Builder|Company recent(int $days = 30)
 * @method static Builder|Company popular(int $minJobs = 5)
 * @method static Builder|Company verified()
 * @method static Builder|Company withSocialMedia()
 * @method static Builder|Company multiOffice()
 * @method static Builder|Company alphabetical()
 *
 * @mixin Eloquent
 */
class Company extends Model implements HasMedia
{
    use HasFactory, LogsActivity, SoftDeletes, InteractsWithMedia, HasSlug;
    
    public $table = 'companies';

    /**
     * Default eager loading for performance
     */
    protected $with = ['industry', 'companySize', 'user'];

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
        'values', 'company_culture', 'diversity_policy'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'founded_year' => 'integer',
            'employee_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'is_private' => 'boolean',
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
        ];
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'ceo' => 'required|max:180',
        'industry_id' => 'required|integer|exists:industries,id',
        'ownership_type_id' => 'required|integer|exists:ownership_types,id',
        'company_size_id' => 'required|integer|exists:company_sizes,id',
        'established_in' => 'required|integer|min:1800|max:2030',
        'website' => 'nullable|url',
        'location' => 'required|string|max:255',
        'no_of_offices' => 'required|numeric|min:1|max:1000',
        'details' => 'nullable|string|max:2000',
        'facebook_url' => 'nullable|url',
        'twitter_url' => 'nullable|url',
        'linkedin_url' => 'nullable|url',
        'google_plus_url' => 'nullable|url',
        'pinterest_url' => 'nullable|url',
    ];

    /**
     * @var array
     */
    protected $appends = ['company_url', 'country_name', 'state_name', 'city_name'];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Generate unique ID
        static::creating(function ($company) {
            if (!$company->unique_id) {
                $company->unique_id = 'COMP-' . strtoupper(uniqid());
            }
        });

        // Clear cache when company is updated
        static::updated(function ($company) {
            cache()->forget("company.{$company->id}");
            cache()->forget("companies.featured");
            cache()->forget("companies.active");
            cache()->tags(['companies', 'company-' . $company->id])->flush();
        });

        // Clear cache when company is deleted
        static::deleted(function ($company) {
            cache()->forget("company.{$company->id}");
            cache()->forget("companies.featured");
            cache()->forget("companies.active");
            cache()->tags(['companies', 'company-' . $company->id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getCountryNameAttribute()
    {
        return cache()->remember("company.{$this->id}.country_name", 3600, function () {
            return $this->user?->country?->name;
        });
    }

    public function getStateNameAttribute()
    {
        return cache()->remember("company.{$this->id}.state_name", 3600, function () {
            return $this->user?->state?->name;
        });
    }

    public function getCityNameAttribute()
    {
        return cache()->remember("company.{$this->id}.city_name", 3600, function () {
            return $this->user?->city?->name;
        });
    }

    public function uploadLogo(UploadedFile $file): void
    {
        $fileService = app(FileService::class);
        $fileService->deleteFile($this->logo_path);
        
        $this->logo_path = $fileService->uploadFile($file, 'companies');
        $this->save();
    }

    public function getCompanyUrlAttribute()
    {
        return cache()->remember("company.{$this->id}.company_url", 3600, function () {
            if ($this->logo_path) {
                return asset('storage/' . $this->logo_path);
            }
            return asset('assets/img/default-company-logo.png');
        });
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class)->withDefault();
    }

    public function ownerShipType(): BelongsTo
    {
        return $this->belongsTo(OwnerShipType::class, 'ownership_type_id')->withDefault();
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class)->withDefault();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    public function featured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner');
    }

    public function activeFeatured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner')
                    ->where('end_date', '>=', now())
                    ->where('start_date', '<=', now());
    }

    /**
     * Scope for active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive companies.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified companies.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified companies.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for featured companies.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured companies.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for companies by industry.
     */
    public function scopeByIndustry($query, int $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope for companies by ownership type.
     */
    public function scopeByOwnershipType($query, int $ownershipTypeId)
    {
        return $query->where('ownership_type_id', $ownershipTypeId);
    }

    /**
     * Scope for companies by size.
     */
    public function scopeBySize($query, int $companySizeId)
    {
        return $query->where('company_size_id', $companySizeId);
    }

    /**
     * Scope for companies by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for companies by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for companies by city.
     */
    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope for searching companies.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('website', 'like', "%{$term}%");
    }

    /**
     * Scope for recent companies.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old companies.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for companies with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for companies without jobs.
     */
    public function scopeWithoutJobs($query)
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope for companies with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 1);
        });
    }

    /**
     * Scope for companies established in year range.
     */
    public function scopeEstablishedBetween($query, int $startYear, int $endYear)
    {
        return $query->whereBetween('established_in', [$startYear, $endYear]);
    }

    /**
     * Scope for startup companies (established recently).
     */
    public function scopeStartup($query, int $years = 5)
    {
        $cutoffYear = now()->year - $years;
        return $query->where('established_in', '>=', $cutoffYear);
    }

    /**
     * Scope for established companies.
     */
    public function scopeEstablished($query, int $years = 10)
    {
        $cutoffYear = now()->year - $years;
        return $query->where('established_in', '<=', $cutoffYear);
    }

    /**
     * Scope for small companies.
     */
    public function scopeSmall($query)
    {
        return $query->whereHas('companySize', function ($q) {
            $q->where('size', '<=', 50);
        });
    }

    /**
     * Scope for medium companies.
     */
    public function scopeMedium($query)
    {
        return $query->whereHas('companySize', function ($q) {
            $q->whereBetween('size', [51, 500]);
        });
    }

    /**
     * Scope for large companies.
     */
    public function scopeLarge($query)
    {
        return $query->whereHas('companySize', function ($q) {
            $q->where('size', '>', 500);
        });
    }

    /**
     * Scope for companies with website.
     */
    public function scopeWithWebsite($query)
    {
        return $query->whereNotNull('website')
                    ->where('website', '!=', '');
    }

    /**
     * Scope for companies with social media.
     */
    public function scopeWithSocialMedia($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('facebook_url')
              ->orWhereNotNull('twitter_url')
              ->orWhereNotNull('linkedin_url')
              ->orWhereNotNull('google_plus_url')
              ->orWhereNotNull('pinterest_url');
        });
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for popular companies (with most jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('jobs')
                    ->orderBy('jobs_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for technology companies.
     */
    public function scopeTechnology($query)
    {
        return $query->whereHas('industry', function ($q) {
            $q->where('name', 'like', '%technology%')
              ->orWhere('name', 'like', '%IT%')
              ->orWhere('name', 'like', '%software%');
        });
    }

    /**
     * Scope for companies with multiple offices.
     */
    public function scopeMultiOffice($query)
    {
        return $query->where('no_of_offices', '>', 1);
    }

    /**
     * Scope for companies by office count range.
     */
    public function scopeByOfficeRange($query, int $min, int $max)
    {
        return $query->whereBetween('no_of_offices', [$min, $max]);
    }

    /**
     * Get company statistics.
     */
    public function getStatsAttribute(): array
    {
        return cache()->remember("company.{$this->id}.stats", 3600, function () {
            return [
                'total_jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->active()->count(),
                'applications_count' => $this->jobs()->withCount('appliedJobs')->get()->sum('applied_jobs_count'),
                'company_age' => now()->year - $this->established_in,
            ];
        });
    }

    // Slug configuration
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return $this->getFirstMediaUrl('cover') ?: null;
    }

    public function getCompanyAgeAttribute()
    {
        return $this->founded_year ? now()->year - $this->founded_year : null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    public function getActiveJobsCountAttribute()
    {
        return $this->activeJobs()->count();
    }

    public function getSizeCategoryAttribute()
    {
        if ($this->employee_count <= 10) return __('company.size.startup');
        if ($this->employee_count <= 50) return __('company.size.small');
        if ($this->employee_count <= 500) return __('company.size.medium');
        return __('company.size.large');
    }

    public function getEstablishmentCategoryAttribute()
    {
        $age = $this->company_age;
        if (!$age) return __('company.establishment.unknown');
        if ($age <= 5) return __('company.establishment.startup');
        if ($age <= 10) return __('company.establishment.growing');
        if ($age <= 25) return __('company.establishment.established');
        return __('company.establishment.legacy');
    }

    public function getLocationStringAttribute()
    {
        $parts = array_filter([
            $this->city?->name,
            $this->state?->name,
            $this->country?->name
        ]);
        return implode(', ', $parts);
    }

    public function getSocialLinksAttribute()
    {
        return array_filter([
            'facebook' => $this->social_facebook,
            'twitter' => $this->social_twitter,
            'linkedin' => $this->social_linkedin,
            'instagram' => $this->social_instagram,
            'youtube' => $this->social_youtube,
            'github' => $this->social_github,
        ]);
    }

    // Business Logic Methods
    public function isActivelyHiring()
    {
        return $this->activeJobs()->count() > 0;
    }

    public function hasRecentActivity($days = 30)
    {
        return $this->jobs()
            ->where('created_at', '>=', now()->subDays($days))
            ->exists();
    }

    public function canPostJobs()
    {
        return $this->is_active && $this->is_verified;
    }

    public function getTotalApplications()
    {
        return $this->jobs()
            ->withCount('applications')
            ->get()
            ->sum('applications_count');
    }

    public function getHiringStats()
    {
        return [
            'total_jobs' => $this->jobs()->count(),
            'active_jobs' => $this->activeJobs()->count(),
            'total_applications' => $this->getTotalApplications(),
            'avg_applications_per_job' => $this->jobs()->count() > 0 
                ? round($this->getTotalApplications() / $this->jobs()->count(), 2) 
                : 0,
        ];
    }

    public function calculateEngagementScore()
    {
        $followers = $this->followers()->count();
        $jobs = $this->jobs()->count();
        $reviews = $this->reviews()->count();
        $avgRating = $this->average_rating;
        
        return min(100, ($followers * 0.3) + ($jobs * 0.4) + ($reviews * 0.2) + ($avgRating * 10 * 0.1));
    }
}
