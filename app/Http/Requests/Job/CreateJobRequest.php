<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for Job create
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Auto-generated for Level 4 Complex System Transformation
 */
class CreateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Job::class);
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation with security
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
            // Security validation
            'g-recaptcha-response' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (config('app.recaptcha_enabled', false) && empty($value)) {
                        $fail(__('validation.recaptcha_required'));
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Context7 Pattern: Multilingual error messages
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
            'position.integer' => __('validation.job.position.integer'),
            'position.min' => __('validation.job.position.min'),
            'position.max' => __('validation.job.position.max'),
            'skills.array' => __('validation.job.skills.array'),
            'skills.*.exists' => __('validation.job.skills.exists'),
            'job_tags.array' => __('validation.job.job_tags.array'),
            'job_tags.*.string' => __('validation.job.job_tags.string'),
            'job_tags.*.max' => __('validation.job.job_tags.max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'title' => __('attributes.job.title'),
            'description' => __('attributes.job.description'),
            'company_id' => __('attributes.job.company'),
            'job_category_id' => __('attributes.job.category'),
            'job_type_id' => __('attributes.job.type'),
            'job_shift_id' => __('attributes.job.shift'),
            'career_level_id' => __('attributes.job.career_level'),
            'functional_area_id' => __('attributes.job.functional_area'),
            'salary_from' => __('attributes.job.salary_from'),
            'salary_to' => __('attributes.job.salary_to'),
            'salary_currency_id' => __('attributes.job.salary_currency'),
            'salary_period_id' => __('attributes.job.salary_period'),
            'country_id' => __('attributes.job.country'),
            'state_id' => __('attributes.job.state'),
            'city_id' => __('attributes.job.city'),
            'experience' => __('attributes.job.experience'),
            'degree_level_id' => __('attributes.job.degree_level'),
            'position' => __('attributes.job.position'),
            'expiry_date' => __('attributes.job.expiry_date'),
            'skills' => __('attributes.job.skills'),
            'job_tags' => __('attributes.job.tags'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_freelance' => $this->boolean('is_freelance', false),
            'is_suspended' => $this->boolean('is_suspended', false),
            'is_featured' => $this->boolean('is_featured', false),
            'hide_salary' => $this->boolean('hide_salary', false),
            'position' => $this->input('position', 1),
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
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check if user can create jobs for this company
            if ($this->input('company_id')) {
                $company = \App\Models\Company::find($this->input('company_id'));
                if ($company && !$this->user()->can('createJobFor', $company)) {
                    $validator->errors()->add('company_id', __('validation.job.company_unauthorized'));
                }
            }

            // Validate salary range logic
            if ($this->input('salary_from') && $this->input('salary_to')) {
                if ($this->input('salary_from') > $this->input('salary_to')) {
                    $validator->errors()->add('salary_to', __('validation.job.salary_range_invalid'));
                }
            }

            // Validate expiry date is reasonable (not more than 1 year)
            if ($this->input('expiry_date')) {
                $expiryDate = \Carbon\Carbon::parse($this->input('expiry_date'));
                if ($expiryDate->gt(now()->addYear())) {
                    $validator->errors()->add('expiry_date', __('validation.job.expiry_date_too_far'));
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling with security monitoring
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Context7 validation failed for CreateJobRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'JobController',
            'method' => 'create',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get error messages for failed authorization.
     */
    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            __('validation.job.unauthorized_creation')
        );
    }
}