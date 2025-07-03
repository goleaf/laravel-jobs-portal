<?php

namespace App\Http\Requests\BusinessLogic;

use App\Http\Requests\Foundation\AbstractBaseRequest;

/**
 * Business Logic Request - Base class for business logic validation
 *
 * Handles validation for:
 * - Company management operations
 * - Job posting and management
 * - Application workflows
 * - Employer operations
 * - Candidate profile management
 * - Business process validations
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
abstract class BusinessLogicRequest extends AbstractBaseRequest
{
    /**
     * Security level for business logic operations
     */
    protected string $securityLevel = 'high';

    /**
     * Get domain-specific validation rules for business logic
     */
    protected function getDomainRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:active,inactive,draft,published,expired'],
            'created_by' => ['sometimes', 'string', 'max:255'],
            'updated_by' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * Get domain-specific error messages for business logic
     */
    protected function getDomainMessages(): array
    {
        return [
            'title.required' => __('validation.business_logic.title_required'),
            'title.string' => __('validation.business_logic.title_string'),
            'title.max' => __('validation.business_logic.title_max'),
            'status.in' => __('validation.business_logic.status_invalid'),
        ];
    }

    /**
     * Get domain-specific attribute names for business logic
     */
    protected function getDomainAttributes(): array
    {
        return [
            'title' => __('validation.attributes.title'),
            'status' => __('validation.attributes.status'),
            'created_by' => __('validation.attributes.created_by'),
            'updated_by' => __('validation.attributes.updated_by'),
        ];
    }

    /**
     * Common validation rules for company operations
     */
    protected function getCompanyRules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_website' => ['sometimes', 'url', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['sometimes', 'string', 'max:20'],
            'company_address' => ['required', 'string', 'max:500'],
            'company_size_id' => ['required', 'exists:company_sizes,id'],
            'industry_id' => ['required', 'exists:industries,id'],
            'ownership_type_id' => ['sometimes', 'exists:ownership_types,id'],
            'established_year' => ['sometimes', 'integer', 'min:1800', 'max:'.date('Y')],
            'no_of_offices' => ['sometimes', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * Common validation rules for job operations
     */
    protected function getJobRules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string', 'min:100'],
            'job_requirements' => ['required', 'string', 'min:50'],
            'salary_min' => ['sometimes', 'numeric', 'min:0'],
            'salary_max' => ['sometimes', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency_id' => ['required_with:salary_min,salary_max', 'exists:salary_currencies,id'],
            'salary_period_id' => ['required_with:salary_min,salary_max', 'exists:salary_periods,id'],
            'application_deadline' => ['required', 'date', 'after:today'],
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'career_level_id' => ['required', 'exists:career_levels,id'],
            'job_type_id' => ['required', 'exists:job_types,id'],
            'required_degree_level_id' => ['sometimes', 'exists:required_degree_levels,id'],
            'experience_min' => ['sometimes', 'integer', 'min:0', 'max:50'],
            'experience_max' => ['sometimes', 'integer', 'min:0', 'max:50', 'gte:experience_min'],
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['exists:skills,id'],
            'benefits' => ['sometimes', 'array'],
            'benefits.*' => ['string', 'max:255'],
        ];
    }

    /**
     * Common validation rules for application operations
     */
    protected function getApplicationRules(): array
    {
        return [
            'job_id' => ['required', 'exists:jobs,id'],
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['required', 'email', 'max:255'],
            'candidate_phone' => ['sometimes', 'string', 'max:20'],
            'cover_letter' => ['sometimes', 'string', 'max:2000'],
            'resume_file' => ['sometimes', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
            'expected_salary' => ['sometimes', 'numeric', 'min:0'],
            'available_from' => ['sometimes', 'date', 'after_or_equal:today'],
            'linkedin_profile' => ['sometimes', 'url', 'max:255'],
            'portfolio_url' => ['sometimes', 'url', 'max:255'],
        ];
    }

    /**
     * Common validation rules for employer operations
     */
    protected function getEmployerRules(): array
    {
        return [
            'employer_name' => ['required', 'string', 'max:255'],
            'employer_email' => ['required', 'email', 'max:255'],
            'employer_phone' => ['sometimes', 'string', 'max:20'],
            'job_title' => ['required', 'string', 'max:255'],
            'department' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * Common validation rules for candidate operations
     */
    protected function getCandidateRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'gender' => ['sometimes', 'in:male,female,other,prefer_not_to_say'],
            'nationality' => ['sometimes', 'string', 'max:100'],
            'current_job_title' => ['sometimes', 'string', 'max:255'],
            'years_of_experience' => ['sometimes', 'integer', 'min:0', 'max:50'],
            'education_level' => ['sometimes', 'string', 'max:100'],
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['exists:skills,id'],
            'languages' => ['sometimes', 'array'],
            'languages.*' => ['string', 'max:100'],
        ];
    }

    /**
     * Perform business logic specific validation
     */
    protected function performCustomValidation($validator): void
    {
        // Validate business rules
        $this->validateBusinessRules($validator);

        // Validate workflow states
        $this->validateWorkflowStates($validator);

        // Validate permissions and access control
        $this->validatePermissions($validator);

        // Validate data integrity
        $this->validateDataIntegrity($validator);
    }

    /**
     * Validate business rules
     */
    protected function validateBusinessRules($validator): void
    {
        // Override in specific request classes to implement business rule validation
    }

    /**
     * Validate workflow states
     */
    protected function validateWorkflowStates($validator): void
    {
        // Override in specific request classes to implement workflow validation
    }

    /**
     * Validate permissions and access control
     */
    protected function validatePermissions($validator): void
    {
        // Override in specific request classes to implement permission validation
    }

    /**
     * Validate data integrity
     */
    protected function validateDataIntegrity($validator): void
    {
        // Override in specific request classes to implement data integrity validation
    }

    /**
     * Apply business logic specific sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Business logic specific sanitization
        if (isset($sanitized['title'])) {
            $sanitized['title'] = trim($sanitized['title']);
        }

        if (isset($sanitized['job_title'])) {
            $sanitized['job_title'] = trim(ucwords(strtolower($sanitized['job_title'])));
        }

        if (isset($sanitized['company_name'])) {
            $sanitized['company_name'] = trim($sanitized['company_name']);
        }

        // Sanitize email fields
        foreach (['email', 'company_email', 'candidate_email', 'employer_email'] as $emailField) {
            if (isset($sanitized[$emailField])) {
                $sanitized[$emailField] = strtolower(trim($sanitized[$emailField]));
            }
        }

        return $sanitized;
    }
}
