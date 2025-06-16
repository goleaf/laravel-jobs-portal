<?php

namespace App\Views;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobType;
use Carbon\Carbon;

/**
 * Job Template Model
 * 
 * Based on Habr article patterns for model-oriented templating
 * Provides typed properties and methods for job-related templates
 */
class JobTemplateModel extends BaseTemplateModel
{
    public string $title;
    public string $description;
    public ?string $keyResponsibilities;
    public string $requirements;
    public ?float $salaryFrom;
    public ?float $salaryTo;
    public string $salaryCurrency;
    public string $salaryPeriod;
    public string $location;
    public Carbon $deadline;
    public Carbon $createdAt;
    public Carbon $updatedAt;
    public bool $isFeatured;
    public bool $isActive;
    public string $status;
    public int $experienceYears;
    public string $employmentType;
    public string $workType; // remote, onsite, hybrid
    
    // Related models
    public CompanyTemplateModel $company;
    public JobCategoryTemplateModel $category;
    public JobTypeTemplateModel $jobType;
    public array $skills = [];
    public array $applications = [];
    public int $applicationsCount = 0;
    
    // SEO and metadata
    public string $slug;
    public string $metaTitle;
    public string $metaDescription;
    public array $metaKeywords = [];
    
    // Display helpers
    public string $statusLabel;
    public string $urgencyLevel;
    public bool $isUrgent;
    public bool $isExpired;
    public int $daysUntilDeadline;

    /**
     * Create from Job model
     */
    public static function fromJob(Job $job): self
    {
        $model = new self();
        
        // Basic properties
        $model->title = $job->title ?? '';
        $model->description = $job->description ?? '';
        $model->keyResponsibilities = $job->key_responsibilities;
        $model->requirements = $job->requirements ?? '';
        $model->salaryFrom = $job->salary_from;
        $model->salaryTo = $job->salary_to;
        $model->salaryCurrency = $job->salary_currency ?? 'USD';
        $model->salaryPeriod = $job->salary_period ?? 'month';
        $model->location = $job->location ?? '';
        $model->deadline = $job->deadline ? Carbon::parse($job->deadline) : Carbon::now()->addMonth();
        $model->createdAt = Carbon::parse($job->created_at);
        $model->updatedAt = Carbon::parse($job->updated_at);
        $model->isFeatured = (bool)$job->is_featured;
        $model->isActive = (bool)$job->is_active;
        $model->status = $job->status ?? 'active';
        $model->experienceYears = $job->experience_years ?? 0;
        $model->employmentType = $job->employment_type ?? 'full-time';
        $model->workType = $job->work_type ?? 'onsite';
        
        // Related models
        if ($job->company) {
            $model->company = CompanyTemplateModel::fromCompany($job->company);
        }
        
        if ($job->category) {
            $model->category = JobCategoryTemplateModel::fromJobCategory($job->category);
        }
        
        if ($job->jobType) {
            $model->jobType = JobTypeTemplateModel::fromJobType($job->jobType);
        }
        
        // Skills
        $model->skills = $job->skills ? $job->skills->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'slug' => $skill->slug ?? '',
            ];
        })->toArray() : [];
        
        // Applications count
        $model->applicationsCount = $job->applications()->count();
        
        // SEO
        $model->slug = $job->slug ?? '';
        $model->metaTitle = $job->meta_title ?: $job->title;
        $model->metaDescription = $job->meta_description ?: $model->truncate($job->description ?? '', 160);
        $model->metaKeywords = !empty($job->meta_keywords) 
            ? explode(',', $job->meta_keywords) 
            : array_column($model->skills, 'name');
        
        // Display helpers
        $model->statusLabel = ucfirst($model->status);
        $model->isExpired = $model->deadline->isPast();
        $model->daysUntilDeadline = $model->deadline->diffInDays(Carbon::now());
        $model->isUrgent = $model->daysUntilDeadline <= 7;
        $model->urgencyLevel = $model->getUrgencyLevel();
        
        return $model;
    }

    /**
     * Get formatted salary range
     */
    public function salaryRange(): string
    {
        if (!$this->salaryFrom && !$this->salaryTo) {
            return 'Salary negotiable';
        }
        
        if ($this->salaryFrom && $this->salaryTo) {
            return $this->formatCurrency($this->salaryFrom, $this->salaryCurrency) . 
                   ' - ' . 
                   $this->formatCurrency($this->salaryTo, $this->salaryCurrency) . 
                   ' per ' . $this->salaryPeriod;
        }
        
        if ($this->salaryFrom) {
            return 'From ' . $this->formatCurrency($this->salaryFrom, $this->salaryCurrency) . 
                   ' per ' . $this->salaryPeriod;
        }
        
        return 'Up to ' . $this->formatCurrency($this->salaryTo, $this->salaryCurrency) . 
               ' per ' . $this->salaryPeriod;
    }

    /**
     * Get urgency level
     */
    public function getUrgencyLevel(): string
    {
        if ($this->isExpired) {
            return 'expired';
        }
        
        if ($this->daysUntilDeadline <= 3) {
            return 'critical';
        }
        
        if ($this->daysUntilDeadline <= 7) {
            return 'urgent';
        }
        
        if ($this->daysUntilDeadline <= 14) {
            return 'moderate';
        }
        
        return 'normal';
    }

    /**
     * Get urgency badge class
     */
    public function urgencyBadge(): string
    {
        return match($this->urgencyLevel) {
            'expired' => 'bg-red-100 text-red-800 border-red-200',
            'critical' => 'bg-red-100 text-red-800 border-red-200',
            'urgent' => 'bg-orange-100 text-orange-800 border-orange-200',
            'moderate' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            default => 'bg-green-100 text-green-800 border-green-200',
        };
    }

    /**
     * Get formatted deadline
     */
    public function deadlineFormatted(): string
    {
        if ($this->isExpired) {
            return 'Expired ' . $this->humanDate($this->deadline);
        }
        
        return 'Deadline: ' . $this->deadline->format('M j, Y') . ' (' . $this->humanDate($this->deadline) . ')';
    }

    /**
     * Get experience level text
     */
    public function experienceLevel(): string
    {
        if ($this->experienceYears === 0) {
            return 'Entry Level';
        }
        
        if ($this->experienceYears <= 2) {
            return 'Junior (' . $this->experienceYears . ' years)';
        }
        
        if ($this->experienceYears <= 5) {
            return 'Mid-level (' . $this->experienceYears . ' years)';
        }
        
        return 'Senior (' . $this->experienceYears . '+ years)';
    }

    /**
     * Get work type icon
     */
    public function workTypeIcon(): string
    {
        return match($this->workType) {
            'remote' => '🏠',
            'hybrid' => '🔄',
            'onsite' => '🏢',
            default => '🏢',
        };
    }

    /**
     * Get employment type badge
     */
    public function employmentTypeBadge(): string
    {
        return match($this->employmentType) {
            'full-time' => 'bg-blue-100 text-blue-800',
            'part-time' => 'bg-purple-100 text-purple-800',
            'contract' => 'bg-orange-100 text-orange-800',
            'freelance' => 'bg-green-100 text-green-800',
            'internship' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get skills as formatted string
     */
    public function skillsList(): string
    {
        return implode(', ', array_column($this->skills, 'name'));
    }

    /**
     * Get job URL
     */
    public function url(): string
    {
        return $this->route('jobs.show', ['job' => $this->slug]);
    }

    /**
     * Get apply URL
     */
    public function applyUrl(): string
    {
        return $this->route('jobs.apply', ['job' => $this->slug]);
    }

    /**
     * Check if user can apply
     */
    public function canApply(): bool
    {
        if ($this->isExpired || !$this->isActive) {
            return false;
        }
        
        $user = $this->currentUser();
        if (!$user) {
            return false;
        }
        
        // Check if user already applied
        return !in_array($user->id, array_column($this->applications, 'user_id'));
    }

    /**
     * Get structured data for SEO
     */
    public function structuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $this->title,
            'description' => $this->description,
            'datePosted' => $this->createdAt->toISOString(),
            'validThrough' => $this->deadline->toISOString(),
            'employmentType' => strtoupper(str_replace('-', '_', $this->employmentType)),
            'workType' => strtoupper($this->workType),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $this->company->name ?? '',
                'logo' => $this->company->logoUrl ?? '',
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => $this->location,
            ],
            'baseSalary' => $this->salaryFrom ? [
                '@type' => 'MonetaryAmount',
                'currency' => $this->salaryCurrency,
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $this->salaryFrom,
                    'maxValue' => $this->salaryTo,
                    'unitText' => strtoupper($this->salaryPeriod),
                ],
            ] : null,
        ];
    }
} 