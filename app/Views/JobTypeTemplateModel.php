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
        return match($this->name) {
            'Full-time' => 'bg-blue-100 text-blue-800',
            'Part-time' => 'bg-purple-100 text-purple-800',
            'Contract' => 'bg-orange-100 text-orange-800',
            'Freelance' => 'bg-green-100 text-green-800',
            'Internship' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
} 