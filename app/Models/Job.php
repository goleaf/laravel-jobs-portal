<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Job
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CareerLevel $careerLevel
 * @property-read SalaryCurrency $currency
 * @property-read RequiredDegreeLevel $degreeLevel
 * @property-read FunctionalArea $functionalArea
 * @property-read JobShift $jobShift
 * @property-read JobType $jobType
 * @property-read Position $position
 * @property-read SalaryPeriod $salaryPeriod
 *
 * @method static Builder|Job newModelQuery()
 * @method static Builder|Job newQuery()
 * @method static Builder|Job query()
 * @method static Builder|Job whereCareerLevelId($value)
 * @method static Builder|Job whereCity($value)
 * @method static Builder|Job whereCountry($value)
 * @method static Builder|Job whereCreatedAt($value)
 * @method static Builder|Job whereCurrencyId($value)
 * @method static Builder|Job whereDegreeLevelId($value)
 * @method static Builder|Job whereDescription($value)
 * @method static Builder|Job whereFunctionalAreaId($value)
 * @method static Builder|Job whereHideSalary($value)
 * @method static Builder|Job whereId($value)
 * @method static Builder|Job whereIsFreelance($value)
 * @method static Builder|Job whereIsFeatured($value)
 * @method static Builder|Job whereIsSuspended($value)
 * @method static Builder|Job whereJobExpiryDate($value)
 * @method static Builder|Job whereJobShiftId($value)
 * @method static Builder|Job whereJobTitle($value)
 * @method static Builder|Job whereJobTypeId($value)
 * @method static Builder|Job whereNoPreference($value)
 * @method static Builder|Job wherePositionId($value)
 * @method static Builder|Job whereSalaryFrom($value)
 * @method static Builder|Job whereSalaryPeriodId($value)
 * @method static Builder|Job whereSalaryTo($value)
 * @method static Builder|Job whereState($value)
 * @method static Builder|Job whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property int $company_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobApplication[] $appliedJobs
 * @property-read int|null $applied_jobs_count
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobSkill[] $jobsSkill
 * @property-read int|null $jobs_skill_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereCompanyId($value)
 *
 * @property string $job_id
 * @property int $job_category_id
 * @property-read \App\Models\JobCategory $jobCategory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereJobCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job status($status)
 *
 * @property int $status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereStatus($value)
 *
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $city_id
 * @property-read mixed $city_name
 * @property-read mixed $country_name
 * @property-read mixed $state_name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Job whereStateId($value)
 *
 * @property int|null $experience
 * @property-read \App\Models\FeaturedRecord|null $activeFeatured
 * @property-read \App\Models\FeaturedRecord|null $featured
 * @property-read string $full_location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $jobsTag
 * @property-read int|null $jobs_tag_count
 *
 * @method static Builder|Job whereExperience($value)
 * @method static Builder|Job wherePosition($value)
 *
 * @property int $is_created_by_admin
 *
 * @method static Builder|Job whereIsCreatedByAdmin($value)
 */
class Job extends Model
{
    const NO_PREFERENCE = [
        2 => 'Both',
        1 => 'Male',
        0 => 'Female',
    ];

    const GENDER = [
        0 => 'Male',
        1 => 'Female',
    ];

    const IS_SUSPENDED = [
        self::SELECT_IS_SUSPENDED => 'Is Suspended',
        self::YES => 'Yes',
        self::NO => 'No',
    ];

    const IS_FEATURED = [
        self::SELECT_FEATURD => 'Select Featured job',
        self::YES => 'Yes',
        self::NO => 'No',
    ];
    const SELECT_FEATURD = 2;
    const SELECT_IS_SUSPENDED = 2;
    const SELECT_IS_FREELANCER = 2;
    const SELECT_JOBS_ACTIVE = 2;
    const YES = 1;
    const NO = 0;
    const ACTIVE = 0;
    const EXPIRE = 1;
    const STATUS_DRAFT = 0;

    const STATUS_OPEN = 1;

    const STATUS_CLOSED = 2;

    const STATUS_PAUSED = 3;
    const SELECT_STATUS = 4;

    const STATUS_SUSPENDED = 4;

    const NOT_SUSPENDED = 0;

    const STATUS = [
        self::SELECT_STATUS => 'Select Status',
        self::STATUS_DRAFT => 'Drafted',
        self::STATUS_OPEN => 'Live',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_PAUSED => 'Paused',
    ];

    const STATUS_ARRAY = [
        0 => 'Drafted',
        1 => 'Live',
        2 => 'Closed',
        3 => 'Paused',
    ];

    const FAVORITE_JOB_STATUS = [
        1 => 'Live',
        2 => 'Closed',
        3 => 'Paused',
    ];

    const STATUS_COLOR = [
        0 => 'warning',
        1 => 'success',
        2 => 'danger',
        3 => 'primary',
    ];

    const IS_FREELANCER = [
        self::SELECT_IS_FREELANCER => 'Is Freelance',
        self::YES => 'Yes',
        self::NO => 'No',
    ];

    const JOBS_ACTIVE = [
        self::SELECT_JOBS_ACTIVE => 'Select Job Status',
        self::ACTIVE => 'Active',
        self::EXPIRE => 'Expire',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'company_id' => 'sometimes|required',
        'job_title' => 'required|max:180',
        'currency_id' => 'required',
        'salary_period_id' => 'required',
        'job_type_id' => 'required',
        'functional_area_id' => 'required',
        'position' => 'required|min:0|max:255',
        'experience' => 'required|min:0|max:255',
        'country_id' => 'required',
        'job_category_id' => 'required',
        'state_id' => 'required',
        'city_id' => 'required',
        'salary_from' => 'required|min:0|max:999999999',
        'salary_to' => 'required|min:0|max:999999999',
        'job_expiry_date' => 'required',
    ];

    protected $table = 'jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_title',
        'description',
        'job_id',
        'company_id',
        'job_category_id',
        'country_id',
        'state_id', 
        'city_id',
        'salary_from',
        'salary_to',
        'currency_id',
        'salary_period_id',
        'job_type_id',
        'career_level_id',
        'functional_area_id',
        'job_shift_id',
        'degree_level_id',
        'experience',
        'job_expiry_date',
        'no_preference',
        'hide_salary',
        'is_freelance',
        'is_suspended',
        'status',
        'is_created_by_admin',
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
            'company_id' => 'integer',
            'job_category_id' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'currency_id' => 'integer',
            'salary_period_id' => 'integer',
            'job_type_id' => 'integer',
            'career_level_id' => 'integer',
            'functional_area_id' => 'integer',
            'job_shift_id' => 'integer',
            'degree_level_id' => 'integer',
            'experience' => 'integer',
            'job_expiry_date' => 'date',
            'no_preference' => 'integer',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean',
            'status' => 'integer',
            'is_created_by_admin' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Generate unique job ID
        static::creating(function ($job) {
            if (!$job->job_id) {
                $job->job_id = 'JOB-' . strtoupper(uniqid());
            }
        });

        // Clear cache when job is updated
        static::updated(function ($job) {
            cache()->forget("job.{$job->id}");
            cache()->forget("job.featured");
            cache()->forget("jobs.active");
        });

        // Clear cache when job is deleted
        static::deleted(function ($job) {
            cache()->forget("job.{$job->id}");
            cache()->forget("job.featured");
            cache()->forget("jobs.active");
        });
    }

    /**
     * Get the job's country with caching.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    /**
     * Get the job's state with caching.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    /**
     * Get the job's city with caching.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    /**
     * Get the admin who created this job.
     */
    public function admin(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'company_id')
                    ->where('is_created_by_admin', true);
    }

    /**
     * Get cached country name.
     */
    public function getCountryNameAttribute(): ?string
    {
        return cache()->remember("job.{$this->id}.country_name", 3600, function () {
            return $this->country?->name;
        });
    }

    /**
     * Get cached state name.
     */
    public function getStateNameAttribute(): ?string
    {
        return cache()->remember("job.{$this->id}.state_name", 3600, function () {
            return $this->state?->name;
        });
    }

    /**
     * Get cached city name.
     */
    public function getCityNameAttribute(): ?string
    {
        return cache()->remember("job.{$this->id}.city_name", 3600, function () {
            return $this->city?->name;
        });
    }

    /**
     * Get the job's company with optimized loading.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withDefault();
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for active jobs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN)
                    ->where('is_suspended', false)
                    ->where('job_expiry_date', '>=', now());
    }

    /**
     * Scope for featured jobs.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->whereHas('activeFeatured');
    }

    /**
     * Scope for jobs by location.
     */
    public function scopeByLocation(Builder $query, ?int $countryId = null, ?int $stateId = null, ?int $cityId = null): Builder
    {
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        if ($cityId) {
            $query->where('city_id', $cityId);
        }
        return $query;
    }

    /**
     * Scope for jobs by salary range.
     */
    public function scopeBySalaryRange(Builder $query, ?float $minSalary = null, ?float $maxSalary = null): Builder
    {
        if ($minSalary) {
            $query->where('salary_from', '>=', $minSalary);
        }
        if ($maxSalary) {
            $query->where('salary_to', '<=', $maxSalary);
        }
        return $query;
    }

    /**
     * Get the job's currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(SalaryCurrency::class, 'currency_id')->withDefault();
    }

    /**
     * Get the job's salary period.
     */
    public function salaryPeriod(): BelongsTo
    {
        return $this->belongsTo(SalaryPeriod::class)->withDefault();
    }

    /**
     * Get the job's type.
     */
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class)->withDefault();
    }

    /**
     * Get the job's career level.
     */
    public function careerLevel(): BelongsTo
    {
        return $this->belongsTo(CareerLevel::class)->withDefault();
    }

    /**
     * Get the job's functional area.
     */
    public function functionalArea(): BelongsTo
    {
        return $this->belongsTo(FunctionalArea::class)->withDefault();
    }

    /**
     * Get the job's shift.
     */
    public function jobShift(): BelongsTo
    {
        return $this->belongsTo(JobShift::class)->withDefault();
    }

    /**
     * Get the job's degree level requirement.
     */
    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(RequiredDegreeLevel::class, 'degree_level_id')->withDefault();
    }

    /**
     * Get the job's skills with efficient loading.
     */
    public function jobsSkill(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id');
    }

    /**
     * Get the job's tags with efficient loading.
     */
    public function jobsTag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'job_tags', 'job_id', 'tag_id');
    }

    /**
     * Get job applications with optimized loading.
     */
    public function appliedJobs(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the job's category.
     */
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class)->withDefault();
    }

    /**
     * Get the full location string.
     */
    public function getFullLocationAttribute(): string
    {
        return cache()->remember("job.{$this->id}.full_location", 3600, function () {
            $location = [];
            
            if ($this->city_name) {
                $location[] = $this->city_name;
            }
            if ($this->state_name) {
                $location[] = $this->state_name;
            }
            if ($this->country_name) {
                $location[] = $this->country_name;
            }
            
            return implode(', ', $location) ?: 'Remote';
        });
    }

    /**
     * Get the job's featured record.
     */
    public function featured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner');
    }

    /**
     * Get the job's active featured record.
     */
    public function activeFeatured(): MorphOne
    {
        return $this->morphOne(FeaturedRecord::class, 'owner')
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
    }

    /**
     * Check if job is expired.
     */
    public function isExpired(): bool
    {
        return $this->job_expiry_date < now();
    }

    /**
     * Check if job is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_OPEN && 
               !$this->is_suspended && 
               !$this->isExpired();
    }

    /**
     * Check if job is featured.
     */
    public function isFeatured(): bool
    {
        return $this->activeFeatured()->exists();
    }

    /**
     * Get formatted salary range.
     */
    public function getFormattedSalaryAttribute(): string
    {
        if ($this->hide_salary) {
            return 'Salary not disclosed';
        }

        $currency = $this->currency?->currency_symbol ?? '$';
        $period = $this->salaryPeriod?->period ?? 'month';
        
        if ($this->salary_from && $this->salary_to) {
            return "{$currency}{$this->salary_from} - {$currency}{$this->salary_to} per {$period}";
        } elseif ($this->salary_from) {
            return "From {$currency}{$this->salary_from} per {$period}";
        } elseif ($this->salary_to) {
            return "Up to {$currency}{$this->salary_to} per {$period}";
        }

        return 'Salary negotiable';
    }

    /**
     * Get job status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'badge-warning',
            self::STATUS_OPEN => 'badge-success',
            self::STATUS_CLOSED => 'badge-danger',
            self::STATUS_PAUSED => 'badge-info',
            default => 'badge-secondary'
        };
    }

    /**
     * Get job status text.
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUS_ARRAY[$this->status] ?? 'Unknown';
    }
}
