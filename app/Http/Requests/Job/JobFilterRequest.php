<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Job;

class JobFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public job filtering
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:job_categories,id'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'functional_area_id' => ['nullable', 'exists:functional_areas,id'],
            'career_level_id' => ['nullable', 'exists:career_levels,id'],
            'degree_level_id' => ['nullable', 'exists:required_degree_levels,id'],
            'job_shift_id' => ['nullable', 'exists:job_shifts,id'],
            'currency_id' => ['nullable', 'exists:salary_currencies,id'],
            'salary_period_id' => ['nullable', 'exists:salary_periods,id'],
            'min_salary' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'max_salary' => ['nullable', 'numeric', 'min:0', 'max:999999999', 'gte:min_salary'],
            'min_experience' => ['nullable', 'integer', 'min:0', 'max:255'],
            'max_experience' => ['nullable', 'integer', 'min:0', 'max:255', 'gte:min_experience'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'posted_within' => ['nullable', 'integer', 'in:1,7,14,30,90'], // Days
            'is_featured' => ['nullable', 'boolean'],
            'is_freelance' => ['nullable', 'boolean'],
            'hide_salary' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', Rule::in([
                'relevance', 'date_desc', 'date_asc', 'salary_desc', 'salary_asc', 
                'company_name', 'location', 'popularity'
            ])],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'job category',
            'job_type_id' => 'job type',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'company_id' => 'company',
            'functional_area_id' => 'functional area',
            'career_level_id' => 'career level',
            'degree_level_id' => 'degree level',
            'job_shift_id' => 'job shift',
            'currency_id' => 'currency',
            'salary_period_id' => 'salary period',
            'min_salary' => 'minimum salary',
            'max_salary' => 'maximum salary',
            'min_experience' => 'minimum experience',
            'max_experience' => 'maximum experience',
            'posted_within' => 'posted within',
            'is_featured' => 'featured jobs only',
            'is_freelance' => 'freelance jobs only',
            'hide_salary' => 'hide salary',
            'sort_by' => 'sort by',
            'per_page' => 'results per page',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Search term cannot be longer than 255 characters.',
            'max_salary.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'max_experience.gte' => 'Maximum experience must be greater than or equal to minimum experience.',
            'posted_within.in' => 'Posted within must be 1, 7, 14, 30, or 90 days.',
            'sort_by.in' => 'Invalid sort option selected.',
            'per_page.min' => 'Results per page must be at least 10.',
            'per_page.max' => 'Results per page cannot exceed 100.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for location hierarchy
            if ($this->filled('state_id') && $this->filled('country_id')) {
                $stateExists = \App\Models\State::where('id', $this->input('state_id'))
                    ->where('country_id', $this->input('country_id'))
                    ->exists();
                
                if (!$stateExists) {
                    $validator->errors()->add('state_id', 'The selected state does not belong to the selected country.');
                }
            }

            if ($this->filled('city_id') && $this->filled('state_id')) {
                $cityExists = \App\Models\City::where('id', $this->input('city_id'))
                    ->where('state_id', $this->input('state_id'))
                    ->exists();
                
                if (!$cityExists) {
                    $validator->errors()->add('city_id', 'The selected city does not belong to the selected state.');
                }
            }

            // Validate skills array size
            if ($this->filled('skills') && count($this->input('skills', [])) > 20) {
                $validator->errors()->add('skills', 'You can select maximum 20 skills.');
            }

            // Validate tags array size
            if ($this->filled('tags') && count($this->input('tags', [])) > 10) {
                $validator->errors()->add('tags', 'You can select maximum 10 tags.');
            }
        });
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        // Set default values
        $this->merge([
            'sort_by' => $this->input('sort_by', 'relevance'),
            'per_page' => $this->input('per_page', 20),
            'page' => $this->input('page', 1),
            'posted_within' => $this->input('posted_within', 30), // Default to last 30 days
        ]);

        // Convert boolean inputs properly
        if ($this->has('is_featured')) {
            $this->merge(['is_featured' => $this->boolean('is_featured')]);
        }

        if ($this->has('is_freelance')) {
            $this->merge(['is_freelance' => $this->boolean('is_freelance')]);
        }

        if ($this->has('hide_salary')) {
            $this->merge(['hide_salary' => $this->boolean('hide_salary')]);
        }
    }

    /**
     * Get search filters as array for easy use in controllers.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'search' => $this->input('search'),
            'category_id' => $this->input('category_id'),
            'job_type_id' => $this->input('job_type_id'),
            'location' => [
                'country_id' => $this->input('country_id'),
                'state_id' => $this->input('state_id'),
                'city_id' => $this->input('city_id'),
            ],
            'company_id' => $this->input('company_id'),
            'functional_area_id' => $this->input('functional_area_id'),
            'career_level_id' => $this->input('career_level_id'),
            'degree_level_id' => $this->input('degree_level_id'),
            'job_shift_id' => $this->input('job_shift_id'),
            'salary' => [
                'currency_id' => $this->input('currency_id'),
                'period_id' => $this->input('salary_period_id'),
                'min' => $this->input('min_salary'),
                'max' => $this->input('max_salary'),
            ],
            'experience' => [
                'min' => $this->input('min_experience'),
                'max' => $this->input('max_experience'),
            ],
            'skills' => $this->input('skills', []),
            'tags' => $this->input('tags', []),
            'options' => [
                'posted_within' => $this->input('posted_within'),
                'is_featured' => $this->boolean('is_featured'),
                'is_freelance' => $this->boolean('is_freelance'),
                'hide_salary' => $this->boolean('hide_salary'),
            ],
            'sort_by' => $this->input('sort_by'),
            'pagination' => [
                'per_page' => $this->input('per_page'),
                'page' => $this->input('page'),
            ],
        ];
    }
    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }
} 