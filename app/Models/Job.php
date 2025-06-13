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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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
 */
class Job extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public $table = 'jobs';

    /**
     * Job status constants
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PAUSED = 'paused';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_title', 'title', 'description', 'requirements', 'benefits',
        'company_id', 'user_id', 'job_type_id', 'career_level_id', 
        'functional_area_id', 'job_shift_id', 'degree_level_id', 'position_id',
        'currency_id', 'salary_period_id', 'country_id', 'state_id', 'city_id',
        'salary_from', 'salary_to', 'salary_min', 'salary_max',
        'job_expiry_date', 'expires_at', 'published_at',
        'country', 'state', 'city', 'location', 'address',
        'no_preference', 'hide_salary', 'is_freelance', 'is_suspended',
        'is_remote', 'is_featured', 'is_active', 'status'
    ];

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

    /**
     * Activity log configuration for spatie/laravel-activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['job_title', 'title', 'status', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Job has been {$eventName}");
    }

    /**
     * Scope a query to only include old records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("created_at", "asc");
    }




    /**
     * Scope a query to only include active jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orWhere('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include inactive jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', '!=', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include published jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include jobs that haven't expired.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now())
              ->orWhere('job_expiry_date', '>=', now()->format('Y-m-d'));
        });
    }

    /**
     * Scope a query to only include expired jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<', now())
                     ->orWhere('job_expiry_date', '<', now()->format('Y-m-d'));
    }

    /**
     * Scope a query to only include remote jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    /**
     * Scope a query to only include freelance jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreelance($query)
    {
        return $query->where('is_freelance', true);
    }

    /**
     * Scope a query to only include non-freelance jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotFreelance($query)
    {
        return $query->where('is_freelance', false);
    }

    /**
     * Scope a query to only include jobs in a specific location.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%')
                     ->orWhere('country', 'like', '%' . $location . '%')
                     ->orWhere('state', 'like', '%' . $location . '%')
                     ->orWhere('city', 'like', '%' . $location . '%');
    }

    /**
     * Scope a query to only include jobs within a salary range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $jobTypeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $jobTypeId)
    {
        return $query->where('job_type_id', $jobTypeId);
    }

    /**
     * Scope a query to only include jobs in a specific industry.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $industryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId)
                     ->orWhere('functional_area_id', $industryId);
    }

    /**
     * Scope a query to only include jobs from a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to search jobs by title or description.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%')
                     ->orWhere('description', 'like', '%' . $search . '%')
                     ->orWhere('job_title', 'like', '%' . $search . '%');
    }

    /**
     * Scope a query to order jobs by creation date.
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
     * Scope a query to order jobs by publication date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByPublished($query, $direction = 'desc')
    {
        return $query->orderBy('published_at', $direction);
    }

    /**
     * Scope a query to order jobs by salary range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderBySalary($query, $direction = 'desc')
    {
        return $query->orderBy('salary_max', $direction)
                     ->orOrderBy('salary_to', $direction);
    }

    /**
     * Scope a query to only include jobs published within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedBetween($query, $start, $end)
    {
        return $query->whereBetween('published_at', [$start, $end]);
    }

    /**
     * Scope a query to only include featured jobs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Additional scopes and methods can be added here as needed for the job portal project
} 