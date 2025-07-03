<?php

namespace App\Dtos;

use LumoSolutions\Actionable\Attributes\ArrayOf;
use LumoSolutions\Actionable\Attributes\DateFormat;
use LumoSolutions\Actionable\Attributes\FieldName;
use LumoSolutions\Actionable\Attributes\Ignore;
use LumoSolutions\Actionable\Concerns\ArrayConvertible;

class JobApplicationData
{
    use ArrayConvertible;

    public function __construct(
        #[FieldName('job_id')]
        public int $jobId,
        #[FieldName('candidate_id')]
        public int $candidateId,
        public string $status,
        #[FieldName('cover_letter')]
        public ?string $coverLetter = null,
        #[FieldName('resume_path')]
        public ?string $resumePath = null,
        #[FieldName('expected_salary')]
        public ?float $expectedSalary = null,
        #[DateFormat('Y-m-d')]
        #[FieldName('available_start_date')]
        public ?\DateTime $availableStartDate = null,
        #[ArrayOf('array')]
        #[FieldName('screening_answers')]
        public array $screeningAnswers = [],
        public ?string $notes = null,
        public ?int $rating = null,
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('applied_at')]
        public ?\DateTime $appliedAt = null,
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('reviewed_at')]
        public ?\DateTime $reviewedAt = null,

        // Application source tracking
        #[FieldName('application_source')]
        public ?string $applicationSource = null,

        // Privacy settings
        #[FieldName('share_contact_details')]
        public bool $shareContactDetails = true,
        #[FieldName('share_expected_salary')]
        public bool $shareExpectedSalary = false,

        // Additional metadata
        #[ArrayOf('array')]
        public array $metadata = [],

        // Sensitive data - ignored in API responses
        #[Ignore]
        public ?string $internalNotes = null,
        #[Ignore]
        public ?array $systemFlags = null
    ) {}

    /**
     * Create from job application model
     */
    public static function fromModel(\App\Models\JobApplication $application): self
    {
        return new self(
            jobId: $application->job_id,
            candidateId: $application->candidate_id,
            status: $application->status,
            coverLetter: $application->cover_letter,
            resumePath: $application->resume_path,
            expectedSalary: $application->expected_salary,
            availableStartDate: $application->available_start_date,
            screeningAnswers: $application->screening_answers ?? [],
            notes: $application->notes,
            rating: $application->rating,
            appliedAt: $application->applied_at,
            reviewedAt: $application->reviewed_at,
            applicationSource: $application->application_source ?? 'website',
            shareContactDetails: $application->settings('privacy.share_with_employer.contact_details', true),
            shareExpectedSalary: $application->settings('privacy.share_with_employer.expected_salary', false),
            metadata: $application->metadata ?? []
        );
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'pending' => 'Application Pending',
            'reviewed' => 'Under Review',
            'shortlisted' => 'Shortlisted',
            'interview_scheduled' => 'Interview Scheduled',
            'interview_completed' => 'Interview Completed',
            'offered' => 'Offer Extended',
            'hired' => 'Hired',
            'rejected' => 'Not Selected',
            'withdrawn' => 'Withdrawn',
            default => ucfirst($this->status)
        };
    }

    /**
     * Check if application is in active state
     */
    public function isActive(): bool
    {
        return in_array($this->status, [
            'pending', 'reviewed', 'shortlisted',
            'interview_scheduled', 'interview_completed', 'offered',
        ]);
    }

    /**
     * Check if application is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['hired', 'rejected', 'withdrawn']);
    }
}
