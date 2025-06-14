<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Enhanced Enhanced Form Request for Job show
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Auto-generated for Level 4 Complex System Transformation
 */
class ShowJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $job = $this->route('job');
        
        // Admin can view any job
        if (Auth::user()->hasRole('Admin')) {
            return true;
        }
        
        // Employers can view their own jobs
        if (Auth::user()->hasRole('Employer')) {
            return $job->company?->user_id === Auth::id();
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Enhanced Pattern: Comprehensive validation with security
     */
    public function rules(): array
    {
        return [
            'include' => 'sometimes|array',
            'include.*' => 'string|in:company,applications,skills,tags,requirements',
            'with_statistics' => 'sometimes|boolean',
            
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
     * Enhanced Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'include.array' => __('jobs.validation.include_must_be_array'),
            'include.*.in' => __('jobs.validation.include_invalid_relation'),
            'with_statistics.boolean' => __('jobs.validation.with_statistics_boolean'),
            'name.required' => __('validation.name_required'),
            'name.max' => __('validation.name_max'),
            'email.email' => __('validation.email_invalid'),
            'email.max' => __('validation.email_max'),
            'description.max' => __('validation.description_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Enhanced Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'include' => __('jobs.attributes.include'),
            'with_statistics' => __('jobs.attributes.with_statistics'),
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'description' => __('validation.attributes.description'),
            'is_active' => __('validation.attributes.is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Enhanced Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $job = $this->route('job');
            
            // Check if job exists and is accessible
            if (!$job instanceof Job) {
                $validator->errors()->add('job', __('jobs.errors.not_found'));
                return;
            }
            
            // Check if job is deleted (soft deleted)
            if ($job->trashed()) {
                $validator->errors()->add('job', __('jobs.errors.deleted'));
                return;
            }
            
            // Employers can only view their own jobs unless job is published
            if (Auth::user()->hasRole('Employer') && 
                $job->company?->user_id !== Auth::id() && 
                $job->status !== 'open') {
                $validator->errors()->add('job', __('jobs.errors.not_accessible'));
            }
            
            if ($this->hasEnhancedValidationConflicts()) {
                $validator->errors()->add('name', __('validation.conflict_detected'));
            }
            
            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('name', __('validation.suspicious_content'));
            }
        });
    }

    /**
     * Enhanced Pattern: Enhanced business logic validation
     */
    private function hasEnhancedValidationConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }

    /**
     * Enhanced Pattern: Content security validation
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware', 'hack', 'exploit'];
        $content = strtolower(($this->name ?? '') . ' ' . ($this->description ?? ''));
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with security monitoring
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Enhanced validation failed for ShowJobRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'JobController',
            'method' => 'show',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get validated data with processed includes.
     */
    public function getProcessedData(): array
    {
        $validated = $this->validated();
        
        return [
            'includes' => $validated['include'] ?? [],
            'with_statistics' => $validated['with_statistics'] ?? false,
            'user_role' => Auth::user()->getRoleNames()->first(),
            'can_view_sensitive' => $this->canViewSensitiveData(),
        ];
    }

    /**
     * Check if user can view sensitive job data.
     */
    private function canViewSensitiveData(): bool
    {
        $job = $this->route('job');
        
        return Auth::user()->hasRole('Admin') || 
               ($job->company?->user_id === Auth::id());
    }
}