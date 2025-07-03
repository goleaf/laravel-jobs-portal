<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class DeepRelationshipUserLocationJobsRequest
 * Enterprise-grade validation for Enhanced Deep Relationship user location jobs analysis
 */
class DeepRelationshipUserLocationJobsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'location' => [
                'required',
                'string',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],
            'radius_km' => [
                'sometimes',
                'integer',
                'min:1',
                'max:500',
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
            'job_categories' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'job_categories.*' => [
                'integer',
                'min:1',
                Rule::exists('job_categories', 'id'),
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
            'skills_match' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'skills_match.*' => [
                'integer',
                'min:1',
                Rule::exists('skills', 'id'),
            ],
            'relationship_depth' => [
                'sometimes',
                'string',
                'in:basic,medium,deep,comprehensive',
            ],
            'include_remote' => [
                'sometimes',
                'boolean',
            ],
            'date_range' => [
                'sometimes',
                'array',
            ],
            'date_range.from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'date_range.to' => [
                'sometimes',
                'date',
                'after_or_equal:date_range.from',
                'before_or_equal:today',
            ],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'in:relevance,distance,salary,date_posted,match_score',
            ],
            'include_analytics' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'radius_km' => 50,
            'relationship_depth' => 'medium',
            'include_remote' => false,
            'limit' => 20,
            'sort_by' => 'relevance',
            'include_analytics' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'location.required' => __('validation.custom.deep_relationship.location_required'),
            'location.regex' => __('validation.custom.deep_relationship.location_format'),
            'radius_km.max' => __('validation.custom.deep_relationship.radius_too_large'),
            'coordinates.latitude.between' => __('validation.custom.deep_relationship.latitude_invalid'),
            'coordinates.longitude.between' => __('validation.custom.deep_relationship.longitude_invalid'),
            'job_categories.max' => __('validation.custom.deep_relationship.categories_limit'),
            'experience_levels.*.in' => __('validation.custom.deep_relationship.experience_invalid'),
            'salary_range.max.gte' => __('validation.custom.deep_relationship.salary_range_invalid'),
            'skills_match.max' => __('validation.custom.deep_relationship.skills_limit'),
            'relationship_depth.in' => __('validation.custom.deep_relationship.depth_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('location')) {
            $this->merge(['location' => trim($this->location)]);
        }

        foreach (['job_categories', 'experience_levels', 'skills_match'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
