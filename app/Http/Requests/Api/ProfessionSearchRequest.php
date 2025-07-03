<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProfessionSearchRequest
 * Enterprise-grade validation for API Profession search operations
 */
class ProfessionSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
                'min:1',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'search_type' => [
                'sometimes',
                'string',
                'in:exact,fuzzy,partial,phonetic',
            ],
            'categories' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'categories.*' => [
                'integer',
                'min:1',
                Rule::exists('profession_categories', 'id'),
            ],
            'exclude_categories' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'exclude_categories.*' => [
                'integer',
                'min:1',
            ],
            'min_popularity' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
            ],
            'location_based' => [
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
            'radius_km' => [
                'sometimes',
                'integer',
                'min:1',
                'max:500',
            ],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'include_suggestions' => [
                'sometimes',
                'boolean',
            ],
            'highlight_results' => [
                'sometimes',
                'boolean',
            ],
            'boost_featured' => [
                'sometimes',
                'boolean',
            ],
            'language' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'response_format' => [
                'sometimes',
                'string',
                'in:standard,detailed,minimal',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'search_type' => 'fuzzy',
            'location_based' => false,
            'radius_km' => 50,
            'limit' => 20,
            'include_suggestions' => true,
            'highlight_results' => false,
            'boost_featured' => true,
            'language' => app()->getLocale(),
            'response_format' => 'standard',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'query.required' => __('validation.custom.search.query_required'),
            'query.regex' => __('validation.custom.search.query_format'),
            'search_type.in' => __('validation.custom.search.type_invalid'),
            'categories.max' => __('validation.custom.search.categories_limit'),
            'coordinates.latitude.between' => __('validation.custom.search.latitude_invalid'),
            'coordinates.longitude.between' => __('validation.custom.search.longitude_invalid'),
            'radius_km.max' => __('validation.custom.search.radius_too_large'),
            'limit.max' => __('validation.custom.search.limit_exceeded'),
            'language.regex' => __('validation.custom.search.language_format'),
            'response_format.in' => __('validation.custom.search.format_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('query')) {
            $this->merge(['query' => trim($this->query)]);
        }

        if ($this->has('language')) {
            $this->merge(['language' => strtolower(trim($this->language))]);
        }

        foreach (['categories', 'exclude_categories'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
