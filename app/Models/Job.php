<?php

namespace App\Models;

use App\Traits\HasTaxonomy;
use App\Traits\Universal\HasUniqueValues;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Job.
 *
 * @property int $id
 * @property string $job_title
 * @property string $description
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $salary_from
 * @property string $salary_to
 * @property int $currency_id
 * @property int $salary_period_id
 * @property int $job_type_id
 * @property int $career_level_id
 * @property int $functional_area_id
 * @property int $job_shift_id
 * @property int $degree_level_id
 * @property int $position_id
 * @property string $job_expiry_date
 * @property int $no_preference
 * @property int $hide_salary
 * @property int $is_freelance
 * @property int $is_suspended
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property CareerLevel $careerLevel
 * @property SalaryCurrency $currency
 * @property RequiredDegreeLevel $degreeLevel
 * @property FunctionalArea $functionalArea
 * @property JobShift $jobShift
 * @property JobType $jobType
 * @property Position $position
 * @property SalaryPeriod $salaryPeriod
 */
class Job extends Model
{
    use HasFactory;
    use HasSettingsField;
    use HasTaxonomy;
    use HasUniqueValues;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Default settings for job model.
     */
    public $defaultSettings = [
        'visibility' => [
            'public' => true,
            'searchable' => true,
            'featured' => false,
            'highlight' => false,
            'urgent' => false,
        ],
        'application' => [
            'auto_accept' => false,
            'require_cover_letter' => false,
            'require_portfolio' => false,
            'max_applications' => 100,
            'application_deadline_reminder' => true,
            'send_confirmation_email' => true,
        ],
        'notifications' => [
            'new_application' => true,
            'application_status_change' => true,
            'job_expiry_reminder' => true,
            'daily_digest' => false,
            'weekly_summary' => true,
        ],
        'display' => [
            'show_salary' => true,
            'show_company_logo' => true,
            'show_application_count' => false,
            'show_view_count' => false,
            'layout' => 'standard', // standard, compact, detailed
            'color_scheme' => 'default',
        ],
        'seo' => [
            'custom_meta_title' => '',
            'custom_meta_description' => '',
            'custom_keywords' => '',
            'canonical_url' => '',
            'robots_index' => true,
            'robots_follow' => true,
        ],
        'social' => [
            'share_enabled' => true,
            'auto_post_linkedin' => false,
            'auto_post_twitter' => false,
            'auto_post_facebook' => false,
            'custom_share_message' => '',
        ],
        'analytics' => [
            'track_views' => true,
            'track_applications' => true,
            'track_shares' => true,
            'google_analytics_enabled' => false,
            'custom_tracking_code' => '',
        ],
        'workflow' => [
            'auto_close_on_expiry' => true,
            'auto_extend_expiry' => false,
            'require_approval' => false,
            'auto_publish' => true,
            'screening_questions_enabled' => false,
        ],
        'premium' => [
            'boost_enabled' => false,
            'priority_listing' => false,
            'extended_visibility' => false,
            'premium_badge' => false,
            'featured_placement' => false,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'visibility.public' => 'boolean',
        'visibility.searchable' => 'boolean',
        'visibility.featured' => 'boolean',
        'visibility.highlight' => 'boolean',
        'visibility.urgent' => 'boolean',

        'application.auto_accept' => 'boolean',
        'application.require_cover_letter' => 'boolean',
        'application.require_portfolio' => 'boolean',
        'application.max_applications' => 'integer|min:1|max:1000',
        'application.application_deadline_reminder' => 'boolean',
        'application.send_confirmation_email' => 'boolean',

        'notifications.new_application' => 'boolean',
        'notifications.application_status_change' => 'boolean',
        'notifications.job_expiry_reminder' => 'boolean',
        'notifications.daily_digest' => 'boolean',
        'notifications.weekly_summary' => 'boolean',

        'display.show_salary' => 'boolean',
        'display.show_company_logo' => 'boolean',
        'display.show_application_count' => 'boolean',
        'display.show_view_count' => 'boolean',
        'display.layout' => 'string|in:standard,compact,detailed',
        'display.color_scheme' => 'string|in:default,blue,green,red,purple',

        'seo.custom_meta_title' => 'string|max:60',
        'seo.custom_meta_description' => 'string|max:160',
        'seo.custom_keywords' => 'string|max:255',
        'seo.canonical_url' => 'url|nullable',
        'seo.robots_index' => 'boolean',
        'seo.robots_follow' => 'boolean',

        'social.share_enabled' => 'boolean',
        'social.auto_post_linkedin' => 'boolean',
        'social.auto_post_twitter' => 'boolean',
        'social.auto_post_facebook' => 'boolean',
        'social.custom_share_message' => 'string|max:280',

        'analytics.track_views' => 'boolean',
        'analytics.track_applications' => 'boolean',
        'analytics.track_shares' => 'boolean',
        'analytics.google_analytics_enabled' => 'boolean',
        'analytics.custom_tracking_code' => 'string|max:500',

        'workflow.auto_close_on_expiry' => 'boolean',
        'workflow.auto_extend_expiry' => 'boolean',
        'workflow.require_approval' => 'boolean',
        'workflow.auto_publish' => 'boolean',
        'workflow.screening_questions_enabled' => 'boolean',

        'premium.boost_enabled' => 'boolean',
        'premium.priority_listing' => 'boolean',
        'premium.extended_visibility' => 'boolean',
        'premium.premium_badge' => 'boolean',
        'premium.featured_placement' => 'boolean',
    ];

    /**
     * Job status constants (numeric).
     */
    public const STATUS_DRAFT = 0;
    public const STATUS_OPEN = 1;
    public const STATUS_CLOSED = 2;
    public const STATUS_PAUSED = 3;
    public const STATUS_SUSPENDED = 4;

    /**
     * Job suspension constants.
     */
    public const NOT_SUSPENDED = 0;

    /**
     * Boolean constants.
     */
    public const YES = 1;
    public const NO = 0;
    public const SELECT_FEATURD = 2;
    public const SELECT_IS_SUSPENDED = 2;
    public const SELECT_IS_FREELANCER = 2;
    public const SELECT_JOBS_ACTIVE = 2;

    /**
     * No preference constants.
     */
    public const NO_PREFERENCE = [
        2 => 'Both',
        1 => 'Male',
        0 => 'Female',
    ];

    /**
     * Gender constants.
     */
    public const GENDER = [
        0 => 'Male',
        1 => 'Female',
    ];

    /**
     * Status array constants.
     */
    public const STATUS_ARRAY = [
        0 => 'Drafted',
        1 => 'Live',
        2 => 'Closed',
        3 => 'Paused',
    ];

    /**
     * Status color constants.
     */
    public const STATUS_COLOR = [
        0 => 'warning',
        1 => 'success',
        2 => 'danger',
        3 => 'primary',
    ];

    /**
     * Favorite job status constants.
     */
    public const FAVORITE_JOB_STATUS = [
        1 => 'Live',
        2 => 'Closed',
        3 => 'Paused',
    ];

    public $table = 'jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'job_title', 'title', 'description', 'requirements', 'benefits',
        'company_id', 'user_id', 'job_type_id', 'job_category_id', 'career_level_id',
        'functional_area_id', 'job_shift_id', 'degree_level_id', 'position_id',
        'currency_id', 'salary_period_id', 'country_id', 'state_id', 'city_id',
        'salary_from', 'salary_to', 'salary_min', 'salary_max',
        'job_expiry_date', 'expires_at', 'published_at',
        'country', 'state', 'city', 'location', 'address',
        'no_preference', 'hide_salary', 'is_freelance', 'is_suspended',
        'is_remote', 'is_featured', 'is_active', 'status', 'is_created_by_admin',
        'position', 'experience', 'last_change', 'key_responsibilities',
    ];

    /**
     * Activity log configuration for spatie/laravel-activitylog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['job_title', 'title', 'status', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Job has been {$eventName}");
    }

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include active jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orWhere('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include inactive jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', '!=', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include published jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include jobs that haven't expired.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            if (Schema::hasColumn($this->getTable(), 'expires_at')) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            }
            $q->orWhere('job_expiry_date', '>=', now()->format('Y-m-d'));
        });
    }

    /**
     * Scope a query to only include expired jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            if (Schema::hasColumn($this->getTable(), 'expires_at')) {
                $q->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
            }
            $q->orWhere('job_expiry_date', '<', now()->format('Y-m-d'));
        });
    }

    /**
     * Scope a query to only include remote jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    /**
     * Scope a query to only include freelance jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFreelance($query)
    {
        return $query->where('is_freelance', true);
    }

    /**
     * Scope a query to only include non-freelance jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotFreelance($query)
    {
        return $query->where('is_freelance', false);
    }

    /**
     * Scope a query to only include jobs in a specific location.
     *
     * @param  Builder  $query
     * @param  string  $location
     * @return Builder
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', '%'.$location.'%')
            ->orWhere('country', 'like', '%'.$location.'%')
            ->orWhere('state', 'like', '%'.$location.'%')
            ->orWhere('city', 'like', '%'.$location.'%');
    }

    /**
     * Backward-compatible wrapper: byLocation → prefer relational name match.
     * Falls back to text columns if present.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where(function ($q) use ($location) {
            $q->whereHas('city', function ($sub) use ($location) {
                $sub->where('name', 'like', '%'.$location.'%');
            })
            ->orWhereHas('state', function ($sub) use ($location) {
                $sub->where('name', 'like', '%'.$location.'%');
            })
            ->orWhereHas('country', function ($sub) use ($location) {
                $sub->where('name', 'like', '%'.$location.'%');
            })
            // Fallback: if text columns exist in the schema
            ->orWhere('location', 'like', '%'.$location.'%')
            ->orWhere('country', 'like', '%'.$location.'%')
            ->orWhere('state', 'like', '%'.$location.'%')
            ->orWhere('city', 'like', '%'.$location.'%');
        });
    }

    /**
     * Scope a query to only include jobs within a salary range.
     *
     * @param  Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return Builder
     */
    public function scopeSalaryRange($query, $min, $max)
    {
        return $query->where('salary_min', '<=', $max)
            ->where('salary_max', '>=', $min)
            ->orWhere('salary_from', '<=', $max)
            ->where('salary_to', '>=', $min);
    }

    /**
     * Scope a query to only include jobs of a specific type.
     *
     * @param  Builder  $query
     * @param  int  $jobTypeId
     * @return Builder
     */
    public function scopeOfType($query, $jobTypeId)
    {
        return $query->where('job_type_id', $jobTypeId);
    }

    /**
     * Filter by employment type string (e.g., full_time, part_time).
     */
    public function scopeByEmploymentType($query, string $employmentType)
    {
        return $query->where('employment_type', $employmentType);
    }

    /**
     * Scope a query to only include jobs in a specific industry.
     *
     * @param  Builder  $query
     * @param  int  $industryId
     * @return Builder
     */
    public function scopeInIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId)
            ->orWhere('functional_area_id', $industryId);
    }

    /**
     * Scope a query to only include jobs from a specific company.
     *
     * @param  Builder  $query
     * @param  int  $companyId
     * @return Builder
     */
    public function scopeFromCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Backward-compatible wrapper: byCompany → fromCompany.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $this->scopeFromCompany($query, $companyId);
    }

    /**
     * Scope a query to search jobs by title or description.
     *
     * @param  Builder  $query
     * @param  string  $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->orWhere('job_title', 'like', '%'.$search.'%');
    }

    /**
     * Scope a query to order jobs by creation date.
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
     * Scope a query to order jobs by publication date.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByPublished($query, $direction = 'desc')
    {
        return $query->orderBy('published_at', $direction);
    }

    /**
     * Scope a query to order jobs by salary range.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderBySalary($query, $direction = 'desc')
    {
        return $query->orderBy('salary_max', $direction)
            ->orOrderBy('salary_to', $direction);
    }

    /**
     * Scope a query to only include jobs published within a date range.
     *
     * @param  Builder  $query
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return Builder
     */
    public function scopePublishedBetween($query, $start, $end)
    {
        return $query->whereBetween('published_at', [$start, $end]);
    }

    /**
     * Scope a query to only include featured jobs.
     *
     * @return Builder
     */
    public function scopeFeatured($query)
    {
        return $query->whereHas('activeFeatured');
    }

    /**
     * Scope a query to only include recent jobs.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to filter jobs by category.
     *
     * @param  Builder  $query
     * @param  int  $categoryId
     * @return Builder
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('job_category_id', $categoryId);
    }

    /**
     * Scope a query to get popular jobs (jobs with many applications).
     *
     * @param  Builder  $query
     * @param  int  $minApplications
     * @return Builder
     */
    public function scopePopular($query, $minApplications = 10)
    {
        return $query->has('appliedJobs', '>=', $minApplications);
    }

    // Relationships

    /**
     * Get the company that owns the job.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the job type.
     */
    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    /**
     * Get the job category.
     */
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    /**
     * Get the career level.
     */
    public function careerLevel()
    {
        return $this->belongsTo(CareerLevel::class);
    }

    /**
     * Get the functional area.
     */
    public function functionalArea()
    {
        return $this->belongsTo(FunctionalArea::class);
    }

    /**
     * Get the job shift.
     */
    public function jobShift()
    {
        return $this->belongsTo(JobShift::class);
    }

    /**
     * Get the degree level.
     */
    public function degreeLevel()
    {
        return $this->belongsTo(DegreeLevel::class);
    }

    /**
     * Get the currency.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the salary period.
     */
    public function salaryPeriod()
    {
        return $this->belongsTo(SalaryPeriod::class);
    }

    /**
     * Get the country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the applied jobs for this job.
     */
    public function appliedJobs()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the job skills.
     */
    public function jobsSkill()
    {
        return $this->belongsToMany(Skill::class, 'jobs_skill');
    }

    /**
     * Get the job tags.
     */
    public function jobsTag()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'job_tag');
    }

    /**
     * Get the featured records.
     */
    public function featured()
    {
        return $this->hasOne(\App\Models\FeaturedJob::class);
    }

    /**
     * Get the active featured records.
     */
    public function activeFeatured()
    {
        return $this->hasOne(\App\Models\FeaturedJob::class)->where('is_active', true);
    }

    // ==============================================
    // ACCESSORS
    // ==============================================

    /**
     * Get the full location string for the job.
     */
    public function getFullLocationAttribute(): string
    {
        $location = [];

        // Add city, state, country if they exist
        if ($this->city?->name) {
            $location[] = $this->city->name;
        }
        if ($this->state?->name) {
            $location[] = $this->state->name;
        }
        if ($this->country?->name) {
            $location[] = $this->country->name;
        }

        return implode(', ', $location) ?: __('common.location_not_specified');
    }

    /**
     * Get a human-friendly formatted salary string.
     */
    public function getFormattedSalaryAttribute(): string
    {
        $salaryFrom = $this->salary_from ?? $this->salary_min;
        $salaryTo = $this->salary_to ?? $this->salary_max;

        if (is_numeric($salaryFrom) && is_numeric($salaryTo)) {
            return '$'.number_format((int) $salaryFrom). ' - $'. number_format((int) $salaryTo);
        }

        if (is_numeric($salaryFrom)) {
            return 'From $'.number_format((int) $salaryFrom);
        }

        return 'Competitive';
    }

    /**
     * Humanized time since job was posted.
     */
    public function getTimeSincePostedAttribute(): ?string
    {
        if ($this->created_at instanceof Carbon) {
            return $this->created_at->diffForHumans();
        }
        return null;
    }

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_remote' => 'boolean',
        'is_urgent' => 'boolean',
        'experience_from' => 'integer',
        'experience_to' => 'integer',
        'salary_from' => 'integer',
        'salary_to' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Configure unique value fields for automatic generation.
     */
    protected function getUniqueValueFields(): array
    {
        return [
            'job_reference' => [
                'type' => 'job-reference',
            ],
            'slug' => [
                'type' => 'slug',
                'source_field' => 'job_title',
                'scope' => 'job-slug',
            ],
        ];
    }

    // Additional scopes and methods can be added here as needed for the job portal project
}
