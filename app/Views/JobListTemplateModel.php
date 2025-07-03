<?php

namespace App\Views;

/**
 * Job List Template Model
 *
 * Based on Habr article patterns for model-oriented templating
 * Handles collections of jobs with pagination and filtering
 */
class JobListTemplateModel extends BaseTemplateModel
{
    public string $title;
    public string $description;
    public array $jobs = [];
    public int $totalCount = 0;
    public bool $showPagination = false;
    public int $currentPage = 1;
    public int $perPage = 20;
    public array $filters = [];
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public bool $showFilters = true;
    public string $viewType = 'list'; // list, grid, table

    /**
     * Get pagination data
     */
    public function pagination(): array
    {
        if (! $this->showPagination) {
            return [];
        }

        $totalPages = ceil($this->totalCount / $this->perPage);

        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total' => $this->totalCount,
            'total_pages' => $totalPages,
            'has_previous' => $this->currentPage > 1,
            'has_next' => $this->currentPage < $totalPages,
            'previous_page' => max(1, $this->currentPage - 1),
            'next_page' => min($totalPages, $this->currentPage + 1),
        ];
    }

    /**
     * Get featured jobs
     */
    public function featuredJobs(): array
    {
        return array_filter($this->jobs, function ($job) {
            return $job->isFeatured ?? false;
        });
    }

    /**
     * Get urgent jobs
     */
    public function urgentJobs(): array
    {
        return array_filter($this->jobs, function ($job) {
            return $job->isUrgent ?? false;
        });
    }

    /**
     * Get jobs by status
     */
    public function jobsByStatus(string $status): array
    {
        return array_filter($this->jobs, function ($job) use ($status) {
            return $job->status === $status;
        });
    }

    /**
     * Get statistics
     */
    public function statistics(): array
    {
        $featured = count($this->featuredJobs());
        $urgent = count($this->urgentJobs());
        $expired = count($this->jobsByStatus('expired'));

        return [
            'total' => $this->totalCount,
            'showing' => count($this->jobs),
            'featured' => $featured,
            'urgent' => $urgent,
            'expired' => $expired,
            'active' => $this->totalCount - $expired,
        ];
    }

    /**
     * Get filter summary
     */
    public function filterSummary(): string
    {
        $parts = [];

        if (! empty($this->filters['category'])) {
            $parts[] = 'Category: '.$this->filters['category'];
        }

        if (! empty($this->filters['location'])) {
            $parts[] = 'Location: '.$this->filters['location'];
        }

        if (! empty($this->filters['salary_range'])) {
            $parts[] = 'Salary: '.$this->filters['salary_range'];
        }

        if (! empty($this->filters['experience'])) {
            $parts[] = 'Experience: '.$this->filters['experience'];
        }

        return empty($parts) ? 'All jobs' : implode(' • ', $parts);
    }

    /**
     * Get CSS class for view type
     */
    public function viewTypeClass(): string
    {
        return match ($this->viewType) {
            'grid' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
            'table' => 'overflow-x-auto',
            default => 'space-y-4',
        };
    }
}
