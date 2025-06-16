<?php

namespace App\Views;

use App\Models\JobType;

/**
 * Job Type Template Model
 * 
 * Based on Habr article patterns for model-oriented templating
 */
class JobTypeTemplateModel extends BaseTemplateModel
{
    public string $name;
    public string $slug;
    public ?string $description;
    public bool $isActive;
    public int $jobsCount = 0;

    public static function fromJobType(JobType $jobType): self
    {
        $model = new self();
        $model->name = $jobType->name ?? '';
        $model->slug = $jobType->slug ?? '';
        $model->description = $jobType->description;
        $model->isActive = (bool)$jobType->is_active;
        $model->jobsCount = $jobType->jobs()->count();
        
        return $model;
    }

    public function badge(): string
    {
        return $this->employmentTypeBadge();
    }
} 