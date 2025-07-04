<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class IndexJobRequest
 * Enterprise-grade validation for Web Job index operations
 * Handles public job listings with advanced filtering and search
 */
class IndexJobRequest extends FormRequest
{
    private const MAX_SEARCH_LENGTH = 200;
    private const MAX_FILTER_VALUES = 20;
    private const ALLOWED_SORT_FIELDS = [
        'created_at', 'updated_at', 'job_title', 'salary_from',
        'salary_to', 'deadline', 'company_name', 'location',
    ];

    private const ALLOWED_JOB_TYPES = [
        'full_time', 'part_time', 'contract', 'freelance',
        'internship', 'temporary', 'remote',
    ];

    public function authorize(): bool
    {
        return true; // Public access for job listings
    }

    public function rules(): array
    {
        return [
            // Search & Keywords
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:'.self::MAX_SEARCH_LENGTH,
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'keyword' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.]+$/u',
            ],

            // Location Filtering
            'location' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],
            'city' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-_\.]+$/u',
            ],
            'state' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-_\.]+$/u',
            ],
            'country' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],

            // Job Category & Type Filtering
            'job_category_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('job_categories', 'id')->where('is_active', 1),
            ],
            'job_type' => [
                'sometimes',
                'array',
                'max:'.self::MAX_FILTER_VALUES,
            ],
            'job_type.*' => [
                'string',
                Rule::in(self::ALLOWED_JOB_TYPES),
            ],
            'job_shift' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'job_shift.*' => [
                'integer',
                'min:1',
                Rule::exists('job_shifts', 'id')->where('is_active', 1),
            ],

            // Salary Filtering
            'salary_from' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
            ],
            'salary_to' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
                'gte:salary_from',
            ],
            'salary_period' => [
                'sometimes',
                'string',
                'in:per_hour,per_day,per_week,per_month,per_year',
            ],

            // Experience & Education
            'experience_level' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'experience_level.*' => [
                'string',
                'in:entry_level,mid_level,senior_level,executive',
            ],
            'degree_level' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'degree_level.*' => [
                'integer',
                'min:1',
                Rule::exists('required_degree_levels', 'id')->where('is_active', 1),
            ],

            // Company Filtering
            'company_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('companies', 'id')->where('is_active', 1),
            ],
            'company_size' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'company_size.*' => [
                'string',
                'in:1-10,11-50,51-200,201-500,501-1000,1000+',
            ],

            // Date Filtering
            'posted_within' => [
                'sometimes',
                'string',
                'in:today,3_days,week,2_weeks,month,3_months,6_months',
            ],
            'deadline_within' => [
                'sometimes',
                'string',
                'in:today,3_days,week,2_weeks,month,3_months',
            ],

            // Skills & Tags
            'skills' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'skills.*' => [
                'integer',
                'min:1',
                Rule::exists('skills', 'id')->where('is_active', 1),
            ],
            'tags' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'tags.*' => [
                'integer',
                'min:1',
                Rule::exists('tags', 'id')->where('is_active', 1),
            ],

            // Status & Visibility
            'status' => [
                'sometimes',
                'string',
                'in:open,closed,featured,urgent',
            ],
            'featured' => [
                'sometimes',
                'boolean',
            ],
            'remote_work' => [
                'sometimes',
                'boolean',
            ],

            // Pagination & Sorting
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:50',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(self::ALLOWED_SORT_FIELDS),
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],

            // View & Display Options
            'view_type' => [
                'sometimes',
                'string',
                'in:list,grid,map',
            ],
            'show_expired' => [
                'sometimes',
                'boolean',
            ],

            // Advanced Filters
            'work_arrangement' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'work_arrangement.*' => [
                'string',
                'in:on_site,remote,hybrid,flexible',
            ],
            'employment_type' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'employment_type.*' => [
                'string',
                'in:permanent,contract,temporary,casual,internship',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'page' => 1,
            'per_page' => 15,
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
            'view_type' => 'list',
            'show_expired' => false,
            'featured' => false,
            'remote_work' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'search.regex' => __('validation.custom.job.search_format'),
            'keyword.regex' => __('validation.custom.job.keyword_format'),
            'location.regex' => __('validation.custom.job.location_format'),
            'city.regex' => __('validation.custom.job.city_format'),
            'state.regex' => __('validation.custom.job.state_format'),
            'country.regex' => __('validation.custom.job.country_format'),
            'job_category_id.exists' => __('validation.custom.job.category_invalid'),
            'job_type.*.in' => __('validation.custom.job.type_invalid'),
            'salary_to.gte' => __('validation.custom.job.salary_range_invalid'),
            'skills.max' => __('validation.custom.job.skills_limit'),
            'tags.max' => __('validation.custom.job.tags_limit'),
            'per_page.max' => __('validation.custom.job.per_page_limit'),
            'sort_by.in' => __('validation.custom.job.sort_field_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize search terms
        if ($this->has('search')) {
            $this->merge(['search' => trim($this->search)]);
        }
        if ($this->has('keyword')) {
            $this->merge(['keyword' => trim($this->keyword)]);
        }
        if ($this->has('location')) {
            $this->merge(['location' => trim($this->location)]);
        }

        // Normalize country code
        if ($this->has('country')) {
            $this->merge(['country' => strtoupper(trim($this->country))]);
        }

        // Convert string arrays to proper arrays for multi-select filters
        foreach (['job_type', 'job_shift', 'experience_level', 'degree_level', 'company_size', 'skills', 'tags', 'work_arrangement', 'employment_type'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }

    public function getBusinessContext(): array
    {
        return [
            'operation' => 'job_index',
            'is_search' => $this->filled('search') || $this->filled('keyword'),
            'is_filtered' => $this->hasFilters(),
            'filter_count' => $this->getFilterCount(),
            'is_location_based' => $this->hasLocationFilters(),
            'performance_mode' => $this->input('per_page', 15) <= 25,
        ];
    }

    private function hasFilters(): bool
    {
        $filterFields = ['job_category_id', 'job_type', 'salary_from', 'company_id', 'skills', 'featured'];

        return collect($filterFields)->some(fn ($field) => $this->filled($field));
    }

    private function getFilterCount(): int
    {
        $filterFields = ['job_category_id', 'job_type', 'salary_from', 'company_id', 'skills', 'location', 'featured'];

        return collect($filterFields)->filter(fn ($field) => $this->filled($field))->count();
    }

    private function hasLocationFilters(): bool
    {
        return $this->filled(['location', 'city', 'state', 'country']);
    }
}
