<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SearchJobRequest
 * 
 * Comprehensive request validation for job search and filtering with multilanguage support
 */
class SearchJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public search functionality
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'keyword' => [
                'nullable',
                'string',
                'min:2',
                'max:200'
            ],
            'job_category_id' => [
                'nullable',
                'integer',
                'exists:job_categories,id'
            ],
            'job_type_id' => [
                'nullable',
                'integer',
                'exists:job_types,id'
            ],
            'career_level_id' => [
                'nullable',
                'integer',
                'exists:career_levels,id'
            ],
            'functional_area_id' => [
                'nullable',
                'integer',
                'exists:functional_areas,id'
            ],
            'industry_id' => [
                'nullable',
                'integer',
                'exists:industries,id'
            ],
            'company_id' => [
                'nullable',
                'integer',
                'exists:companies,id'
            ],
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id'
            ],
            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id'
            ],
            'city_id' => [
                'nullable',
                'integer',
                'exists:cities,id'
            ],
            'salary_from' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99'
            ],
            'salary_to' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                'gte:salary_from'
            ],
            'salary_currency_id' => [
                'nullable',
                'integer',
                'exists:salary_currencies,id'
            ],
            'experience_from' => [
                'nullable',
                'integer',
                'min:0',
                'max:50'
            ],
            'experience_to' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
                'gte:experience_from'
            ],
            'job_shift_id' => [
                'nullable',
                'integer',
                'exists:job_shifts,id'
            ],
            'degree_level_id' => [
                'nullable',
                'integer',
                'exists:required_degree_levels,id'
            ],
            'is_freelance' => [
                'nullable',
                'boolean'
            ],
            'is_featured' => [
                'nullable',
                'boolean'
            ],
            'posted_within' => [
                'nullable',
                'integer',
                'min:1',
                'max:365'
            ],
            'sort_by' => [
                'nullable',
                'string',
                Rule::in([
                    'created_at',
                    'job_title', 
                    'salary_from',
                    'experience',
                    'job_expiry_date',
                    'company_name',
                    'relevance'
                ])
            ],
            'sort_direction' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc'])
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:5',
                'max:100'
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'skills' => [
                'nullable',
                'array',
                'max:20'
            ],
            'skills.*' => [
                'integer',
                'exists:skills,id'
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10'
            ],
            'tags.*' => [
                'integer',
                'exists:tags,id'
            ],
            'hide_expired' => [
                'nullable',
                'boolean'
            ],
            'remote_only' => [
                'nullable',
                'boolean'
            ],
            'with_salary' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom validation messages with multilanguage support.
     */
    public function messages(): array
    {
        return [
            'keyword.string' => __('validation.search.keyword_string'),
            'keyword.min' => __('validation.search.keyword_min'),
            'keyword.max' => __('validation.search.keyword_max'),
            
            'job_category_id.integer' => __('validation.search.category_integer'),
            'job_category_id.exists' => __('validation.search.category_exists'),
            
            'job_type_id.integer' => __('validation.search.type_integer'),
            'job_type_id.exists' => __('validation.search.type_exists'),
            
            'career_level_id.integer' => __('validation.search.career_level_integer'),
            'career_level_id.exists' => __('validation.search.career_level_exists'),
            
            'functional_area_id.integer' => __('validation.search.functional_area_integer'),
            'functional_area_id.exists' => __('validation.search.functional_area_exists'),
            
            'industry_id.integer' => __('validation.search.industry_integer'),
            'industry_id.exists' => __('validation.search.industry_exists'),
            
            'company_id.integer' => __('validation.search.company_integer'),
            'company_id.exists' => __('validation.search.company_exists'),
            
            'country_id.integer' => __('validation.search.country_integer'),
            'country_id.exists' => __('validation.search.country_exists'),
            
            'state_id.integer' => __('validation.search.state_integer'),
            'state_id.exists' => __('validation.search.state_exists'),
            
            'city_id.integer' => __('validation.search.city_integer'),
            'city_id.exists' => __('validation.search.city_exists'),
            
            'salary_from.numeric' => __('validation.search.salary_from_numeric'),
            'salary_from.min' => __('validation.search.salary_from_min'),
            'salary_from.max' => __('validation.search.salary_from_max'),
            
            'salary_to.numeric' => __('validation.search.salary_to_numeric'),
            'salary_to.min' => __('validation.search.salary_to_min'),
            'salary_to.max' => __('validation.search.salary_to_max'),
            'salary_to.gte' => __('validation.search.salary_to_gte'),
            
            'experience_from.integer' => __('validation.search.experience_from_integer'),
            'experience_from.min' => __('validation.search.experience_from_min'),
            'experience_from.max' => __('validation.search.experience_from_max'),
            
            'experience_to.integer' => __('validation.search.experience_to_integer'),
            'experience_to.min' => __('validation.search.experience_to_min'),
            'experience_to.max' => __('validation.search.experience_to_max'),
            'experience_to.gte' => __('validation.search.experience_to_gte'),
            
            'sort_by.string' => __('validation.search.sort_by_string'),
            'sort_by.in' => __('validation.search.sort_by_in'),
            
            'sort_direction.string' => __('validation.search.sort_direction_string'),
            'sort_direction.in' => __('validation.search.sort_direction_in'),
            
            'per_page.integer' => __('validation.search.per_page_integer'),
            'per_page.min' => __('validation.search.per_page_min'),
            'per_page.max' => __('validation.search.per_page_max'),
            
            'page.integer' => __('validation.search.page_integer'),
            'page.min' => __('validation.search.page_min'),
            
            'posted_within.integer' => __('validation.search.posted_within_integer'),
            'posted_within.min' => __('validation.search.posted_within_min'),
            'posted_within.max' => __('validation.search.posted_within_max'),
            
            'skills.array' => __('validation.search.skills_array'),
            'skills.max' => __('validation.search.skills_max'),
            'skills.*.integer' => __('validation.search.skill_integer'),
            'skills.*.exists' => __('validation.search.skill_exists'),
            
            'tags.array' => __('validation.search.tags_array'),
            'tags.max' => __('validation.search.tags_max'),
            'tags.*.integer' => __('validation.search.tag_integer'),
            'tags.*.exists' => __('validation.search.tag_exists'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'keyword' => __('form.search.keyword'),
            'job_category_id' => __('form.search.category'),
            'job_type_id' => __('form.search.type'),
            'career_level_id' => __('form.search.career_level'),
            'functional_area_id' => __('form.search.functional_area'),
            'industry_id' => __('form.search.industry'),
            'company_id' => __('form.search.company'),
            'country_id' => __('form.search.country'),
            'state_id' => __('form.search.state'),
            'city_id' => __('form.search.city'),
            'salary_from' => __('form.search.salary_from'),
            'salary_to' => __('form.search.salary_to'),
            'salary_currency_id' => __('form.search.salary_currency'),
            'experience_from' => __('form.search.experience_from'),
            'experience_to' => __('form.search.experience_to'),
            'job_shift_id' => __('form.search.shift'),
            'degree_level_id' => __('form.search.degree_level'),
            'sort_by' => __('form.search.sort_by'),
            'sort_direction' => __('form.search.sort_direction'),
            'per_page' => __('form.search.per_page'),
            'page' => __('form.search.page'),
            'posted_within' => __('form.search.posted_within'),
            'skills' => __('form.search.skills'),
            'tags' => __('form.search.tags'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_freelance' => $this->boolean('is_freelance'),
            'is_featured' => $this->boolean('is_featured'),
            'hide_expired' => $this->boolean('hide_expired', true), // Default to true
            'remote_only' => $this->boolean('remote_only'),
            'with_salary' => $this->boolean('with_salary'),
            'per_page' => $this->filled('per_page') ? $this->integer('per_page') : 20,
            'page' => $this->filled('page') ? $this->integer('page') : 1,
            'sort_by' => $this->filled('sort_by') ? $this->string('sort_by') : 'created_at',
            'sort_direction' => $this->filled('sort_direction') ? $this->string('sort_direction') : 'desc',
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate salary range logic
            if ($this->filled(['salary_from', 'salary_to'])) {
                if ($this->salary_from >= $this->salary_to) {
                    $validator->errors()->add('salary_to', __('validation.search.salary_range_invalid'));
                }
            }

            // Validate experience range logic
            if ($this->filled(['experience_from', 'experience_to'])) {
                if ($this->experience_from >= $this->experience_to) {
                    $validator->errors()->add('experience_to', __('validation.search.experience_range_invalid'));
                }
            }

            // Validate location hierarchy
            if ($this->filled('city_id') && !$this->filled('state_id')) {
                $validator->errors()->add('state_id', __('validation.search.state_required_for_city'));
            }

            if ($this->filled('state_id') && !$this->filled('country_id')) {
                $validator->errors()->add('country_id', __('validation.search.country_required_for_state'));
            }
        });
    }

    /**
     * Get search parameters for query building.
     */
    public function getSearchParameters(): array
    {
        return [
            'keyword' => $this->string('keyword'),
            'job_category_id' => $this->integer('job_category_id'),
            'job_type_id' => $this->integer('job_type_id'),
            'career_level_id' => $this->integer('career_level_id'),
            'functional_area_id' => $this->integer('functional_area_id'),
            'industry_id' => $this->integer('industry_id'),
            'company_id' => $this->integer('company_id'),
            'country_id' => $this->integer('country_id'),
            'state_id' => $this->integer('state_id'),
            'city_id' => $this->integer('city_id'),
            'salary_from' => $this->float('salary_from'),
            'salary_to' => $this->float('salary_to'),
            'salary_currency_id' => $this->integer('salary_currency_id'),
            'experience_from' => $this->integer('experience_from'),
            'experience_to' => $this->integer('experience_to'),
            'job_shift_id' => $this->integer('job_shift_id'),
            'degree_level_id' => $this->integer('degree_level_id'),
            'is_freelance' => $this->boolean('is_freelance'),
            'is_featured' => $this->boolean('is_featured'),
            'posted_within' => $this->integer('posted_within'),
            'skills' => $this->array('skills', []),
            'tags' => $this->array('tags', []),
            'hide_expired' => $this->boolean('hide_expired'),
            'remote_only' => $this->boolean('remote_only'),
            'with_salary' => $this->boolean('with_salary'),
        ];
    }

    /**
     * Get pagination parameters.
     */
    public function getPaginationParameters(): array
    {
        return [
            'per_page' => $this->integer('per_page', 20),
            'page' => $this->integer('page', 1),
        ];
    }

    /**
     * Get sorting parameters.
     */
    public function getSortingParameters(): array
    {
        return [
            'sort_by' => $this->string('sort_by', 'created_at'),
            'sort_direction' => $this->string('sort_direction', 'desc'),
        ];
    }
} 