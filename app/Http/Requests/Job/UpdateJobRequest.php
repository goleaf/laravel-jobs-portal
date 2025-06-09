<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use App\Models\Company;
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
        $job = $this->route('job');
        $user = auth()->user();
        
        // Admin can always update jobs
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Employer can only update own company's jobs
        if ($user->hasRole('employer') && $user->company) {
            return $job->company_id === $user->company->id && $user->company->is_active;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $job = $this->route('job');
        
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
                'required',
                'integer',
                Rule::in([
                    Job::STATUS_DRAFT,
                    Job::STATUS_OPEN,
                    Job::STATUS_PAUSED,
                    Job::STATUS_CLOSED
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
     * Get custom validation messages with multilingual support.
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
            
            'status.required' => __('validation.required', ['attribute' => __('jobs.status')]),
            'status.in' => __('validation.in', ['attribute' => __('jobs.status')]),
            
            'skills.array' => __('validation.array', ['attribute' => __('jobs.skills')]),
            'skills.max' => __('validation.max.array', ['attribute' => __('jobs.skills'), 'max' => 20]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('jobs.skill')]),
            
            'tags.array' => __('validation.array', ['attribute' => __('jobs.tags')]),
            'tags.max' => __('validation.max.array', ['attribute' => __('jobs.tags'), 'max' => 10]),
            'tags.*.exists' => __('validation.exists', ['attribute' => __('jobs.tag')]),
        ];
    }

    /**
     * Get custom attribute names for multilingual support.
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
            'status' => __('jobs.status'),
            'skills' => __('jobs.skills'),
            'tags' => __('jobs.tags'),
            'key_responsibilities' => __('jobs.key_responsibilities'),
            'benefits' => __('jobs.benefits'),
            'requirements' => __('jobs.requirements'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $job = $this->route('job');
        
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

        // Set default status based on current job status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => $this->has('save_as_draft') && $this->save_as_draft ? 
                           Job::STATUS_DRAFT : ($job->status ?? Job::STATUS_OPEN)
            ]);
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
            'title' => $this->input('job_title'),
            'ip' => $this->ip(),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $job = $this->route('job');
            
            // Check if job can be updated (not closed permanently)
            if ($job->status === Job::STATUS_CLOSED && !auth()->user()->hasRole('admin')) {
                $validator->errors()->add('status', __('jobs.cannot_update_closed_job'));
            }
            
            // Check job creation limits for status changes to live
            if ($this->status == Job::STATUS_OPEN && 
                $job->status != Job::STATUS_OPEN && 
                !$this->checkJobCreationLimit()) {
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
            
            // Check if changing to closed status is allowed
            if ($this->status == Job::STATUS_CLOSED) {
                $this->validateJobClosure($validator, $job);
            }
        });
    }

    /**
     * Check if user can create more jobs (for status changes).
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
        
        // Check job limit (excluding current job)
        $currentJobCount = Job::where('company_id', $user->company->id)
            ->where('status', Job::STATUS_OPEN)
            ->where('id', '!=', $this->route('job')->id)
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
     * Validate job closure constraints.
     */
    protected function validateJobClosure($validator, $job): void
    {
        // Check if there are pending applications
        $pendingApplications = $job->appliedJobs()
            ->whereIn('status', [
                \App\Models\JobApplication::STATUS_APPLIED,
                \App\Models\JobApplication::STATUS_INTERVIEW
            ])
            ->count();

        if ($pendingApplications > 0 && !auth()->user()->hasRole('admin')) {
            $validator->errors()->add('status', __('jobs.cannot_close_with_pending_applications', [
                'count' => $pendingApplications
            ]));
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
     * Get changed fields for audit logging.
     */
    public function getChangedFields(): array
    {
        $job = $this->route('job');
        $validated = $this->validated();
        $changed = [];

        foreach ($validated as $key => $value) {
            if ($job->$key != $value) {
                $changed[$key] = [
                    'old' => $job->$key,
                    'new' => $value
                ];
            }
        }

        return $changed;
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
