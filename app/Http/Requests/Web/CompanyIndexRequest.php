<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CompanyIndexRequest
 * Enterprise-grade validation for Web Company index operations
 */
class CompanyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access
    }

    public function rules(): array
    {
        return [
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'location' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],
            'industry' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'industry.*' => [
                'integer',
                'min:1',
                Rule::exists('industries', 'id')->where('is_active', 1),
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
            'ownership_type' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'ownership_type.*' => [
                'integer',
                'min:1',
                Rule::exists('ownership_types', 'id')->where('is_active', 1),
            ],
            'featured' => [
                'sometimes',
                'boolean',
            ],
            'has_jobs' => [
                'sometimes',
                'boolean',
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
                'min:5',
                'max:50',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'in:name,created_at,job_count,industry,location',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],
            'view_type' => [
                'sometimes',
                'string',
                'in:list,grid,map',
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
            'view_type' => 'grid',
            'featured' => false,
            'has_jobs' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'search.regex' => __('validation.custom.company.search_format'),
            'location.regex' => __('validation.custom.company.location_format'),
            'industry.max' => __('validation.custom.company.industry_limit'),
            'company_size.max' => __('validation.custom.company.size_limit'),
            'per_page.max' => __('validation.custom.company.per_page_limit'),
            'sort_by.in' => __('validation.custom.company.sort_field_invalid'),
            'view_type.in' => __('validation.custom.company.view_type_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $this->merge(['search' => trim($this->search)]);
        }
        if ($this->has('location')) {
            $this->merge(['location' => trim($this->location)]);
        }

        foreach (['industry', 'company_size', 'ownership_type'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
