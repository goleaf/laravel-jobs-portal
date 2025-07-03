<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UniversalSearchRequest
 * Enterprise-grade validation for Universal Search operations across all entities
 */
class UniversalSearchRequest extends FormRequest
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
                'max:500',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)\[\]]+$/u',
            ],
            'entities' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'entities.*' => [
                'string',
                'in:jobs,companies,candidates,skills,professions,categories,applications,reviews,locations',
            ],
            'search_type' => [
                'sometimes',
                'string',
                'in:exact,fuzzy,partial,boolean,semantic,ai_powered',
            ],
            'filters' => [
                'sometimes',
                'array',
                'max:25',
            ],
            'filters.*.entity' => [
                'required',
                'string',
                'in:jobs,companies,candidates,skills,professions,categories,applications',
            ],
            'filters.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'filters.*.operator' => [
                'required',
                'string',
                'in:equals,not_equals,contains,starts_with,ends_with,greater_than,less_than,between,in,not_in,exists,not_exists',
            ],
            'filters.*.value' => [
                'required',
            ],
            'scoring' => [
                'sometimes',
                'array',
            ],
            'scoring.relevance_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],
            'scoring.recency_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],
            'scoring.popularity_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],
            'scoring.quality_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],
            'geospatial' => [
                'sometimes',
                'array',
            ],
            'geospatial.latitude' => [
                'required_with:geospatial',
                'numeric',
                'between:-90,90',
            ],
            'geospatial.longitude' => [
                'required_with:geospatial',
                'numeric',
                'between:-180,180',
            ],
            'geospatial.radius_km' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],
            'geospatial.precision' => [
                'sometimes',
                'string',
                'in:city,region,country,exact',
            ],
            'aggregations' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'aggregations.*' => [
                'array',
            ],
            'aggregations.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'aggregations.*.type' => [
                'required',
                'string',
                'in:terms,range,date_histogram,stats,cardinality',
            ],
            'aggregations.*.size' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'highlighting' => [
                'sometimes',
                'array',
            ],
            'highlighting.enabled' => [
                'boolean',
            ],
            'highlighting.fields' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'highlighting.fields.*' => [
                'string',
                'max:50',
            ],
            'highlighting.fragment_size' => [
                'sometimes',
                'integer',
                'min:50',
                'max:500',
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
            'sort' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'sort.*' => [
                'array',
            ],
            'sort.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'sort.*.direction' => [
                'required',
                'string',
                'in:asc,desc',
            ],
            'sort.*.priority' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10',
            ],
            'include_suggestions' => [
                'sometimes',
                'boolean',
            ],
            'suggestion_count' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10',
            ],
            'faceted_search' => [
                'sometimes',
                'boolean',
            ],
            'export_format' => [
                'sometimes',
                'string',
                'in:json,csv,xml,excel',
            ],
            'ai_enhancement' => [
                'sometimes',
                'boolean',
            ],
            'language' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'search_type' => 'fuzzy',
            'geospatial.radius_km' => 50,
            'geospatial.precision' => 'city',
            'highlighting.enabled' => true,
            'highlighting.fragment_size' => 150,
            'page' => 1,
            'per_page' => 20,
            'include_suggestions' => true,
            'suggestion_count' => 5,
            'faceted_search' => false,
            'export_format' => 'json',
            'ai_enhancement' => false,
            'language' => app()->getLocale(),
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'query.required' => __('validation.custom.universal_search.query_required'),
            'query.regex' => __('validation.custom.universal_search.query_format'),
            'entities.required' => __('validation.custom.universal_search.entities_required'),
            'entities.max' => __('validation.custom.universal_search.entities_limit'),
            'entities.*.in' => __('validation.custom.universal_search.entity_invalid'),
            'search_type.in' => __('validation.custom.universal_search.search_type_invalid'),
            'filters.max' => __('validation.custom.universal_search.filters_limit'),
            'geospatial.latitude.between' => __('validation.custom.universal_search.latitude_invalid'),
            'geospatial.longitude.between' => __('validation.custom.universal_search.longitude_invalid'),
            'geospatial.radius_km.max' => __('validation.custom.universal_search.radius_too_large'),
            'aggregations.max' => __('validation.custom.universal_search.aggregations_limit'),
            'per_page.max' => __('validation.custom.universal_search.per_page_limit'),
            'language.regex' => __('validation.custom.universal_search.language_format'),
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

        foreach (['entities', 'highlighting.fields'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
