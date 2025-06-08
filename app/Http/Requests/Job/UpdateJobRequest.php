<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NoMaliciousContent;

/**
 * Class UpdateJobRequest
 * 
 * Comprehensive request validation for job updates with multilanguage support
 */
class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('job'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'company_id' => 'required|integer|exists:companies,id',
            'job_category_id' => 'required|integer|exists:job_categories,id',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'job_shift_id' => 'nullable|integer|exists:job_shifts,id',
            'career_level_id' => 'nullable|integer|exists:career_levels,id',
            'functional_area_id' => 'nullable|integer|exists:functional_areas,id',
            'salary_from' => 'nullable|numeric|min:0|max:999999999.99',
            'salary_to' => 'nullable|numeric|min:0|max:999999999.99|gte:salary_from',
            'salary_currency_id' => 'nullable|integer|exists:salary_currencies,id',
            'salary_period_id' => 'nullable|integer|exists:salary_periods,id',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'is_freelance' => 'sometimes|boolean',
            'is_suspended' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'hide_salary' => 'sometimes|boolean',
            'experience' => 'nullable|string|max:255',
            'degree_level_id' => 'nullable|integer|exists:required_degree_levels,id',
            'position' => 'nullable|integer|min:1|max:999',
            'expiry_date' => 'nullable|date|after:today',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:skills,id',
            'job_tags' => 'nullable|array',
            'job_tags.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom validation messages with multilanguage support.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.job.title.required'),
            'title.string' => __('validation.job.title.string'),
            'title.max' => __('validation.job.title.max'),
            'description.required' => __('validation.job.description.required'),
            'description.string' => __('validation.job.description.string'),
            'description.min' => __('validation.job.description.min'),
            'company_id.required' => __('validation.job.company_id.required'),
            'company_id.exists' => __('validation.job.company_id.exists'),
            'job_category_id.required' => __('validation.job.job_category_id.required'),
            'job_category_id.exists' => __('validation.job.job_category_id.exists'),
            'job_type_id.required' => __('validation.job.job_type_id.required'),
            'job_type_id.exists' => __('validation.job.job_type_id.exists'),
            'salary_from.numeric' => __('validation.job.salary_from.numeric'),
            'salary_from.min' => __('validation.job.salary_from.min'),
            'salary_from.max' => __('validation.job.salary_from.max'),
            'salary_to.numeric' => __('validation.job.salary_to.numeric'),
            'salary_to.gte' => __('validation.job.salary_to.gte'),
            'country_id.required' => __('validation.job.country_id.required'),
            'country_id.exists' => __('validation.job.country_id.exists'),
            'expiry_date.date' => __('validation.job.expiry_date.date'),
            'expiry_date.after' => __('validation.job.expiry_date.after'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('attributes.job.title'),
            'description' => __('attributes.job.description'),
            'company_id' => __('attributes.job.company'),
            'job_category_id' => __('attributes.job.category'),
            'job_type_id' => __('attributes.job.type'),
            'salary_from' => __('attributes.job.salary_from'),
            'salary_to' => __('attributes.job.salary_to'),
            'country_id' => __('attributes.job.country'),
            'expiry_date' => __('attributes.job.expiry_date'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_freelance' => $this->boolean('is_freelance'),
            'is_suspended' => $this->boolean('is_suspended'),
            'is_featured' => $this->boolean('is_featured'),
            'hide_salary' => $this->boolean('hide_salary'),
        ]);

        // Clean salary values
        if ($this->has('salary_from')) {
            $this->merge(['salary_from' => str_replace(',', '', $this->input('salary_from'))]);
        }
        if ($this->has('salary_to')) {
            $this->merge(['salary_to' => str_replace(',', '', $this->input('salary_to'))]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log job update attempt for security
        \Log::info('Job update attempted', [
            'user_id' => $this->user()->id,
            'job_id' => $this->route('job')->id,
            'title' => $this->input('title'),
            'ip' => $this->ip(),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if user can update jobs for this company
            if ($this->input('company_id')) {
                $company = \App\Models\Company::find($this->input('company_id'));
                if ($company && !$this->user()->can('updateJobFor', $company)) {
                    $validator->errors()->add('company_id', __('validation.job.company_unauthorized'));
                }
            }

            // Validate salary range logic
            if ($this->input('salary_from') && $this->input('salary_to')) {
                if ($this->input('salary_from') > $this->input('salary_to')) {
                    $validator->errors()->add('salary_to', __('validation.job.salary_range_invalid'));
                }
            }
        });
    }

    /**
     * Get error messages for failed authorization.
     */
    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            __('validation.job.unauthorized_update')
        );
    }
}
