<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for Store Job
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Admin can always create jobs
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Employer must have an active company
        if ($user->hasRole('employer') && $user->company) {
            return $user->company->is_active;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive job validation with security
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => [
                'required',
                'string',
                'min:3',
                'max:180',
                'regex:/^[\p{L}\p{N}\s\-.,()&]+$/u'
            ],
            'description' => [
                'required',
                'string',
                'min:50',
                'max:10000'
            ],
            'company_id' => [
                'required_unless:is_admin,true',
                'integer',
                'exists:companies,id'
            ],
            'job_category_id' => [
                'required',
                'integer',
                'exists:job_categories,id'
            ],
            'job_type_id' => [
                'required',
                'integer',
                'exists:job_types,id'
            ],
            'career_level_id' => [
                'nullable',
                'integer',
                'exists:career_levels,id'
            ],
            'functional_area_id' => [
                'required',
                'integer',
                'exists:functional_areas,id'
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
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id'
            ],
            'state_id' => [
                'required',
                'integer',
                'exists:states,id'
            ],
            'city_id' => [
                'required',
                'integer',
                'exists:cities,id'
            ],
            'salary_from' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999',
                'lt:salary_to'
            ],
            'salary_to' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999',
                'gt:salary_from'
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:salary_currencies,id'
            ],
            'salary_period_id' => [
                'required',
                'integer',
                'exists:salary_periods,id'
            ],
            'position' => [
                'required',
                'string',
                'min:2',
                'max:255'
            ],
            'experience' => [
                'required',
                'integer',
                'min:0',
                'max:50'
            ],
            'job_expiry_date' => [
                'required',
                'date',
                'after:today',
                'before:' . now()->addMonths(12)->format('Y-m-d')
            ],
            'no_preference' => [
                'nullable',
                'integer',
                'in:0,1,2'
            ],
            'hide_salary' => [
                'nullable',
                'boolean'
            ],
            'is_freelance' => [
                'nullable',
                'boolean'
            ],
            'status' => [
                'nullable',
                'integer',
                Rule::in([
                    Job::STATUS_DRAFT,
                    Job::STATUS_OPEN,
                    Job::STATUS_PAUSED
                ])
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
            'key_responsibilities' => [
                'nullable',
                'string',
                'max:5000'
            ],
            'benefits' => [
                'nullable',
                'string',
                'max:3000'
            ],
            'requirements' => [
                'nullable',
                'string',
                'max:3000'
            ],
            'save_as_draft' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Context7 Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'job_title.required' => __('validation.required', ['attribute' => __('jobs.job_title')]),
            'job_title.min' => __('validation.min.string', ['attribute' => __('jobs.job_title'), 'min' => 3]),
            'job_title.max' => __('validation.max.string', ['attribute' => __('jobs.job_title'), 'max' => 180]),
            'job_title.regex' => __('validation.regex', ['attribute' => __('jobs.job_title')]),
            
            'description.required' => __('validation.required', ['attribute' => __('jobs.description')]),
            'description.min' => __('validation.min.string', ['attribute' => __('jobs.description'), 'min' => 50]),
            'description.max' => __('validation.max.string', ['attribute' => __('jobs.description'), 'max' => 10000]),
            
            'company_id.required_unless' => __('validation.required', ['attribute' => __('jobs.company')]),
            'company_id.exists' => __('validation.exists', ['attribute' => __('jobs.company')]),
            
            'job_category_id.required' => __('validation.required', ['attribute' => __('jobs.job_category')]),
            'job_category_id.exists' => __('validation.exists', ['attribute' => __('jobs.job_category')]),
            
            'job_type_id.required' => __('validation.required', ['attribute' => __('jobs.job_type')]),
            'job_type_id.exists' => __('validation.exists', ['attribute' => __('jobs.job_type')]),
            
            'functional_area_id.required' => __('validation.required', ['attribute' => __('jobs.functional_area')]),
            'functional_area_id.exists' => __('validation.exists', ['attribute' => __('jobs.functional_area')]),
            
            'country_id.required' => __('validation.required', ['attribute' => __('jobs.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('jobs.country')]),
            
            'state_id.required' => __('validation.required', ['attribute' => __('jobs.state')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('jobs.state')]),
            
            'city_id.required' => __('validation.required', ['attribute' => __('jobs.city')]),
            'city_id.exists' => __('validation.exists', ['attribute' => __('jobs.city')]),
            
            'salary_from.required' => __('validation.required', ['attribute' => __('jobs.salary_from')]),
            'salary_from.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_from')]),
            'salary_from.min' => __('validation.min.numeric', ['attribute' => __('jobs.salary_from'), 'min' => 0]),
            'salary_from.lt' => __('validation.lt.numeric', ['attribute' => __('jobs.salary_from'), 'value' => __('jobs.salary_to')]),
            
            'salary_to.required' => __('validation.required', ['attribute' => __('jobs.salary_to')]),
            'salary_to.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_to')]),
            'salary_to.gt' => __('validation.gt.numeric', ['attribute' => __('jobs.salary_to'), 'value' => __('jobs.salary_from')]),
            
            'currency_id.required' => __('validation.required', ['attribute' => __('jobs.currency')]),
            'currency_id.exists' => __('validation.exists', ['attribute' => __('jobs.currency')]),
            
            'salary_period_id.required' => __('validation.required', ['attribute' => __('jobs.salary_period')]),
            'salary_period_id.exists' => __('validation.exists', ['attribute' => __('jobs.salary_period')]),
            
            'position.required' => __('validation.required', ['attribute' => __('jobs.position')]),
            'position.min' => __('validation.min.string', ['attribute' => __('jobs.position'), 'min' => 2]),
            'position.max' => __('validation.max.string', ['attribute' => __('jobs.position'), 'max' => 255]),
            
            'experience.required' => __('validation.required', ['attribute' => __('jobs.experience')]),
            'experience.integer' => __('validation.integer', ['attribute' => __('jobs.experience')]),
            'experience.min' => __('validation.min.numeric', ['attribute' => __('jobs.experience'), 'min' => 0]),
            'experience.max' => __('validation.max.numeric', ['attribute' => __('jobs.experience'), 'max' => 50]),
            
            'job_expiry_date.required' => __('validation.required', ['attribute' => __('jobs.job_expiry_date')]),
            'job_expiry_date.date' => __('validation.date', ['attribute' => __('jobs.job_expiry_date')]),
            'job_expiry_date.after' => __('validation.after', ['attribute' => __('jobs.job_expiry_date'), 'date' => 'today']),
            'job_expiry_date.before' => __('validation.before', ['attribute' => __('jobs.job_expiry_date'), 'date' => now()->addMonths(12)->format('Y-m-d')]),
            
            'skills.array' => __('validation.array', ['attribute' => __('jobs.skills')]),
            'skills.max' => __('validation.max.array', ['attribute' => __('jobs.skills'), 'max' => 20]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('jobs.skill')]),
            
            'tags.array' => __('validation.array', ['attribute' => __('jobs.tags')]),
            'tags.max' => __('validation.max.array', ['attribute' => __('jobs.tags'), 'max' => 10]),
            'tags.*.exists' => __('validation.exists', ['attribute' => __('jobs.tag')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'job_title' => __('jobs.job_title'),
            'description' => __('jobs.description'),
            'company_id' => __('jobs.company'),
            'job_category_id' => __('jobs.job_category'),
            'job_type_id' => __('jobs.job_type'),
            'career_level_id' => __('jobs.career_level'),
            'functional_area_id' => __('jobs.functional_area'),
            'job_shift_id' => __('jobs.job_shift'),
            'degree_level_id' => __('jobs.degree_level'),
            'country_id' => __('jobs.country'),
            'state_id' => __('jobs.state'),
            'city_id' => __('jobs.city'),
            'salary_from' => __('jobs.salary_from'),
            'salary_to' => __('jobs.salary_to'),
            'currency_id' => __('jobs.currency'),
            'salary_period_id' => __('jobs.salary_period'),
            'position' => __('jobs.position'),
            'experience' => __('jobs.experience'),
            'job_expiry_date' => __('jobs.job_expiry_date'),
            'skills' => __('jobs.skills'),
            'tags' => __('jobs.tags'),
            'key_responsibilities' => __('jobs.key_responsibilities'),
            'benefits' => __('jobs.benefits'),
            'requirements' => __('jobs.requirements'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        // Auto-assign company for employers
        if (auth()->user()->hasRole('employer') && auth()->user()->company) {
            $this->merge([
                'company_id' => auth()->user()->company->id,
                'is_admin' => false
            ]);
        } elseif (auth()->user()->hasRole('admin')) {
            $this->merge([
                'is_admin' => true
            ]);
        }

        // Set default status
        if (!$this->has('status')) {
            $this->merge([
                'status' => $this->has('save_as_draft') && $this->save_as_draft ? 
                           Job::STATUS_DRAFT : Job::STATUS_OPEN
            ]);
        }
    }

    /**
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check job creation limits for live jobs
            if ($this->status == Job::STATUS_OPEN && !$this->checkJobCreationLimit()) {
                $validator->errors()->add('status', __('jobs.job_creation_limit_exceeded'));
            }

            // Validate salary range makes sense
            if ($this->salary_from && $this->salary_to) {
                $salaryDifference = $this->salary_to - $this->salary_from;
                $minimumDifference = $this->salary_from * 0.1; // 10% minimum difference
                
                if ($salaryDifference < $minimumDifference) {
                    $validator->errors()->add('salary_to', __('jobs.salary_range_too_small'));
                }
            }

            // Validate company ownership for non-admin users
            if (!auth()->user()->hasRole('admin') && $this->company_id) {
                $company = Company::find($this->company_id);
                if (!$company || $company->user_id !== auth()->id()) {
                    $validator->errors()->add('company_id', __('jobs.unauthorized_company'));
                }
            }

            // Validate location hierarchy
            $this->validateLocationHierarchy($validator);
        });
    }

    /**
     * Check if user can create more jobs.
     */
    protected function checkJobCreationLimit(): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Get active subscription
        $subscription = $user->subscriptions()->active()->first();
        if (!$subscription) {
            return false;
        }
        
        // Check job limit
        $currentJobCount = Job::where('company_id', $user->company->id)
            ->where('status', Job::STATUS_OPEN)
            ->count();
            
        return $currentJobCount < $subscription->plan->job_limit;
    }

    /**
     * Validate location hierarchy (country -> state -> city).
     */
    protected function validateLocationHierarchy($validator): void
    {
        if ($this->state_id && $this->country_id) {
            $state = \App\Models\State::find($this->state_id);
            if (!$state || $state->country_id != $this->country_id) {
                $validator->errors()->add('state_id', __('jobs.invalid_state_for_country'));
            }
        }

        if ($this->city_id && $this->state_id) {
            $city = \App\Models\City::find($this->city_id);
            if (!$city || $city->state_id != $this->state_id) {
                $validator->errors()->add('city_id', __('jobs.invalid_city_for_state'));
            }
        }
    }

    /**
     * Get validated data with defaults.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        // Set default values
        $validated['hide_salary'] = $validated['hide_salary'] ?? false;
        $validated['is_freelance'] = $validated['is_freelance'] ?? false;
        $validated['no_preference'] = $validated['no_preference'] ?? 2; // Both
        
        return $validated;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling with security monitoring
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Context7 validation failed for StoreJobRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'Job',
            'action' => 'Store',
            'job_title' => $this->job_title,
            'company_id' => $this->company_id,
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'suspicious_patterns' => $this->hasSuspiciousContent(),
            'invalid_location' => $this->hasInvalidLocationCombination(),
            'invalid_salary' => $this->hasInvalidSalaryRange(),
            'invalid_expiry' => $this->hasInvalidExpiryDate(),
        ]);

        parent::failedValidation($validator);
    }
}
