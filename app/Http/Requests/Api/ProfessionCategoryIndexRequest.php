<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProfessionCategoryIndexRequest
 * Enterprise-grade validation for API Profession Category index operations
 */
class ProfessionCategoryIndexRequest extends FormRequest
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
            'parent_id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'level' => [
                'sometimes',
                'integer',
                'min:0',
                'max:5',
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
            'with_children' => [
                'sometimes',
                'boolean',
            ],
            'children_depth' => [
                'sometimes',
                'integer',
                'min:1',
                'max:3',
            ],
            'with_professions_count' => [
                'sometimes',
                'boolean',
            ],
            'with_statistics' => [
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
                'in:name,created_at,updated_at,professions_count,order_index',
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
                'in:parent,children,professions,statistics,meta,translations',
            ],
            'flat_tree' => [
                'sometimes',
                'boolean',
            ],
            'tree_format' => [
                'sometimes',
                'string',
                'in:nested,flat,hierarchical',
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
                'in:json,xml,tree',
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
            'with_children' => false,
            'children_depth' => 1,
            'with_professions_count' => false,
            'with_statistics' => false,
            'page' => 1,
            'per_page' => 20,
            'sort_by' => 'order_index',
            'sort_direction' => 'asc',
            'flat_tree' => false,
            'tree_format' => 'nested',
            'locale' => app()->getLocale(),
            'format' => 'json',
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'search.regex' => __('validation.custom.api.search_format'),
            'level.max' => __('validation.custom.category.level_limit'),
            'status.in' => __('validation.custom.api.status_invalid'),
            'children_depth.max' => __('validation.custom.category.depth_limit'),
            'per_page.max' => __('validation.custom.api.per_page_limit'),
            'sort_by.in' => __('validation.custom.api.sort_field_invalid'),
            'include.max' => __('validation.custom.api.include_limit'),
            'include.*.in' => __('validation.custom.api.include_invalid'),
            'tree_format.in' => __('validation.custom.category.tree_format_invalid'),
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
