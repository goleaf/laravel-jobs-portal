<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProfessionIndexRequest
 * Enterprise-grade validation for API Profession index operations
 */
class ProfessionIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
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
            'category_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('profession_categories', 'id')->where('is_active', 1),
            ],
            'status' => [
                'sometimes',
                'string',
                'in:active,inactive,all',
            ],
            'featured' => [
                'sometimes',
                'boolean',
            ],
            'popular' => [
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
                'min:1',
                'max:100',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'in:name,created_at,updated_at,popularity,category',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],
            'include' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'include.*' => [
                'string',
                'in:category,jobs_count,statistics,meta',
            ],
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,xml,csv',
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
            'status' => 'active',
            'featured' => false,
            'popular' => false,
            'page' => 1,
            'per_page' => 15,
            'sort_by' => 'name',
            'sort_direction' => 'asc',
            'locale' => app()->getLocale(),
            'format' => 'json',
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'search.regex' => __('validation.custom.api.search_format'),
            'category_id.exists' => __('validation.custom.api.category_not_found'),
            'status.in' => __('validation.custom.api.status_invalid'),
            'per_page.max' => __('validation.custom.api.per_page_limit'),
            'sort_by.in' => __('validation.custom.api.sort_field_invalid'),
            'include.max' => __('validation.custom.api.include_limit'),
            'include.*.in' => __('validation.custom.api.include_invalid'),
            'locale.regex' => __('validation.custom.api.locale_format'),
            'format.in' => __('validation.custom.api.format_invalid'),
            'api_version.in' => __('validation.custom.api.version_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $this->merge(['search' => trim($this->search)]);
        }

        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        if ($this->has('include') && is_string($this->input('include'))) {
            $this->merge(['include' => explode(',', $this->input('include'))]);
        }
    }
}
