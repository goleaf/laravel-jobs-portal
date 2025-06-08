<?php

namespace App\Http\Requests\Job;

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
        // Context7 Pattern: Role-based authorization
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
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
            // Job Basic Information
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65000'],
            'benefits' => ['nullable', 'string', 'max:65000'],
            'requirements' => ['nullable', 'string', 'max:65000'],
            
            // Job Classification
            'job_category_id' => ['required', 'integer', 'exists:job_categories,id'],
            'job_type_id' => ['required', 'integer', 'exists:job_types,id'],
            'job_shift_id' => ['required', 'integer', 'exists:job_shifts,id'],
            'career_level_id' => ['required', 'integer', 'exists:career_levels,id'],
            'functional_area_id' => ['required', 'integer', 'exists:functional_areas,id'],
            
            // Company Information
            'company_id' => [
                'required', 
                'integer', 
                'exists:companies,id',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    if ($user->hasRole('Employer')) {
                        $company = \App\Models\Company::find($value);
                        if (!$company || $company->user_id !== $user->id) {
                            $fail(__('validation.unauthorized_company'));
                        }
                    }
                },
            ],
            
            // Salary Information
            'salary_from' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'salary_to' => [
                'nullable', 
                'numeric', 
                'min:0', 
                'max:999999999',
                'gte:salary_from'
            ],
            'salary_period_id' => ['required', 'integer', 'exists:salary_periods,id'],
            'salary_currency_id' => ['required', 'integer', 'exists:salary_currencies,id'],
            'hide_salary' => ['boolean'],
            
            // Location Information
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'is_freelance' => ['boolean'],
            'is_suspended' => ['boolean'],
            
            // Experience and Education
            'experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            'degree_level_id' => ['required', 'integer', 'exists:required_degree_levels,id'],
            
            // Job Details
            'position' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'job_expiry_date' => [
                'required', 
                'date', 
                'after:today',
                'before:' . now()->addYear()->format('Y-m-d')
            ],
            
            // Skills (array of skill IDs)
            'job_skill' => ['nullable', 'array'],
            'job_skill.*' => ['integer', 'exists:skills,id'],
            
            // Tags (array of tag IDs)  
            'job_tag' => ['nullable', 'array'],
            'job_tag.*' => ['integer', 'exists:job_tags,id'],
            
            // Status and Features
            'status' => ['required', 'boolean'],
            'is_featured' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && !auth()->user()->hasRole('Admin')) {
                        $fail(__('validation.admin_only_field'));
                    }
                },
            ],
            
            // Security
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
            'title.required' => __('validation.job_title_required'),
            'title.max' => __('validation.job_title_max'),
            'description.required' => __('validation.job_description_required'),
            'description.max' => __('validation.job_description_max'),
            'job_category_id.required' => __('validation.job_category_required'),
            'job_category_id.exists' => __('validation.job_category_exists'),
            'job_type_id.required' => __('validation.job_type_required'),
            'job_type_id.exists' => __('validation.job_type_exists'),
            'job_shift_id.required' => __('validation.job_shift_required'),
            'job_shift_id.exists' => __('validation.job_shift_exists'),
            'career_level_id.required' => __('validation.career_level_required'),
            'career_level_id.exists' => __('validation.career_level_exists'),
            'functional_area_id.required' => __('validation.functional_area_required'),
            'functional_area_id.exists' => __('validation.functional_area_exists'),
            'company_id.required' => __('validation.company_required'),
            'company_id.exists' => __('validation.company_exists'),
            'salary_from.numeric' => __('validation.salary_from_numeric'),
            'salary_from.min' => __('validation.salary_from_min'),
            'salary_from.max' => __('validation.salary_from_max'),
            'salary_to.numeric' => __('validation.salary_to_numeric'),
            'salary_to.gte' => __('validation.salary_to_gte'),
            'salary_period_id.required' => __('validation.salary_period_required'),
            'salary_period_id.exists' => __('validation.salary_period_exists'),
            'salary_currency_id.required' => __('validation.salary_currency_required'),
            'salary_currency_id.exists' => __('validation.salary_currency_exists'),
            'country_id.required' => __('validation.country_required'),
            'country_id.exists' => __('validation.country_exists'),
            'state_id.exists' => __('validation.state_exists'),
            'city_id.exists' => __('validation.city_exists'),
            'experience.integer' => __('validation.experience_integer'),
            'experience.min' => __('validation.experience_min'),
            'experience.max' => __('validation.experience_max'),
            'degree_level_id.required' => __('validation.degree_level_required'),
            'degree_level_id.exists' => __('validation.degree_level_exists'),
            'position.integer' => __('validation.position_integer'),
            'position.min' => __('validation.position_min'),
            'position.max' => __('validation.position_max'),
            'job_expiry_date.required' => __('validation.job_expiry_required'),
            'job_expiry_date.date' => __('validation.job_expiry_date'),
            'job_expiry_date.after' => __('validation.job_expiry_future'),
            'job_expiry_date.before' => __('validation.job_expiry_within_year'),
            'job_skill.array' => __('validation.job_skills_array'),
            'job_skill.*.exists' => __('validation.job_skill_exists'),
            'job_tag.array' => __('validation.job_tags_array'),
            'job_tag.*.exists' => __('validation.job_tag_exists'),
            'status.required' => __('validation.status_required'),
            'status.boolean' => __('validation.status_boolean'),
            'is_featured.admin_only' => __('validation.admin_only_field'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.job_title'),
            'description' => __('validation.attributes.job_description'),
            'benefits' => __('validation.attributes.job_benefits'),
            'requirements' => __('validation.attributes.job_requirements'),
            'job_category_id' => __('validation.attributes.job_category'),
            'job_type_id' => __('validation.attributes.job_type'),
            'job_shift_id' => __('validation.attributes.job_shift'),
            'career_level_id' => __('validation.attributes.career_level'),
            'functional_area_id' => __('validation.attributes.functional_area'),
            'company_id' => __('validation.attributes.company'),
            'salary_from' => __('validation.attributes.salary_from'),
            'salary_to' => __('validation.attributes.salary_to'),
            'salary_period_id' => __('validation.attributes.salary_period'),
            'salary_currency_id' => __('validation.attributes.salary_currency'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'experience' => __('validation.attributes.experience_years'),
            'degree_level_id' => __('validation.attributes.degree_level'),
            'position' => __('validation.attributes.positions_available'),
            'job_expiry_date' => __('validation.attributes.job_expiry_date'),
            'job_skill' => __('validation.attributes.job_skills'),
            'job_tag' => __('validation.attributes.job_tags'),
            'status' => __('validation.attributes.status'),
            'is_featured' => __('validation.attributes.featured_status'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim($this->title ?? ''),
            'description' => trim($this->description ?? ''),
            'benefits' => trim($this->benefits ?? ''),
            'requirements' => trim($this->requirements ?? ''),
            'salary_from' => $this->salary_from ? (float) $this->salary_from : null,
            'salary_to' => $this->salary_to ? (float) $this->salary_to : null,
            'experience' => $this->experience ? (int) $this->experience : null,
            'position' => $this->position ? (int) $this->position : 1,
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_freelance' => filter_var($this->is_freelance, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_suspended' => filter_var($this->is_suspended, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'hide_salary' => filter_var($this->hide_salary, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            
            // Ensure arrays are properly formatted
            'job_skill' => is_array($this->job_skill) ? array_filter($this->job_skill) : [],
            'job_tag' => is_array($this->job_tag) ? array_filter($this->job_tag) : [],
        ]);
    }

    /**
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasContext7ValidationConflicts()) {
                $validator->errors()->add('title', __('validation.job_conflict'));
            }
            
            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('description', __('validation.suspicious_content'));
            }
            
            if ($this->hasInvalidLocationCombination()) {
                $validator->errors()->add('location', __('validation.invalid_location_combination'));
            }

            if ($this->hasInvalidSalaryRange()) {
                $validator->errors()->add('salary', __('validation.invalid_salary_range'));
            }

            if ($this->hasInvalidExpiryDate()) {
                $validator->errors()->add('job_expiry_date', __('validation.invalid_expiry_date'));
            }
        });
    }

    /**
     * Context7 Pattern: Enhanced business logic validation
     */
    private function hasContext7ValidationConflicts(): bool
    {
        // Check for duplicate job titles in same company
        if ($this->title && $this->company_id) {
            $duplicateJob = \App\Models\Job::where('title', 'LIKE', '%' . $this->title . '%')
                ->where('company_id', $this->company_id)
                ->where('status', 1)
                ->exists();
                
            return $duplicateJob;
        }
        
        return false;
    }

    /**
     * Context7 Pattern: Content security validation
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = [
            'pyramid scheme', 'get rich quick', 'guaranteed income',
            'work from home easy money', 'no experience required high salary',
            'spam', 'scam', 'virus', 'malware'
        ];
        
        $content = strtolower($this->description . ' ' . $this->benefits . ' ' . $this->requirements);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Context7 Pattern: Location validation
     */
    private function hasInvalidLocationCombination(): bool
    {
        // Validate state belongs to country
        if ($this->country_id && $this->state_id) {
            $stateExists = \App\Models\State::where('id', $this->state_id)
                ->where('country_id', $this->country_id)
                ->exists();
            
            if (!$stateExists) return true;
        }

        // Validate city belongs to state
        if ($this->state_id && $this->city_id) {
            $cityExists = \App\Models\City::where('id', $this->city_id)
                ->where('state_id', $this->state_id)
                ->exists();
            
            if (!$cityExists) return true;
        }

        return false;
    }

    /**
     * Context7 Pattern: Salary validation
     */
    private function hasInvalidSalaryRange(): bool
    {
        if ($this->salary_from && $this->salary_to) {
            // Salary range should not be too wide (more than 10x)
            if ($this->salary_to > ($this->salary_from * 10)) {
                return true;
            }
            
            // Minimum salary should not be unrealistically low
            if ($this->salary_from < 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * Context7 Pattern: Expiry date validation
     */
    private function hasInvalidExpiryDate(): bool
    {
        if ($this->job_expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($this->job_expiry_date);
            $now = \Carbon\Carbon::now();
            
            // Must be at least 7 days from now
            if ($expiryDate->diffInDays($now) < 7) {
                return true;
            }
            
            // Must not be more than 1 year from now
            if ($expiryDate->diffInDays($now) > 365) {
                return true;
            }
        }

        return false;
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
            'job_title' => $this->title,
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
