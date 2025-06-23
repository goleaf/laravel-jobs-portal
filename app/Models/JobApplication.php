<?php

namespace App\Models;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * JobApplication Model with Laravel Model Settings Integration
 *
 * @property int $id
 * @property int $job_id
 * @property int $candidate_id
 * @property string $status
 * @property string $cover_letter
 * @property string $resume_path
 * @property array $screening_answers
 * @property Carbon $applied_at
 * @property Carbon|null $reviewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Job $job
 * @property Candidate $candidate
 */
class JobApplication extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFactory> */
    use HasFactory;
    use LogsActivity;
    use HasSettingsField;

    /**
     * Application status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW_SCHEDULED = 'interview_scheduled';
    public const STATUS_INTERVIEW_COMPLETED = 'interview_completed';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_HIRED = 'hired';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'candidate_id',
        'resume_id',
        'status',
        'cover_letter',
        'resume_path',
        'screening_answers',
        'applied_at',
        'reviewed_at',
        'expected_salary',
        'available_start_date',
        'notes',
        'rating',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'job_id' => 'integer',
        'candidate_id' => 'integer',
        'screening_answers' => 'array',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'available_start_date' => 'date',
        'expected_salary' => 'decimal:2',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default settings for job application model.
     */
    public $defaultSettings = [
        'workflow' => [
            'auto_acknowledge' => true,
            'auto_status_updates' => true,
            'require_cover_letter' => false,
            'require_portfolio' => false,
            'enable_screening_questions' => false,
            'send_status_notifications' => true,
        ],
        'notifications' => [
            'candidate_notifications' => [
                'application_received' => true,
                'status_change' => true,
                'interview_scheduled' => true,
                'offer_extended' => true,
                'rejection_notice' => true,
            ],
            'employer_notifications' => [
                'new_application' => true,
                'application_withdrawn' => true,
                'candidate_response' => true,
            ],
        ],
        'privacy' => [
            'share_with_employer' => [
                'contact_details' => true,
                'resume' => true,
                'cover_letter' => true,
                'expected_salary' => false,
                'availability' => true,
            ],
            'anonymize_after_rejection' => false,
            'data_retention_days' => 365,
        ],
        'tracking' => [
            'track_application_source' => true,
            'track_time_to_hire' => true,
            'track_interview_feedback' => true,
            'enable_analytics' => true,
        ],
        'features' => [
            'enable_video_interviews' => false,
            'enable_skill_assessments' => false,
            'enable_reference_checks' => false,
            'enable_background_checks' => false,
            'custom_application_form' => false,
        ],
        'automation' => [
            'auto_screen_applications' => false,
            'auto_reject_unqualified' => false,
            'auto_schedule_interviews' => false,
            'send_automated_responses' => true,
            'qualification_scoring' => false,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'workflow.auto_acknowledge' => 'boolean',
        'workflow.auto_status_updates' => 'boolean',
        'workflow.require_cover_letter' => 'boolean',
        'workflow.require_portfolio' => 'boolean',
        'workflow.enable_screening_questions' => 'boolean',
        'workflow.send_status_notifications' => 'boolean',
        
        'notifications.candidate_notifications.application_received' => 'boolean',
        'notifications.candidate_notifications.status_change' => 'boolean',
        'notifications.candidate_notifications.interview_scheduled' => 'boolean',
        'notifications.candidate_notifications.offer_extended' => 'boolean',
        'notifications.candidate_notifications.rejection_notice' => 'boolean',
        
        'notifications.employer_notifications.new_application' => 'boolean',
        'notifications.employer_notifications.application_withdrawn' => 'boolean',
        'notifications.employer_notifications.candidate_response' => 'boolean',
        
        'privacy.share_with_employer.contact_details' => 'boolean',
        'privacy.share_with_employer.resume' => 'boolean',
        'privacy.share_with_employer.cover_letter' => 'boolean',
        'privacy.share_with_employer.expected_salary' => 'boolean',
        'privacy.share_with_employer.availability' => 'boolean',
        'privacy.anonymize_after_rejection' => 'boolean',
        'privacy.data_retention_days' => 'integer|min:30|max:3650',
        
        'tracking.track_application_source' => 'boolean',
        'tracking.track_time_to_hire' => 'boolean',
        'tracking.track_interview_feedback' => 'boolean',
        'tracking.enable_analytics' => 'boolean',
        
        'features.enable_video_interviews' => 'boolean',
        'features.enable_skill_assessments' => 'boolean',
        'features.enable_reference_checks' => 'boolean',
        'features.enable_background_checks' => 'boolean',
        'features.custom_application_form' => 'boolean',
        
        'automation.auto_screen_applications' => 'boolean',
        'automation.auto_reject_unqualified' => 'boolean',
        'automation.auto_schedule_interviews' => 'boolean',
        'automation.send_automated_responses' => 'boolean',
        'automation.qualification_scoring' => 'boolean',
    ];

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'job_id', 'candidate_id', 'rating'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Scopes
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REVIEWED);
    }

    public function scopeShortlisted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SHORTLISTED);
    }

    public function scopeHired(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_HIRED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('applied_at', '>=', now()->subDays($days));
    }

    public function scopeByJob(Builder $query, int $jobId): Builder
    {
        return $query->where('job_id', $jobId);
    }

    public function scopeByCandidate(Builder $query, int $candidateId): Builder
    {
        return $query->where('candidate_id', $candidateId);
    }
}
