<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * App\Models\JobApplication
 *
 * @property int $id
 * @property int $job_id
 * @property int $candidate_id
 * @property int $resume_id
 * @property float $expected_salary
 * @property string|null $notes
 * @property int $status
 * @property int|null $job_stage_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|JobApplication newModelQuery()
 * @method static Builder|JobApplication newQuery()
 * @method static Builder|JobApplication query()
 * @method static Builder|JobApplication whereCandidateId($value)
 * @method static Builder|JobApplication whereCreatedAt($value)
 * @method static Builder|JobApplication whereExpectedSalary($value)
 * @method static Builder|JobApplication whereId($value)
 * @method static Builder|JobApplication whereJobId($value)
 * @method static Builder|JobApplication whereNotes($value)
 * @method static Builder|JobApplication whereResumeId($value)
 * @method static Builder|JobApplication whereUpdatedAt($value)
 * @method static Builder|JobApplication whereStatus($value)
 * @method static Builder|JobApplication whereJobStageId($value)
 * @method static Builder|JobApplication byStatus(int $status)
 * @method static Builder|JobApplication pending()
 * @method static Builder|JobApplication hired()
 * @method static Builder|JobApplication rejected()
 * @method static Builder|JobApplication shortlisted()
 * @method static Builder|JobApplication recent(int $days = 30)
 * @method static Builder|JobApplication byJob(int $jobId)
 * @method static Builder|JobApplication byCandidate(int $candidateId)
 * @method static Builder|JobApplication bySalaryRange(?float $minSalary = null, ?float $maxSalary = null)
 * @method static Builder|JobApplication withNotes()
 * @method static Builder|JobApplication byCompany(int $companyId)
 * @method static Builder|JobApplication today()
 * @method static Builder|JobApplication thisWeek()
 * @method static Builder|JobApplication thisMonth()
 * @method static Builder|JobApplication withSchedules()
 * @method static Builder|JobApplication needsReview()
 * @method static Builder|JobApplication highSalaryExpectation(float $threshold = 100000)
 * @method static Builder|JobApplication popular()
 *
 * @mixin Eloquent
 *
 * @property-read \App\Models\Candidate $candidate
 * @property-read \App\Models\Job $job
 * @property-read mixed $resume_url
 * @property-read JobStage|null $jobStage
 * @property-read mixed $status_text
 * @property-read mixed $status_color
 * @property-read mixed $formatted_salary
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobApplicationSchedule[] $applicationSchedule
 * @property-read int|null $application_schedule_count
 */
class JobApplication extends Model
{
    use HasFactory, LogsActivity;
    
    public $table = 'job_applications';

    /**
     * Default eager loading for performance
     */
    protected $with = ['candidate', 'job', 'jobStage'];

    protected $appends = ['resume_url', 'status_text', 'status_color', 'formatted_salary'];

    const STATUS_DRAFT = 0;
    const STATUS_APPLIED = 1;
    const REJECTED = 2;
    const COMPLETE = 3;
    const SHORT_LIST = 4;
    const SELECT_STATUS = 5;

    const FILTER = [
        self::SELECT_STATUS => 'Select Status',
        self::COMPLETE => 'Hired',
        self::SHORT_LIST => 'Ongoing',
    ];

    const STATUS = [
        0 => 'Drafted',
        1 => 'Applied',
        2 => 'Declined',
        3 => 'Hired',
        4 => 'Ongoing',
    ];

    const STATUS_COLOR = [
        0 => 'warning',
        1 => 'primary',
        2 => 'danger',
        3 => 'info',
        4 => 'success',
    ];

    public $fillable = [
        'job_id',
        'candidate_id',
        'resume_id',
        'expected_salary',
        'notes',
        'status',
        'job_stage_id',
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
            'job_id' => 'integer',
            'candidate_id' => 'integer',
            'resume_id' => 'integer',
            'status' => 'string',
            'expected_salary' => 'decimal:2',
            'cover_letter' => 'string',
            'notes' => 'string',
            'applied_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'interview_scheduled_at' => 'datetime',
            'rejected_at' => 'datetime',
            'hired_at' => 'datetime',
            'is_shortlisted' => 'boolean',
            'is_rejected' => 'boolean',
            'is_hired' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'job_id' => 'required|integer|exists:jobs,id',
        'candidate_id' => 'required|integer|exists:candidates,id',
        'resume_id' => 'required|integer|exists:media,id',
        'expected_salary' => 'required|numeric|min:0|max:9999999999',
        'notes' => 'nullable|string|max:2000',
        'status' => 'integer|in:0,1,2,3,4',
        'job_stage_id' => 'nullable|integer|exists:job_stages,id',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when application is updated
        static::updated(function ($application) {
            cache()->forget("job_application.{$application->id}");
            cache()->forget("job.{$application->job_id}.applications_count");
            cache()->forget("candidate.{$application->candidate_id}.applications_count");
            cache()->tags(['job_applications', 'application-' . $application->id])->flush();
        });

        // Clear cache when application is deleted
        static::deleted(function ($application) {
            cache()->forget("job_application.{$application->id}");
            cache()->forget("job.{$application->job_id}.applications_count");
            cache()->forget("candidate.{$application->candidate_id}.applications_count");
            cache()->tags(['job_applications', 'application-' . $application->id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'expected_salary', 'job_stage_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id')->withDefault();
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id')->withDefault();
    }

    public function jobStage(): BelongsTo
    {
        return $this->belongsTo(JobStage::class, 'job_stage_id')->withDefault();
    }

    public function applicationSchedule(): HasMany
    {
        return $this->hasMany(JobApplicationSchedule::class, 'job_application_id');
    }

    /**
     * Get cached resume URL.
     */
    public function getResumeUrlAttribute()
    {
        return cache()->remember("job_application.{$this->id}.resume_url", 3600, function () {
            $media = Media::find($this->resume_id);
            return $media?->getFullUrl();
        });
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status] ?? 'Unknown';
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLOR[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted salary with currency.
     */
    public function getFormattedSalaryAttribute(): string
    {
        if (!$this->expected_salary) {
            return 'Not specified';
        }

        return number_format($this->expected_salary, 2) . ' ' . ($this->job->currency->code ?? 'USD');
    }

    /**
     * Scope for applications by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                    ->orWhere('status', 'applied');
    }

    /**
     * Scope for reviewed applications.
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed')
                    ->orWhereNotNull('reviewed_at');
    }

    /**
     * Scope for shortlisted applications.
     */
    public function scopeShortlisted($query)
    {
        return $query->where('is_shortlisted', true)
                    ->orWhere('status', 'shortlisted');
    }

    /**
     * Scope for rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_rejected', true)
                    ->orWhere('status', 'rejected');
    }

    /**
     * Scope for hired applications.
     */
    public function scopeHired($query)
    {
        return $query->where('is_hired', true)
                    ->orWhere('status', 'hired');
    }

    /**
     * Scope for applications with interview scheduled.
     */
    public function scopeInterviewScheduled($query)
    {
        return $query->whereNotNull('interview_scheduled_at')
                    ->orWhere('status', 'interview_scheduled');
    }

    /**
     * Scope for applications by job.
     */
    public function scopeByJob($query, int $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope for applications by candidate.
     */
    public function scopeByCandidate($query, int $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope for applications by company.
     */
    public function scopeByCompany($query, int $companyId)
    {
        return $query->whereHas('job', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    /**
     * Scope for recent applications.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('applied_at', '>=', now()->subDays($days))
                    ->orWhere('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old applications.
     */
    public function scopeOld($query, int $days = 30)
    {
        return $query->where('applied_at', '<', now()->subDays($days))
                    ->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for applications by salary range.
     */
    public function scopeBySalaryRange($query, float $min, float $max)
    {
        return $query->whereBetween('expected_salary', [$min, $max]);
    }

    /**
     * Scope for high salary expectations.
     */
    public function scopeHighSalaryExpectation($query, float $threshold = 100000)
    {
        return $query->where('expected_salary', '>=', $threshold);
    }

    /**
     * Scope for applications with cover letter.
     */
    public function scopeWithCoverLetter($query)
    {
        return $query->whereNotNull('cover_letter')
                    ->where('cover_letter', '!=', '');
    }

    /**
     * Scope for applications without cover letter.
     */
    public function scopeWithoutCoverLetter($query)
    {
        return $query->whereNull('cover_letter')
                    ->orWhere('cover_letter', '');
    }

    /**
     * Scope for applications with notes.
     */
    public function scopeWithNotes($query)
    {
        return $query->whereNotNull('notes')
                    ->where('notes', '!=', '');
    }

    /**
     * Scope for applications requiring review.
     */
    public function scopeRequiringReview($query)
    {
        return $query->where('status', 'pending')
                    ->orWhere('status', 'applied')
                    ->whereNull('reviewed_at');
    }

    /**
     * Scope for applications today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('applied_at', today())
                    ->orWhereDate('created_at', today());
    }

    /**
     * Scope for applications this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('applied_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->orWhereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for applications this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('applied_at', now()->month)
                    ->whereYear('applied_at', now()->year)
                    ->orWhere(function ($q) {
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    });
    }

    /**
     * Scope for applications with upcoming interviews.
     */
    public function scopeUpcomingInterviews($query, int $days = 7)
    {
        return $query->whereNotNull('interview_scheduled_at')
                    ->where('interview_scheduled_at', '>=', now())
                    ->where('interview_scheduled_at', '<=', now()->addDays($days));
    }

    /**
     * Scope for overdue interviews.
     */
    public function scopeOverdueInterviews($query)
    {
        return $query->whereNotNull('interview_scheduled_at')
                    ->where('interview_scheduled_at', '<', now())
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'rejected')
                    ->where('status', '!=', 'hired');
    }

    /**
     * Scope for applications by job category.
     */
    public function scopeByJobCategory($query, int $categoryId)
    {
        return $query->whereHas('job', function ($q) use ($categoryId) {
            $q->where('job_category_id', $categoryId);
        });
    }

    /**
     * Scope for applications by job type.
     */
    public function scopeByJobType($query, int $typeId)
    {
        return $query->whereHas('job', function ($q) use ($typeId) {
            $q->where('job_type_id', $typeId);
        });
    }

    /**
     * Scope for applications to active jobs.
     */
    public function scopeToActiveJobs($query)
    {
        return $query->whereHas('job', function ($q) {
            $q->where('status', 1);
        });
    }

    /**
     * Scope for applications to featured jobs.
     */
    public function scopeToFeaturedJobs($query)
    {
        return $query->whereHas('job', function ($q) {
            $q->where('is_featured', true);
        });
    }

    /**
     * Scope for applications from verified candidates.
     */
    public function scopeFromVerifiedCandidates($query)
    {
        return $query->whereHas('candidate', function ($q) {
            $q->where('is_verified', true);
        });
    }

    /**
     * Scope for applications from active candidates.
     */
    public function scopeFromActiveCandidates($query)
    {
        return $query->whereHas('candidate', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching applications.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('cover_letter', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%")
                    ->orWhereHas('candidate', function ($q) use ($term) {
                        $q->where('first_name', 'like', "%{$term}%")
                          ->orWhere('last_name', 'like', "%{$term}%")
                          ->orWhere('email', 'like', "%{$term}%");
                    })
                    ->orWhereHas('job', function ($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%");
                    });
    }

    /**
     * Check if application is still active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_APPLIED, self::SHORT_LIST]);
    }

    /**
     * Check if application is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, [self::REJECTED, self::COMPLETE]);
    }

    /**
     * Get application age in days.
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }
}
