<?php

namespace App\Dtos;

use LumoSolutions\Actionable\Attributes\ArrayOf;
use LumoSolutions\Actionable\Attributes\DateFormat;
use LumoSolutions\Actionable\Attributes\FieldName;
use LumoSolutions\Actionable\Attributes\Ignore;
use LumoSolutions\Actionable\Concerns\ArrayConvertible;

class JobData
{
    use ArrayConvertible;

    public function __construct(
        #[FieldName('job_title')]
        public string $jobTitle,
        
        public string $description,
        
        #[FieldName('company_id')]
        public int $companyId,
        
        #[FieldName('job_category_id')]
        public int $jobCategoryId,
        
        #[FieldName('job_type_id')]
        public int $jobTypeId,
        
        #[FieldName('career_level_id')]
        public ?int $careerLevelId = null,
        
        #[FieldName('functional_area_id')]
        public ?int $functionalAreaId = null,
        
        #[FieldName('job_shift_id')]
        public ?int $jobShiftId = null,
        
        #[FieldName('degree_level_id')]
        public ?int $degreeLevelId = null,
        
        // Location details
        #[FieldName('country_id')]
        public int $countryId,
        
        #[FieldName('state_id')]
        public ?int $stateId = null,
        
        #[FieldName('city_id')]
        public ?int $cityId = null,
        
        public ?string $address = null,
        
        #[FieldName('is_remote')]
        public bool $isRemote = false,
        
        // Salary information
        #[FieldName('salary_from')]
        public ?float $salaryFrom = null,
        
        #[FieldName('salary_to')]
        public ?float $salaryTo = null,
        
        #[FieldName('currency_id')]
        public ?int $currencyId = null,
        
        #[FieldName('salary_period_id')]
        public ?int $salaryPeriodId = null,
        
        #[FieldName('hide_salary')]
        public bool $hideSalary = false,
        
        #[FieldName('salary_negotiable')]
        public bool $salaryNegotiable = true,
        
        // Job settings
        #[DateFormat('Y-m-d')]
        #[FieldName('job_expiry_date')]
        public ?\DateTime $jobExpiryDate = null,
        
        #[FieldName('no_of_positions')]
        public int $numberOfPositions = 1,
        
        #[FieldName('years_experience_required')]
        public ?int $yearsExperienceRequired = null,
        
        #[FieldName('is_featured')]
        public bool $isFeatured = false,
        
        #[FieldName('is_urgent')]
        public bool $isUrgent = false,
        
        #[FieldName('is_freelance')]
        public bool $isFreelance = false,
        
        // Status and visibility
        public string $status = 'draft',
        
        #[FieldName('is_active')]
        public bool $isActive = true,
        
        #[FieldName('auto_publish')]
        public bool $autoPublish = false,
        
        // Requirements and benefits
        #[ArrayOf('string')]
        #[FieldName('key_responsibilities')]
        public array $keyResponsibilities = [],
        
        #[ArrayOf('string')]
        public array $requirements = [],
        
        #[ArrayOf('string')]
        public array $benefits = [],
        
        #[ArrayOf('int')]
        #[FieldName('skill_ids')]
        public array $skillIds = [],
        
        // SEO and metadata
        #[FieldName('meta_title')]
        public ?string $metaTitle = null,
        
        #[FieldName('meta_description')]
        public ?string $metaDescription = null,
        
        #[ArrayOf('string')]
        public array $tags = [],
        
        // Application settings
        #[FieldName('application_email')]
        public ?string $applicationEmail = null,
        
        #[FieldName('application_url')]
        public ?string $applicationUrl = null,
        
        #[FieldName('require_cover_letter')]
        public bool $requireCoverLetter = false,
        
        #[FieldName('screening_questions')]
        public array $screeningQuestions = [],
        
        // Contact information
        #[FieldName('contact_person')]
        public ?string $contactPerson = null,
        
        #[FieldName('contact_email')]
        public ?string $contactEmail = null,
        
        #[FieldName('contact_phone')]
        public ?string $contactPhone = null,
        
        // Analytics and tracking
        #[FieldName('view_count')]
        public int $viewCount = 0,
        
        #[FieldName('application_count')]
        public int $applicationCount = 0,
        
        // Timestamps
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('published_at')]
        public ?\DateTime $publishedAt = null,
        
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('created_at')]
        public ?\DateTime $createdAt = null,
        
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('updated_at')]
        public ?\DateTime $updatedAt = null,
        
        // Internal data - ignored in API responses
        #[Ignore]
        public ?array $internalSettings = null,
        
        #[Ignore]
        public ?string $adminNotes = null
    ) {}

    /**
     * Create from job model
     */
    public static function fromModel(\App\Models\Job $job): self
    {
        return new self(
            jobTitle: $job->job_title,
            description: $job->description,
            companyId: $job->company_id,
            jobCategoryId: $job->job_category_id,
            jobTypeId: $job->job_type_id,
            careerLevelId: $job->career_level_id,
            functionalAreaId: $job->functional_area_id,
            jobShiftId: $job->job_shift_id,
            degreeLevelId: $job->degree_level_id,
            countryId: $job->country_id,
            stateId: $job->state_id,
            cityId: $job->city_id,
            address: $job->address,
            isRemote: $job->is_remote ?? false,
            salaryFrom: $job->salary_from,
            salaryTo: $job->salary_to,
            currencyId: $job->currency_id,
            salaryPeriodId: $job->salary_period_id,
            hideSalary: $job->hide_salary ?? false,
            salaryNegotiable: $job->salary_negotiable ?? true,
            jobExpiryDate: $job->job_expiry_date,
            numberOfPositions: $job->no_of_positions ?? 1,
            yearsExperienceRequired: $job->years_experience_required,
            isFeatured: $job->is_featured ?? false,
            isUrgent: $job->is_urgent ?? false,
            isFreelance: $job->is_freelance ?? false,
            status: $job->status ?? 'draft',
            isActive: $job->is_active ?? true,
            autoPublish: $job->settings('workflow.auto_publish', false),
            keyResponsibilities: $job->key_responsibilities ?? [],
            requirements: $job->requirements ?? [],
            benefits: $job->benefits ?? [],
            skillIds: $job->jobsSkill->pluck('id')->toArray(),
            metaTitle: $job->meta_title,
            metaDescription: $job->meta_description,
            tags: $job->jobsTag->pluck('name')->toArray(),
            applicationEmail: $job->application_email,
            applicationUrl: $job->application_url,
            requireCoverLetter: $job->settings('application.require_cover_letter', false),
            screeningQuestions: $job->settings('workflow.screening_questions', []),
            contactPerson: $job->contact_person,
            contactEmail: $job->contact_email,
            contactPhone: $job->contact_phone,
            viewCount: $job->view_count ?? 0,
            applicationCount: $job->application_count ?? 0,
            publishedAt: $job->published_at,
            createdAt: $job->created_at,
            updatedAt: $job->updated_at
        );
    }

    /**
     * Get formatted salary range
     */
    public function getFormattedSalaryRange(): ?string
    {
        if ($this->hideSalary || (!$this->salaryFrom && !$this->salaryTo)) {
            return null;
        }

        if ($this->salaryFrom && $this->salaryTo) {
            return "{$this->salaryFrom} - {$this->salaryTo}";
        }

        if ($this->salaryFrom) {
            return "From {$this->salaryFrom}";
        }

        if ($this->salaryTo) {
            return "Up to {$this->salaryTo}";
        }

        return null;
    }

    /**
     * Check if job is published and active
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->isActive;
    }

    /**
     * Check if job is expired
     */
    public function isExpired(): bool
    {
        return $this->jobExpiryDate && $this->jobExpiryDate < new \DateTime();
    }

    /**
     * Get job status display name
     */
    public function getStatusDisplayName(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'published' => 'Published',
            'paused' => 'Paused',
            'expired' => 'Expired',
            'closed' => 'Closed',
            default => ucfirst($this->status)
        };
    }
}
