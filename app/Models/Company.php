<?php

namespace App\Models;

use App\Traits\HasTaxonomy;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Company.
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
 * @property null|string $details
 * @property string $website
 * @property string $unique_id
 * @property string $location
 * @property string $location2
 * @property null|string $fax
 * @property null|string $facebook_url
 * @property null|string $twitter_url
 * @property null|string $linkedin_url
 * @property null|string $google_plus_url
 * @property null|string $pinterest_url
 * @property bool $is_active
 * @property bool $is_featured
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property CompanySize $companySize
 * @property Industry $industry
 * @property OwnerShipType $ownerShipType
 * @property null|User $user
 * @property mixed $company_url
 * @property Collection|Job[] $jobs
 * @property null|int $jobs_count
 * @property null|FeaturedRecord $activeFeatured
 * @property null|FeaturedRecord $featured
 * @property mixed $city_name
 * @property mixed $country_name
 * @property mixed $state_name
 */
class Company extends Model implements HasMedia
{
    use HasFactory;
    use HasSettingsField;
    use HasSlug;
    use HasTaxonomy;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    public const COMPANY_LOGIN_TYPE = 0;
    public const ISACTIVE = 1;
    public const DEACTIVE = 0;
    public const ALL = 2;

    public const BTN_BTN_COLOR = [
        'btn btn-green btn-small-effect',
        'btn btn-purple btn-small btn-effect',
        'btn btn-blue btn-small btn-effect',
        'btn btn-orange btn-small btn-effect',
        'btn btn-red btn-small btn-effect',
        'btn btn-blue-grey btn-small btn-effect',
        'btn btn-green btn-small btn-effect',
    ];

    public const IS_FEATURED = [
        self::ALL => 'Select Featured Company',
        self::ISACTIVE => 'Yes',
        self::DEACTIVE => 'No',
    ];

    public const STATUS = [
        self::ALL => 'ALL',
        self::ISACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public $table = 'companies';

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
        'ceo', 'no_of_offices', 'no_of_employees', 'established_in', 'details', 'fax',
        'facebook_url', 'twitter_url', 'linkedin_url', 'google_plus_url',
        'pinterest_url', 'unique_id', 'location', 'location2', 'founded_at', 'status',
    ];

    /**
     * Default eager loading for performance.
     */
    protected $with = [];

    protected $dates = ['deleted_at', 'founded_at'];

    /**
     * Scope a query to only include verified companies.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include unverified companies.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope a query to only include active companies.
     *
     * @param  Builder  $query
     * @return Builder
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
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include companies in a specific industry.
     *
     * @param  Builder  $query
     * @param  int  $industryId
     * @return Builder
     */
    public function scopeInIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope a query to only include companies of a specific size.
     *
     * @param  Builder  $query
     * @param  int  $sizeId
     * @return Builder
     */
    public function scopeOfSize($query, $sizeId)
    {
        return $query->where('size_id', $sizeId)->orWhere('company_size_id', $sizeId);
    }

    /**
     * Scope a query to only include companies in a specific location.
     *
     * @param  Builder  $query
     * @param  string  $location
     * @return Builder
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', '%'.$location.'%')
            ->orWhere('location2', 'like', '%'.$location.'%')
            ->orWhere('address', 'like', '%'.$location.'%');
    }

    /**
     * Scope a query to search companies by name or description.
     *
     * @param  Builder  $query
     * @param  string  $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->orWhere('short_description', 'like', '%'.$search.'%')
            ->orWhere('details', 'like', '%'.$search.'%');
    }

    /**
     * Scope a query to order companies by creation date.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope a query to order companies by founding date.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByFounded($query, $direction = 'desc')
    {
        return $query->orderBy('founded_at', $direction)->orOrderBy('founded_year', $direction);
    }

    /**
     * Scope a query to only include companies founded within a date range.
     *
     * @param  Builder  $query
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return Builder
     */
    public function scopeFoundedBetween($query, $start, $end)
    {
        return $query->whereBetween('founded_at', [$start, $end]);
    }

    /**
     * Scope a query to only include companies with a specific status.
     *
     * @param  Builder  $query
     * @param  string  $status
     * @return Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include featured companies.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include recent companies.
     *
     * @param  Builder  $query
     * @return Builder
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
     * @return null|string
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
     * Activity log configuration for spatie/laravel-activitylog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'website', 'is_active', 'is_featured', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Company has been {$eventName}");
    }

    /**
     * Slug configuration for spatie/laravel-sluggable.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
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
    public function featuredRecord(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner');
    }

    /**
     * Get the active featured records.
     */
    public function activeFeatured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
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

    /**
     * Scope a query to filter by industry.
     *
     * @param  Builder  $query
     * @param  int  $industryId
     * @return Builder
     */
    public function scopeByIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope a query to filter by location.
     *
     * @param  Builder  $query
     * @param  string  $location
     * @return Builder
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where(function ($q) use ($location) {
            $q->where('city_id', $location)
                ->orWhere('state_id', $location)
                ->orWhere('country_id', $location)
                ->orWhere('address', 'like', "%{$location}%");
        });
    }

    /**
     * Scope a query to filter by company size.
     *
     * @param  Builder  $query
     * @param  int  $sizeId
     * @return Builder
     */
    public function scopeBySize($query, $sizeId)
    {
        return $query->where('company_size_id', $sizeId);
    }

    /**
     * Scope a query to filter by establishment year range.
     *
     * @param  Builder  $query
     * @param  int  $startYear
     * @param  int  $endYear
     * @return Builder
     */
    public function scopeEstablishedBetween($query, $startYear, $endYear)
    {
        return $query->whereBetween('established_in', [$startYear, $endYear]);
    }

    /**
     * Scope a query to include only companies with jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeWithJobs($query)
    {
        return $query->whereHas('jobs');
    }

    /**
     * Get the full location string.
     *
     * @return null|string
     */
    public function getFullLocation()
    {
        $parts = [];

        if ($this->city) {
            $parts[] = $this->city->name;
        }
        if ($this->state) {
            $parts[] = $this->state->name;
        }
        if ($this->country) {
            $parts[] = $this->country->name;
        }

        return empty($parts) ? null : implode(', ', $parts);
    }

    /**
     * Get the total jobs count.
     *
     * @return int
     */
    public function getJobsCount()
    {
        return $this->jobs()->count();
    }

    /**
     * Get the active jobs count.
     *
     * @return int
     */
    public function getActiveJobsCount()
    {
        return $this->jobs()->where(function ($query) {
            $query->where('is_active', true)
                ->orWhere('status', 'active')
                ->orWhere('status', 1); // Job::STATUS_OPEN
        })->count();
    }

    /**
     * Check if company has social links.
     *
     * @return bool
     */
    public function hasSocialLinks()
    {
        return ! empty($this->social_facebook)
               || ! empty($this->social_twitter)
               || ! empty($this->social_linkedin)
               || ! empty($this->social_instagram)
               || ! empty($this->facebook_url)
               || ! empty($this->twitter_url)
               || ! empty($this->linkedin_url);
    }

    /**
     * Get the company age in years.
     *
     * @return null|int
     */
    public function getCompanyAge()
    {
        if (! $this->established_in && ! $this->founded_year) {
            return null;
        }

        $establishedYear = $this->established_in ?: $this->founded_year;

        return now()->year - $establishedYear;
    }

    /**
     * Get employee range description.
     *
     * @return string
     */
    public function getEmployeeRangeDescription()
    {
        if ($this->companySize && $this->companySize->size) {
            return $this->companySize->size;
        }

        if ($this->employee_count || $this->no_of_employees) {
            $count = $this->employee_count ?: $this->no_of_employees;

            return $count.' employees';
        }

        return 'Size not specified';
    }

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
     * ELOQUENT HAS MANY DEEP INTEGRATION
     *
     * Package: staudenmeir/eloquent-has-many-deep v1.21
     * Source: https://github.com/staudenmeir/eloquent-has-many-deep
     * Reference: https://madewithlaravel.com/eloquent-has-many-deep
     *
     * Deep relationships for advanced company analytics and querying.
     */

    /**
     * Get all candidates who applied to company jobs.
     *
     * Path: Company -> Jobs -> JobApplications -> Users (Candidates)
     *
     * This replaces multiple queries with a single optimized relationship.
     *
     * Usage: $company->jobApplicants()->with('candidate')->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function jobApplicants()
    {
        return $this->hasManyDeep(
            \App\Models\User::class,
            [\App\Models\Job::class, \App\Models\JobApplication::class],
            [
                'company_id',  // Company.id = Job.company_id
                'job_id',      // Job.id = JobApplication.job_id
                'candidate_id', // JobApplication.candidate_id = User.id
            ],
            [
                'id',          // Company.id
                'id',          // Job.id
                'id',           // User.id
            ]
        )->distinct();
    }

    /**
     * Get all skills required across company jobs.
     *
     * Path: Company -> Jobs -> JobSkills -> Skills
     *
     * Shows all skills the company is hiring for.
     *
     * Usage: $company->requiredSkills()->orderBy('name')->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function requiredSkills()
    {
        return $this->hasManyDeep(
            \App\Models\Skill::class,
            [\App\Models\Job::class, 'jobs_skill'],
            [
                'company_id', // Company.id = Job.company_id
                'job_id',     // Job.id = jobs_skill.job_id
                'skill_id',    // jobs_skill.skill_id = Skill.id
            ],
            [
                'id',         // Company.id
                'id',         // Job.id
                'id',          // Skill.id
            ]
        )->distinct();
    }

    /**
     * Get companies in same industry and location.
     *
     * Path: Company -> Industry -> Companies (in same city)
     *
     * Find local competitors in the same industry.
     *
     * Usage: $company->localCompetitors()->active()->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function localCompetitors()
    {
        return $this->hasManyDeep(
            \App\Models\Company::class,
            [\App\Models\Industry::class],
            [
                'industry_id', // Company.industry_id = Industry.id
                'id',          // Industry.id = Company.industry_id
            ],
            [
                'id',         // Company.id
                'industry_id', // Industry.industry_id
            ]
        )->where('companies.city_id', $this->city_id)
            ->where('companies.id', '!=', $this->id);
    }

    /**
     * Get all regions where company has received applications.
     *
     * Path: Company -> Jobs -> JobApplications -> Users -> Cities
     *
     * Shows geographic reach of company's talent attraction.
     *
     * Usage: $company->applicantRegions()->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function applicantRegions()
    {
        return $this->hasManyDeep(
            \App\Models\City::class,
            [\App\Models\Job::class, \App\Models\JobApplication::class, \App\Models\User::class],
            [
                'company_id',  // Company.id = Job.company_id
                'job_id',      // Job.id = JobApplication.job_id
                'candidate_id', // JobApplication.candidate_id = User.id
                'city_id',      // User.city_id = City.id
            ],
            [
                'id',          // Company.id
                'id',          // Job.id
                'id',          // User.id
                'id',           // City.id
            ]
        )->distinct();
    }

    /**
     * Default settings for Company model.
     *
     * These settings provide company-specific configuration options
     * for branding, recruitment preferences, and operational settings.
     *
     * @var array
     */
    public $defaultSettings = [
        'branding' => [
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'logo_style' => 'standard',
            'company_tagline' => '',
            'brand_voice' => 'professional',
        ],
        'recruitment' => [
            'auto_publish_jobs' => false,
            'application_deadline_days' => 30,
            'require_cover_letter' => false,
            'allow_remote_applications' => true,
            'screening_questions_enabled' => false,
            'interview_scheduling_enabled' => true,
            'candidate_rating_system' => true,
        ],
        'notifications' => [
            'new_applications' => true,
            'application_status_updates' => true,
            'job_expiry_reminders' => true,
            'weekly_analytics' => false,
            'candidate_recommendations' => true,
        ],
        'privacy' => [
            'company_profile_public' => true,
            'show_employee_count' => true,
            'show_salary_ranges' => false,
            'allow_anonymous_reviews' => false,
            'data_retention_months' => 24,
        ],
        'features' => [
            'job_analytics' => true,
            'candidate_search' => true,
            'bulk_messaging' => false,
            'custom_application_forms' => false,
            'integration_ats' => false,
        ],
    ];

    /**
     * Validation rules for company settings data.
     *
     * @var array
     */
    public $settingsRules = [
        'branding.primary_color' => 'string|regex:/^#[0-9A-Fa-f]{6}$/',
        'branding.secondary_color' => 'string|regex:/^#[0-9A-Fa-f]{6}$/',
        'branding.logo_style' => 'string|in:standard,minimal,modern,classic',
        'branding.brand_voice' => 'string|in:professional,casual,friendly,corporate',

        'recruitment.auto_publish_jobs' => 'boolean',
        'recruitment.application_deadline_days' => 'integer|min:1|max:365',
        'recruitment.require_cover_letter' => 'boolean',
        'recruitment.allow_remote_applications' => 'boolean',
        'recruitment.screening_questions_enabled' => 'boolean',
        'recruitment.interview_scheduling_enabled' => 'boolean',
        'recruitment.candidate_rating_system' => 'boolean',

        'notifications.new_applications' => 'boolean',
        'notifications.application_status_updates' => 'boolean',
        'notifications.job_expiry_reminders' => 'boolean',
        'notifications.weekly_analytics' => 'boolean',
        'notifications.candidate_recommendations' => 'boolean',

        'privacy.company_profile_public' => 'boolean',
        'privacy.show_employee_count' => 'boolean',
        'privacy.show_salary_ranges' => 'boolean',
        'privacy.allow_anonymous_reviews' => 'boolean',
        'privacy.data_retention_months' => 'integer|min:1|max:120',

        'features.job_analytics' => 'boolean',
        'features.candidate_search' => 'boolean',
        'features.bulk_messaging' => 'boolean',
        'features.custom_application_forms' => 'boolean',
        'features.integration_ats' => 'boolean',
    ];

    // Additional scopes and methods can be added here as needed for the job portal project
}
