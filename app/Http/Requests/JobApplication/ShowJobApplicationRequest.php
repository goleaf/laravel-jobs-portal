<?php

namespace App\Http\Requests\JobApplication;

use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $applicationId = $this->route('jobApplication') ?? $this->route('id');
        
        if (!$user || !$applicationId) {
            return false;
        }
        
        // Admin can view any application
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Find the application
        $application = JobApplication::find($applicationId);
        if (!$application) {
            return false;
        }
        
        // Candidate can view their own applications
        if ($user->hasRole('candidate') && $application->candidate_id === $user->id) {
            return true;
        }
        
        // Employer can view applications for their jobs
        if ($user->hasRole('employer')) {
            $userCompanyIds = $user->companies()->pluck('id')->toArray();
            $jobCompanyId = $application->job->company_id ?? null;
            
            if ($jobCompanyId && in_array($jobCompanyId, $userCompanyIds)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Include relationships
            'include' => ['sometimes', 'array'],
            'include.*' => ['string', Rule::in([
                'candidate',
                'candidate.profile',
                'candidate.resumes',
                'candidate.skills',
                'candidate.experiences',
                'candidate.educations',
                'job',
                'job.company',
                'job.category',
                'job.type',
                'job.skills',
                'resume',
                'skills',
                'notes',
                'interviews',
                'documents',
                'activity_logs'
            ])],
            
            // Response format options
            'format' => ['sometimes', 'string', Rule::in(['json', 'pdf', 'html'])],
            'template' => ['sometimes', 'string', Rule::in(['detailed', 'summary', 'print'])],
            
            // Privacy options
            'include_private' => ['sometimes', 'boolean'],
            'include_contact_info' => ['sometimes', 'boolean'],
            'include_salary_info' => ['sometimes', 'boolean'],
            
            // Tracking options
            'track_view' => ['sometimes', 'boolean'],
            'mark_as_viewed' => ['sometimes', 'boolean'],
            
            // Language and localization
            'locale' => ['sometimes', 'string', 'size:2', Rule::in(['en', 'ar', 'es', 'fr', 'de', 'pt', 'ru', 'tr', 'zh'])],
            'timezone' => ['sometimes', 'string', 'max:50'],
            
            // Export options
            'export_fields' => ['sometimes', 'array'],
            'export_fields.*' => ['string', Rule::in([
                'basic_info', 'contact_info', 'experience', 'education', 'skills',
                'resume', 'cover_letter', 'salary_expectations', 'availability',
                'application_details', 'status_history', 'notes', 'documents'
            ])],
            
            // Comparison options
            'compare_with' => ['sometimes', 'array'],
            'compare_with.*' => ['integer', 'exists:job_applications,id'],
            'comparison_fields' => ['sometimes', 'array'],
            'comparison_fields.*' => ['string'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'include.array' => __('validation.include_must_be_array'),
            'include.*.string' => __('validation.include_item_must_be_string'),
            'include.*.in' => __('validation.invalid_include_relationship'),
            
            'format.string' => __('validation.format_must_be_string'),
            'format.in' => __('validation.invalid_format'),
            
            'template.string' => __('validation.template_must_be_string'),
            'template.in' => __('validation.invalid_template'),
            
            'include_private.boolean' => __('validation.include_private_must_be_boolean'),
            'include_contact_info.boolean' => __('validation.include_contact_info_must_be_boolean'),
            'include_salary_info.boolean' => __('validation.include_salary_info_must_be_boolean'),
            
            'track_view.boolean' => __('validation.track_view_must_be_boolean'),
            'mark_as_viewed.boolean' => __('validation.mark_as_viewed_must_be_boolean'),
            
            'locale.string' => __('validation.locale_must_be_string'),
            'locale.size' => __('validation.locale_invalid_length'),
            'locale.in' => __('validation.locale_not_supported'),
            
            'timezone.string' => __('validation.timezone_must_be_string'),
            'timezone.max' => __('validation.timezone_too_long'),
            
            'export_fields.array' => __('validation.export_fields_must_be_array'),
            'export_fields.*.string' => __('validation.export_field_must_be_string'),
            'export_fields.*.in' => __('validation.invalid_export_field'),
            
            'compare_with.array' => __('validation.compare_with_must_be_array'),
            'compare_with.*.integer' => __('validation.compare_with_item_must_be_integer'),
            'compare_with.*.exists' => __('validation.compare_application_not_found'),
            
            'comparison_fields.array' => __('validation.comparison_fields_must_be_array'),
            'comparison_fields.*.string' => __('validation.comparison_field_must_be_string'),
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
            'include' => __('attributes.include_relationships'),
            'format' => __('attributes.response_format'),
            'template' => __('attributes.template'),
            'include_private' => __('attributes.include_private_info'),
            'include_contact_info' => __('attributes.include_contact_info'),
            'include_salary_info' => __('attributes.include_salary_info'),
            'track_view' => __('attributes.track_view'),
            'mark_as_viewed' => __('attributes.mark_as_viewed'),
            'locale' => __('attributes.locale'),
            'timezone' => __('attributes.timezone'),
            'export_fields' => __('attributes.export_fields'),
            'compare_with' => __('attributes.compare_with'),
            'comparison_fields' => __('attributes.comparison_fields'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $defaults = [
            'format' => 'json',
            'template' => 'detailed',
            'include_private' => false,
            'include_contact_info' => true,
            'include_salary_info' => true,
            'track_view' => true,
            'mark_as_viewed' => false,
            'locale' => app()->getLocale(),
            'timezone' => $this->user()?->timezone ?? config('app.timezone'),
        ];

        foreach ($defaults as $key => $value) {
            if (!$this->has($key)) {
                $this->merge([$key => $value]);
            }
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'include_private', 'include_contact_info', 'include_salary_info',
            'track_view', 'mark_as_viewed'
        ];
        
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Handle comma-separated include relationships
        if ($this->has('include') && is_string($this->input('include'))) {
            $this->merge([
                'include' => array_filter(explode(',', $this->input('include'))),
            ]);
        }

        // Handle comma-separated export fields
        if ($this->has('export_fields') && is_string($this->input('export_fields'))) {
            $this->merge([
                'export_fields' => array_filter(explode(',', $this->input('export_fields'))),
            ]);
        }

        // Handle comma-separated comparison applications
        if ($this->has('compare_with') && is_string($this->input('compare_with'))) {
            $this->merge([
                'compare_with' => array_map('intval', array_filter(explode(',', $this->input('compare_with')))),
            ]);
        }

        // Handle comma-separated comparison fields
        if ($this->has('comparison_fields') && is_string($this->input('comparison_fields'))) {
            $this->merge([
                'comparison_fields' => array_filter(explode(',', $this->input('comparison_fields'))),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $applicationId = $this->route('jobApplication') ?? $this->route('id');
            
            if (!$applicationId) {
                $validator->errors()->add('application', __('validation.application_id_required'));
                return;
            }

            $application = JobApplication::find($applicationId);
            if (!$application) {
                $validator->errors()->add('application', __('validation.application_not_found'));
                return;
            }

            // Validate privacy options based on user role
            if ($this->input('include_private') && !$user->hasRole('admin')) {
                $isOwner = $user->hasRole('candidate') && $application->candidate_id === $user->id;
                $isEmployer = $user->hasRole('employer') && 
                    $user->companies()->pluck('id')->contains($application->job->company_id ?? null);
                
                if (!$isOwner && !$isEmployer) {
                    $validator->errors()->add('include_private', __('validation.unauthorized_private_access'));
                }
            }

            // Validate comparison applications access
            if ($this->filled('compare_with')) {
                $compareIds = $this->input('compare_with');
                $accessibleIds = [];

                if ($user->hasRole('admin')) {
                    $accessibleIds = JobApplication::whereIn('id', $compareIds)->pluck('id')->toArray();
                } elseif ($user->hasRole('candidate')) {
                    $accessibleIds = JobApplication::whereIn('id', $compareIds)
                        ->where('candidate_id', $user->id)
                        ->pluck('id')->toArray();
                } elseif ($user->hasRole('employer')) {
                    $userCompanyIds = $user->companies()->pluck('id')->toArray();
                    $accessibleIds = JobApplication::whereIn('id', $compareIds)
                        ->whereHas('job', function ($query) use ($userCompanyIds) {
                            $query->whereIn('company_id', $userCompanyIds);
                        })
                        ->pluck('id')->toArray();
                }

                $unauthorizedIds = array_diff($compareIds, $accessibleIds);
                if (!empty($unauthorizedIds)) {
                    $validator->errors()->add('compare_with', 
                        __('validation.unauthorized_comparison_applications', [
                            'ids' => implode(', ', $unauthorizedIds)
                        ])
                    );
                }
            }

            // Validate export fields based on permissions
            if ($this->filled('export_fields')) {
                $restrictedFields = ['salary_expectations', 'contact_info'];
                $requestedFields = $this->input('export_fields');
                
                if (!$user->hasRole('admin')) {
                    $isOwner = $user->hasRole('candidate') && $application->candidate_id === $user->id;
                    $isEmployer = $user->hasRole('employer') && 
                        $user->companies()->pluck('id')->contains($application->job->company_id ?? null);
                    
                    if (!$isOwner && !$isEmployer) {
                        $unauthorizedFields = array_intersect($requestedFields, $restrictedFields);
                        if (!empty($unauthorizedFields)) {
                            $validator->errors()->add('export_fields', 
                                __('validation.unauthorized_export_fields', [
                                    'fields' => implode(', ', $unauthorizedFields)
                                ])
                            );
                        }
                    }
                }
            }

            // Validate format and template combinations
            if ($this->input('format') === 'pdf' && !in_array($this->input('template'), ['detailed', 'summary'])) {
                $validator->errors()->add('template', __('validation.invalid_pdf_template'));
            }

            // Validate timezone
            if ($this->filled('timezone')) {
                $timezone = $this->input('timezone');
                if (!in_array($timezone, timezone_identifiers_list())) {
                    $validator->errors()->add('timezone', __('validation.invalid_timezone'));
                }
            }
        });
    }
} 