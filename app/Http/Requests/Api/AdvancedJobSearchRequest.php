<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AdvancedJobSearchRequest
 * Enterprise-grade validation for API Advanced Job Search operations
 */
class AdvancedJobSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'query' => [
                'sometimes',
                'string',
                'min:1',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'job_categories' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'job_categories.*' => [
                'integer',
                'min:1',
                Rule::exists('job_categories', 'id'),
            ],
            'locations' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'locations.*' => [
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],
            'salary_range' => [
                'sometimes',
                'array',
            ],
            'salary_range.min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
            ],
            'salary_range.max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
                'gte:salary_range.min',
            ],
            'salary_currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'experience_levels' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'experience_levels.*' => [
                'string',
                'in:entry_level,mid_level,senior_level,executive',
            ],
            'job_types' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'job_types.*' => [
                'string',
                'in:full_time,part_time,contract,freelance,internship,temporary',
            ],
            'remote_work' => [
                'sometimes',
                'string',
                'in:on_site,remote,hybrid,flexible',
            ],
            'companies' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'companies.*' => [
                'integer',
                'min:1',
                Rule::exists('companies', 'id'),
            ],
            'skills' => [
                'sometimes',
                'array',
                'max:25',
            ],
            'skills.*' => [
                'integer',
                'min:1',
                Rule::exists('skills', 'id'),
            ],
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
            'featured_only' => [
                'sometimes',
                'boolean',
            ],
            'urgent_only' => [
                'sometimes',
                'boolean',
            ],
            'has_benefits' => [
                'sometimes',
                'boolean',
            ],
            'coordinates' => [
                'sometimes',
                'array',
            ],
            'coordinates.latitude' => [
                'required_with:coordinates',
                'numeric',
                'between:-90,90',
            ],
            'coordinates.longitude' => [
                'required_with:coordinates',
                'numeric',
                'between:-180,180',
            ],
            'search_radius' => [
                'sometimes',
                'integer',
                'min:1',
                'max:500',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'in:relevance,date,salary,distance,company,experience',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'include_metadata' => [
                'sometimes',
                'boolean',
            ],
            'include_company_details' => [
                'sometimes',
                'boolean',
            ],
            'include_salary_details' => [
                'sometimes',
                'boolean',
            ],
            'api_version' => [
                'sometimes',
                'string',
                'in:v1,v2,latest',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'search_radius' => 50,
            'sort_by' => 'relevance',
            'sort_direction' => 'desc',
            'page' => 1,
            'per_page' => 20,
            'include_metadata' => false,
            'include_company_details' => false,
            'include_salary_details' => false,
            'featured_only' => false,
            'urgent_only' => false,
            'has_benefits' => false,
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'query.regex' => __('validation.custom.job_search.query_format'),
            'job_categories.max' => __('validation.custom.job_search.categories_limit'),
            'locations.max' => __('validation.custom.job_search.locations_limit'),
            'salary_range.max.gte' => __('validation.custom.job_search.salary_range_invalid'),
            'salary_currency.regex' => __('validation.custom.job_search.currency_format'),
            'experience_levels.*.in' => __('validation.custom.job_search.experience_invalid'),
            'job_types.*.in' => __('validation.custom.job_search.type_invalid'),
            'remote_work.in' => __('validation.custom.job_search.remote_invalid'),
            'companies.max' => __('validation.custom.job_search.companies_limit'),
            'skills.max' => __('validation.custom.job_search.skills_limit'),
            'coordinates.latitude.between' => __('validation.custom.job_search.latitude_invalid'),
            'coordinates.longitude.between' => __('validation.custom.job_search.longitude_invalid'),
            'search_radius.max' => __('validation.custom.job_search.radius_too_large'),
            'per_page.max' => __('validation.custom.job_search.per_page_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('query')) {
            $this->merge(['query' => trim($this->query)]);
        }

        if ($this->has('salary_currency')) {
            $this->merge(['salary_currency' => strtoupper(trim($this->salary_currency))]);
        }

        foreach (['job_categories', 'locations', 'experience_levels', 'job_types', 'companies', 'skills'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
