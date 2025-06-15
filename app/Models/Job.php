<?php

namespace App\Models;

use App\Traits\HasTaxonomy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Job.
 *
 * @property int                 $id
 * @property string              $job_title
 * @property string              $description
 * @property string              $country
 * @property string              $state
 * @property string              $city
 * @property string              $salary_from
 * @property string              $salary_to
 * @property int                 $currency_id
 * @property int                 $salary_period_id
 * @property int                 $job_type_id
 * @property int                 $career_level_id
 * @property int                 $functional_area_id
 * @property int                 $job_shift_id
 * @property int                 $degree_level_id
 * @property int                 $position_id
 * @property string              $job_expiry_date
 * @property int                 $no_preference
 * @property int                 $hide_salary
 * @property int                 $is_freelance
 * @property int                 $is_suspended
 * @property null|Carbon         $created_at
 * @property null|Carbon         $updated_at
 * @property CareerLevel         $careerLevel
 * @property SalaryCurrency      $currency
 * @property RequiredDegreeLevel $degreeLevel
 * @property FunctionalArea      $functionalArea
 * @property JobShift            $jobShift
 * @property JobType             $jobType
 * @property Position            $position
 * @property SalaryPeriod        $salaryPeriod
 */
class Job extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use HasTaxonomy;

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
            ->setDescriptionForEvent(fn (string $eventName) => "Job has been {$eventName}")
        ;
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orWhere('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include inactive jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', '!=', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include published jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now())
        ;
    }

    /**
     * Scope a query to only include jobs that haven't expired.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now())
                ->orWhere('job_expiry_date', '>=', now()->format('Y-m-d'))
            ;
        });
    }

    /**
     * Scope a query to only include expired jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->orWhere('job_expiry_date', '<', now()->format('Y-m-d'))
        ;
    }

    /**
     * Scope a query to only include remote jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    /**
     * Scope a query to only include freelance jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFreelance($query)
    {
        return $query->where('is_freelance', true);
    }

    /**
     * Scope a query to only include non-freelance jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotFreelance($query)
    {
        return $query->where('is_freelance', false);
    }

    /**
     * Scope a query to only include jobs in a specific location.
     *
     * @param Builder $query
     * @param string  $location
     *
     * @return Builder
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', '%'.$location.'%')
            ->orWhere('country', 'like', '%'.$location.'%')
            ->orWhere('state', 'like', '%'.$location.'%')
            ->orWhere('city', 'like', '%'.$location.'%')
        ;
    }

    /**
     * Scope a query to only include jobs within a salary range.
     *
     * @param Builder $query
     * @param float   $min
     * @param float   $max
     *
     * @return Builder
     */
    public function scopeSalaryRange($query, $min, $max)
    {
        return $query->where('salary_min', '<=', $max)
            ->where('salary_max', '>=', $min)
            ->orWhere('salary_from', '<=', $max)
            ->where('salary_to', '>=', $min)
        ;
    }

    /**
     * Scope a query to only include jobs of a specific type.
     *
     * @param Builder $query
     * @param int     $jobTypeId
     *
     * @return Builder
     */
    public function scopeOfType($query, $jobTypeId)
    {
        return $query->where('job_type_id', $jobTypeId);
    }

    /**
     * Scope a query to only include jobs in a specific industry.
     *
     * @param Builder $query
     * @param int     $industryId
     *
     * @return Builder
     */
    public function scopeInIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId)
            ->orWhere('functional_area_id', $industryId)
        ;
    }

    /**
     * Scope a query to only include jobs from a specific company.
     *
     * @param Builder $query
     * @param int     $companyId
     *
     * @return Builder
     */
    public function scopeFromCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to search jobs by title or description.
     *
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->orWhere('job_title', 'like', '%'.$search.'%')
        ;
    }

    /**
     * Scope a query to order jobs by creation date.
     *
     * @param Builder $query
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope a query to order jobs by publication date.
     *
     * @param Builder $query
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeOrderByPublished($query, $direction = 'desc')
    {
        return $query->orderBy('published_at', $direction);
    }

    /**
     * Scope a query to order jobs by salary range.
     *
     * @param Builder $query
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeOrderBySalary($query, $direction = 'desc')
    {
        return $query->orderBy('salary_max', $direction)
            ->orOrderBy('salary_to', $direction)
        ;
    }

    /**
     * Scope a query to only include jobs published within a date range.
     *
     * @param Builder        $query
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return Builder
     */
    public function scopePublishedBetween($query, $start, $end)
    {
        return $query->whereBetween('published_at', [$start, $end]);
    }

    /**
     * Scope a query to only include featured jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFeatured($query)
    {
        return $query->whereHas('activeFeatured', function ($q) {
            $q->where('record_type', 'job');
        });
    }

    /**
     * Scope a query to only include recent jobs.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Relationships

    /**
     * Get the company that owns the job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the job type.
     */
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    /**
     * Get the job category.
     */
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the career level.
     */
    public function careerLevel(): BelongsTo
    {
        return $this->belongsTo(CareerLevel::class);
    }

    /**
     * Get the functional area.
     */
    public function functionalArea(): BelongsTo
    {
        return $this->belongsTo(FunctionalArea::class);
    }

    /**
     * Get the job shift.
     */
    public function jobShift(): BelongsTo
    {
        return $this->belongsTo(JobShift::class);
    }

    /**
     * Get the degree level.
     */
    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(RequiredDegreeLevel::class);
    }

    /**
     * Get the currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(SalaryCurrency::class);
    }

    /**
     * Get the salary period.
     */
    public function salaryPeriod(): BelongsTo
    {
        return $this->belongsTo(SalaryPeriod::class);
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the applied jobs for this job.
     */
    public function appliedJobs(): HasMany
    {
        return $this->hasMany(AppliedJob::class);
    }

    /**
     * Get the job skills.
     */
    public function jobsSkill(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'jobs_skill', 'job_id', 'skill_id');
    }

    /**
     * Get the job tags.
     */
    public function jobsTag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'jobs_tags', 'job_id', 'tag_id');
    }

    /**
     * Get the featured records.
     */
    public function featured(): MorphOne
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
            ->where('is_active', true)
        ;
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
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'salary_from' => 'decimal:2',
            'salary_to' => 'decimal:2',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'no_preference' => 'boolean',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean',
            'is_remote' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'job_expiry_date' => 'date',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Additional scopes and methods can be added here as needed for the job portal project
}
