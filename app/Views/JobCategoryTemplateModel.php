<?php

namespace App\Views;

use App\Models\JobCategory;

/**
 * Job Category Template Model
 *
 * Based on Habr article patterns for model-oriented templating
 */
class JobCategoryTemplateModel extends BaseTemplateModel
{
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $icon;
    public string $color;
    public bool $isActive;
    public int $jobsCount = 0;

    public static function fromJobCategory(JobCategory $category): self
    {
        $model = new self;
        $model->name = $category->name ?? '';
        $model->slug = $category->slug ?? '';
        $model->description = $category->description;
        $model->icon = $category->icon;
        $model->color = $category->color ?? '#3b82f6';
        $model->isActive = (bool) $category->is_active;
        $model->jobsCount = $category->jobs()->count();

        return $model;
    }

    public function url(): string
    {
        return $this->route('categories.show', ['category' => $this->slug]);
    }

    public function badge(): string
    {
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: %s; color: white;">%s</span>',
            $this->color,
            $this->name
        );
    }
}
