<?php

namespace App\Http\Requests\Job;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Basic job information
            'title' => [
                'required',
                'string',
                'max:255',
                'min:5',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_content'));
                    }
                },
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'min:5',
                'regex:/^[a-z0-9\-]+$/',
                'unique:jobs,slug',
            ],

            'description' => [
                'required',
                'string',
                'min:100',
                'max:10000',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_content'));
                    }
                },
            ],

            'short_description' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'requirements' => [
                'required',
                'string',
                'min:50',
                'max:5000',
            ],

            'responsibilities' => [
                'required',
                'string',
                'min:50',
                'max:5000',
            ],

            'benefits' => [
                'sometimes',
                'string',
                'max:3000',
            ],

            // Company and location
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) {
                    if (! $this->isCompanyActive($value)) {
                        $fail(__('validation.company_not_active'));
                    }
                },
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'city_id' => [
                'required',
                'integer',
                'exists:cities,id',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'is_remote' => [
                'sometimes',
                'boolean',
            ],

            'remote_type' => [
                'required_if:is_remote,true',
                'string',
                Rule::in(['fully_remote', 'hybrid', 'temporary_remote']),
            ],

            'address' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'postal_code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\s\-]+$/i',
            ],

            // Job categorization
            'job_category_id' => [
                'required',
                'integer',
                'exists:job_categories,id',
            ],

            'job_type_id' => [
                'required',
                'integer',
                'exists:job_types,id',
            ],

            'job_shift_id' => [
                'sometimes',
                'integer',
                'exists:job_shifts,id',
            ],

            'career_level_id' => [
                'required',
                'integer',
                'exists:career_levels,id',
            ],

            'functional_area_id' => [
                'sometimes',
                'integer',
                'exists:functional_areas,id',
            ],

            // Employment details
            'employment_type' => [
                'required',
                'string',
                Rule::in([
                    'full_time',
                    'part_time',
                    'contract',
                    'temporary',
                    'internship',
                    'freelance',
                    'volunteer',
                ]),
            ],

            'experience_level' => [
                'required',
                'string',
                Rule::in([
                    'entry_level',
                    'junior',
                    'mid_level',
                    'senior',
                    'lead',
                    'manager',
                    'director',
                    'executive',
                ]),
            ],

            'min_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'max_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
                'gte:min_experience',
            ],

            'education_level_id' => [
                'sometimes',
                'integer',
                'exists:education_levels,id',
            ],

            'degree_level_id' => [
                'sometimes',
                'integer',
                'exists:degree_levels,id',
            ],

            // Salary information
            'salary_type' => [
                'required',
                'string',
                Rule::in(['fixed', 'range', 'negotiable', 'commission', 'hourly']),
            ],

            'min_salary' => [
                'required_if:salary_type,range,fixed',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'max_salary' => [
                'required_if:salary_type,range',
                'numeric',
                'min:0',
                'max:10000000',
                'gte:min_salary',
            ],

            'salary_currency_id' => [
                'required_unless:salary_type,negotiable',
                'integer',
                'exists:salary_currencies,id',
            ],

            'salary_period' => [
                'required_unless:salary_type,negotiable',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'yearly']),
            ],

            'hide_salary' => [
                'sometimes',
                'boolean',
            ],

            'commission_rate' => [
                'required_if:salary_type,commission',
                'numeric',
                'min:0',
                'max:100',
            ],

            // Skills and qualifications
            'skills' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'required_skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'required_skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'preferred_skills' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'preferred_skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'languages' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'languages.*' => [
                'integer',
                'exists:languages,id',
            ],

            'certifications' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'certifications.*' => [
                'string',
                'max:100',
            ],

            // Application settings
            'application_deadline' => [
                'sometimes',
                'date',
                'after:today',
                'before:'.now()->addYear()->toDateString(),
            ],

            'max_applications' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10000',
            ],

            'auto_reject_after_deadline' => [
                'sometimes',
                'boolean',
            ],

            'require_cover_letter' => [
                'sometimes',
                'boolean',
            ],

            'require_resume' => [
                'sometimes',
                'boolean',
            ],

            'application_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'external_apply_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            'application_instructions' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Job status and visibility
            'status' => [
                'sometimes',
                'string',
                Rule::in(['draft', 'active', 'paused', 'closed', 'expired', 'archived']),
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'is_urgent' => [
                'sometimes',
                'boolean',
            ],

            'is_confidential' => [
                'sometimes',
                'boolean',
            ],

            'visibility' => [
                'sometimes',
                'string',
                Rule::in(['public', 'private', 'internal', 'premium']),
            ],

            'featured_until' => [
                'required_if:is_featured,true',
                'date',
                'after:today',
                'before:'.now()->addMonths(6)->toDateString(),
            ],

            // Work environment
            'work_environment' => [
                'sometimes',
                'string',
                Rule::in(['office', 'remote', 'hybrid', 'field', 'travel']),
            ],

            'travel_required' => [
                'sometimes',
                'boolean',
            ],

            'travel_percentage' => [
                'required_if:travel_required,true',
                'integer',
                'min:1',
                'max:100',
            ],

            'overtime_required' => [
                'sometimes',
                'boolean',
            ],

            'weekend_work' => [
                'sometimes',
                'boolean',
            ],

            'shift_work' => [
                'sometimes',
                'boolean',
            ],

            // Company benefits
            'health_insurance' => [
                'sometimes',
                'boolean',
            ],

            'dental_insurance' => [
                'sometimes',
                'boolean',
            ],

            'vision_insurance' => [
                'sometimes',
                'boolean',
            ],

            'retirement_plan' => [
                'sometimes',
                'boolean',
            ],

            'paid_time_off' => [
                'sometimes',
                'boolean',
            ],

            'flexible_schedule' => [
                'sometimes',
                'boolean',
            ],

            'professional_development' => [
                'sometimes',
                'boolean',
            ],

            'gym_membership' => [
                'sometimes',
                'boolean',
            ],

            'stock_options' => [
                'sometimes',
                'boolean',
            ],

            'bonus_eligible' => [
                'sometimes',
                'boolean',
            ],

            // Additional requirements
            'background_check_required' => [
                'sometimes',
                'boolean',
            ],

            'drug_test_required' => [
                'sometimes',
                'boolean',
            ],

            'security_clearance_required' => [
                'sometimes',
                'boolean',
            ],

            'security_clearance_level' => [
                'required_if:security_clearance_required,true',
                'string',
                Rule::in(['confidential', 'secret', 'top_secret']),
            ],

            'driver_license_required' => [
                'sometimes',
                'boolean',
            ],

            'license_type' => [
                'required_if:driver_license_required,true',
                'string',
                'max:50',
            ],

            'physical_requirements' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // SEO and marketing
            'meta_title' => [
                'sometimes',
                'string',
                'max:60',
            ],

            'meta_description' => [
                'sometimes',
                'string',
                'max:160',
            ],

            'keywords' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'keywords.*' => [
                'string',
                'max:50',
            ],

            'tags' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],

            // Contact information
            'contact_person' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.\']+$/',
            ],

            'contact_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'contact_phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            // Performance tracking
            'views_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'applications_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'priority_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            // Integration and external data
            'external_id' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'ats_job_id' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'posted_by' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Custom fields
            'custom_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'custom_fields.*' => [
                'string',
                'max:500',
            ],

            'notes' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Scheduling
            'publish_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'expire_date' => [
                'sometimes',
                'date',
                'after:publish_date',
                'before:'.now()->addYear()->toDateString(),
            ],

            'auto_renew' => [
                'sometimes',
                'boolean',
            ],

            'renewal_period' => [
                'required_if:auto_renew,true',
                'integer',
                'min:1',
                'max:365',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.required_field', ['field' => __('validation.attributes.job_title')]),
            'title.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.job_title'), 'min' => 5]),
            'title.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.job_title'), 'max' => 255]),

            'slug.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.slug'), 'min' => 5]),
            'slug.regex' => __('validation.invalid_slug_format'),
            'slug.unique' => __('validation.unique', ['attribute' => __('validation.attributes.slug')]),

            'description.required' => __('validation.required_field', ['field' => __('validation.attributes.description')]),
            'description.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.description'), 'min' => 100]),
            'description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.description'), 'max' => 10000]),

            'requirements.required' => __('validation.required_field', ['field' => __('validation.attributes.requirements')]),
            'requirements.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.requirements'), 'min' => 50]),
            'requirements.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.requirements'), 'max' => 5000]),

            'responsibilities.required' => __('validation.required_field', ['field' => __('validation.attributes.responsibilities')]),
            'responsibilities.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.responsibilities'), 'min' => 50]),
            'responsibilities.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.responsibilities'), 'max' => 5000]),

            'company_id.required' => __('validation.required_field', ['field' => __('validation.attributes.company')]),
            'company_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.company')]),

            'location.required' => __('validation.required_field', ['field' => __('validation.attributes.location')]),

            'city_id.required' => __('validation.required_field', ['field' => __('validation.attributes.city')]),
            'city_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.city')]),

            'state_id.required' => __('validation.required_field', ['field' => __('validation.attributes.state')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.state')]),

            'country_id.required' => __('validation.required_field', ['field' => __('validation.attributes.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),

            'remote_type.required_if' => __('validation.remote_type_required'),
            'remote_type.in' => __('validation.invalid_remote_type'),

            'job_category_id.required' => __('validation.required_field', ['field' => __('validation.attributes.job_category')]),
            'job_category_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_category')]),

            'job_type_id.required' => __('validation.required_field', ['field' => __('validation.attributes.job_type')]),
            'job_type_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_type')]),

            'career_level_id.required' => __('validation.required_field', ['field' => __('validation.attributes.career_level')]),
            'career_level_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.career_level')]),

            'employment_type.required' => __('validation.required_field', ['field' => __('validation.attributes.employment_type')]),
            'employment_type.in' => __('validation.invalid_employment_type'),

            'experience_level.required' => __('validation.required_field', ['field' => __('validation.attributes.experience_level')]),
            'experience_level.in' => __('validation.invalid_experience_level'),

            'min_experience.min' => __('validation.min_value', ['attribute' => __('validation.attributes.min_experience'), 'min' => 0]),
            'min_experience.max' => __('validation.max_value', ['attribute' => __('validation.attributes.min_experience'), 'max' => 50]),

            'max_experience.gte' => __('validation.gte', ['attribute' => __('validation.attributes.max_experience'), 'value' => __('validation.attributes.min_experience')]),

            'salary_type.required' => __('validation.required_field', ['field' => __('validation.attributes.salary_type')]),
            'salary_type.in' => __('validation.invalid_salary_type'),

            'min_salary.required_if' => __('validation.min_salary_required'),
            'min_salary.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.min_salary')]),
            'min_salary.max' => __('validation.max_value', ['attribute' => __('validation.attributes.min_salary'), 'max' => 10000000]),

            'max_salary.required_if' => __('validation.max_salary_required'),
            'max_salary.gte' => __('validation.gte', ['attribute' => __('validation.attributes.max_salary'), 'value' => __('validation.attributes.min_salary')]),

            'salary_currency_id.required_unless' => __('validation.salary_currency_required'),
            'salary_currency_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.salary_currency')]),

            'salary_period.required_unless' => __('validation.salary_period_required'),
            'salary_period.in' => __('validation.invalid_salary_period'),

            'commission_rate.required_if' => __('validation.commission_rate_required'),
            'commission_rate.max' => __('validation.max_value', ['attribute' => __('validation.attributes.commission_rate'), 'max' => 100]),

            'skills.array' => __('validation.array', ['attribute' => __('validation.attributes.skills')]),
            'skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.skills'), 'max' => 50]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.skill')]),

            'required_skills.array' => __('validation.array', ['attribute' => __('validation.attributes.required_skills')]),
            'required_skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.required_skills'), 'max' => 20]),

            'preferred_skills.array' => __('validation.array', ['attribute' => __('validation.attributes.preferred_skills')]),
            'preferred_skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.preferred_skills'), 'max' => 30]),

            'languages.array' => __('validation.array', ['attribute' => __('validation.attributes.languages')]),
            'languages.max' => __('validation.max_items', ['attribute' => __('validation.attributes.languages'), 'max' => 10]),
            'languages.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.language')]),

            'certifications.array' => __('validation.array', ['attribute' => __('validation.attributes.certifications')]),
            'certifications.max' => __('validation.max_items', ['attribute' => __('validation.attributes.certifications'), 'max' => 20]),

            'application_deadline.after' => __('validation.application_deadline_future'),
            'application_deadline.before' => __('validation.application_deadline_limit'),

            'max_applications.min' => __('validation.min_value', ['attribute' => __('validation.attributes.max_applications'), 'min' => 1]),
            'max_applications.max' => __('validation.max_value', ['attribute' => __('validation.attributes.max_applications'), 'max' => 10000]),

            'application_email.email' => __('validation.email', ['attribute' => __('validation.attributes.application_email')]),

            'external_apply_url.url' => __('validation.url', ['attribute' => __('validation.attributes.external_apply_url')]),

            'status.in' => __('validation.invalid_job_status'),

            'visibility.in' => __('validation.invalid_visibility'),

            'featured_until.required_if' => __('validation.featured_until_required'),
            'featured_until.after' => __('validation.featured_until_future'),
            'featured_until.before' => __('validation.featured_until_limit'),

            'work_environment.in' => __('validation.invalid_work_environment'),

            'travel_percentage.required_if' => __('validation.travel_percentage_required'),
            'travel_percentage.min' => __('validation.min_value', ['attribute' => __('validation.attributes.travel_percentage'), 'min' => 1]),
            'travel_percentage.max' => __('validation.max_value', ['attribute' => __('validation.attributes.travel_percentage'), 'max' => 100]),

            'security_clearance_level.required_if' => __('validation.security_clearance_level_required'),
            'security_clearance_level.in' => __('validation.invalid_security_clearance_level'),

            'license_type.required_if' => __('validation.license_type_required'),

            'meta_title.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.meta_title'), 'max' => 60]),
            'meta_description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.meta_description'), 'max' => 160]),

            'keywords.array' => __('validation.array', ['attribute' => __('validation.attributes.keywords')]),
            'keywords.max' => __('validation.max_items', ['attribute' => __('validation.attributes.keywords'), 'max' => 20]),
            'keywords.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.keyword'), 'max' => 50]),

            'tags.array' => __('validation.array', ['attribute' => __('validation.attributes.tags')]),
            'tags.max' => __('validation.max_items', ['attribute' => __('validation.attributes.tags'), 'max' => 30]),
            'tags.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.tag'), 'max' => 50]),
            'tags.*.regex' => __('validation.invalid_tag_format'),

            'contact_person.regex' => __('validation.invalid_person_name'),
            'contact_email.email' => __('validation.email', ['attribute' => __('validation.attributes.contact_email')]),
            'contact_phone.regex' => __('validation.invalid_phone_format'),

            'priority_score.min' => __('validation.min_value', ['attribute' => __('validation.attributes.priority_score'), 'min' => 0]),
            'priority_score.max' => __('validation.max_value', ['attribute' => __('validation.attributes.priority_score'), 'max' => 100]),

            'custom_fields.array' => __('validation.array', ['attribute' => __('validation.attributes.custom_fields')]),
            'custom_fields.max' => __('validation.max_items', ['attribute' => __('validation.attributes.custom_fields'), 'max' => 20]),
            'custom_fields.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.custom_field'), 'max' => 500]),

            'notes.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.notes'), 'max' => 2000]),

            'publish_date.after_or_equal' => __('validation.publish_date_future'),

            'expire_date.after' => __('validation.expire_date_after_publish'),
            'expire_date.before' => __('validation.expire_date_limit'),

            'renewal_period.required_if' => __('validation.renewal_period_required'),
            'renewal_period.min' => __('validation.min_value', ['attribute' => __('validation.attributes.renewal_period'), 'min' => 1]),
            'renewal_period.max' => __('validation.max_value', ['attribute' => __('validation.attributes.renewal_period'), 'max' => 365]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.job_title'),
            'slug' => __('validation.attributes.slug'),
            'description' => __('validation.attributes.description'),
            'short_description' => __('validation.attributes.short_description'),
            'requirements' => __('validation.attributes.requirements'),
            'responsibilities' => __('validation.attributes.responsibilities'),
            'benefits' => __('validation.attributes.benefits'),
            'company_id' => __('validation.attributes.company'),
            'location' => __('validation.attributes.location'),
            'city_id' => __('validation.attributes.city'),
            'state_id' => __('validation.attributes.state'),
            'country_id' => __('validation.attributes.country'),
            'is_remote' => __('validation.attributes.is_remote'),
            'remote_type' => __('validation.attributes.remote_type'),
            'address' => __('validation.attributes.address'),
            'postal_code' => __('validation.attributes.postal_code'),
            'job_category_id' => __('validation.attributes.job_category'),
            'job_type_id' => __('validation.attributes.job_type'),
            'job_shift_id' => __('validation.attributes.job_shift'),
            'career_level_id' => __('validation.attributes.career_level'),
            'functional_area_id' => __('validation.attributes.functional_area'),
            'employment_type' => __('validation.attributes.employment_type'),
            'experience_level' => __('validation.attributes.experience_level'),
            'min_experience' => __('validation.attributes.min_experience'),
            'max_experience' => __('validation.attributes.max_experience'),
            'education_level_id' => __('validation.attributes.education_level'),
            'degree_level_id' => __('validation.attributes.degree_level'),
            'salary_type' => __('validation.attributes.salary_type'),
            'min_salary' => __('validation.attributes.min_salary'),
            'max_salary' => __('validation.attributes.max_salary'),
            'salary_currency_id' => __('validation.attributes.salary_currency'),
            'salary_period' => __('validation.attributes.salary_period'),
            'hide_salary' => __('validation.attributes.hide_salary'),
            'commission_rate' => __('validation.attributes.commission_rate'),
            'skills' => __('validation.attributes.skills'),
            'required_skills' => __('validation.attributes.required_skills'),
            'preferred_skills' => __('validation.attributes.preferred_skills'),
            'languages' => __('validation.attributes.languages'),
            'certifications' => __('validation.attributes.certifications'),
            'application_deadline' => __('validation.attributes.application_deadline'),
            'max_applications' => __('validation.attributes.max_applications'),
            'auto_reject_after_deadline' => __('validation.attributes.auto_reject_after_deadline'),
            'require_cover_letter' => __('validation.attributes.require_cover_letter'),
            'require_resume' => __('validation.attributes.require_resume'),
            'application_email' => __('validation.attributes.application_email'),
            'external_apply_url' => __('validation.attributes.external_apply_url'),
            'application_instructions' => __('validation.attributes.application_instructions'),
            'status' => __('validation.attributes.status'),
            'is_featured' => __('validation.attributes.is_featured'),
            'is_urgent' => __('validation.attributes.is_urgent'),
            'is_confidential' => __('validation.attributes.is_confidential'),
            'visibility' => __('validation.attributes.visibility'),
            'featured_until' => __('validation.attributes.featured_until'),
            'work_environment' => __('validation.attributes.work_environment'),
            'travel_required' => __('validation.attributes.travel_required'),
            'travel_percentage' => __('validation.attributes.travel_percentage'),
            'overtime_required' => __('validation.attributes.overtime_required'),
            'weekend_work' => __('validation.attributes.weekend_work'),
            'shift_work' => __('validation.attributes.shift_work'),
            'health_insurance' => __('validation.attributes.health_insurance'),
            'dental_insurance' => __('validation.attributes.dental_insurance'),
            'vision_insurance' => __('validation.attributes.vision_insurance'),
            'retirement_plan' => __('validation.attributes.retirement_plan'),
            'paid_time_off' => __('validation.attributes.paid_time_off'),
            'flexible_schedule' => __('validation.attributes.flexible_schedule'),
            'professional_development' => __('validation.attributes.professional_development'),
            'gym_membership' => __('validation.attributes.gym_membership'),
            'stock_options' => __('validation.attributes.stock_options'),
            'bonus_eligible' => __('validation.attributes.bonus_eligible'),
            'background_check_required' => __('validation.attributes.background_check_required'),
            'drug_test_required' => __('validation.attributes.drug_test_required'),
            'security_clearance_required' => __('validation.attributes.security_clearance_required'),
            'security_clearance_level' => __('validation.attributes.security_clearance_level'),
            'driver_license_required' => __('validation.attributes.driver_license_required'),
            'license_type' => __('validation.attributes.license_type'),
            'physical_requirements' => __('validation.attributes.physical_requirements'),
            'meta_title' => __('validation.attributes.meta_title'),
            'meta_description' => __('validation.attributes.meta_description'),
            'keywords' => __('validation.attributes.keywords'),
            'tags' => __('validation.attributes.tags'),
            'contact_person' => __('validation.attributes.contact_person'),
            'contact_email' => __('validation.attributes.contact_email'),
            'contact_phone' => __('validation.attributes.contact_phone'),
            'views_count' => __('validation.attributes.views_count'),
            'applications_count' => __('validation.attributes.applications_count'),
            'priority_score' => __('validation.attributes.priority_score'),
            'external_id' => __('validation.attributes.external_id'),
            'source' => __('validation.attributes.source'),
            'ats_job_id' => __('validation.attributes.ats_job_id'),
            'posted_by' => __('validation.attributes.posted_by'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'notes' => __('validation.attributes.notes'),
            'publish_date' => __('validation.attributes.publish_date'),
            'expire_date' => __('validation.attributes.expire_date'),
            'auto_renew' => __('validation.attributes.auto_renew'),
            'renewal_period' => __('validation.attributes.renewal_period'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug from title if not provided
        if (! $this->has('slug') && $this->has('title')) {
            $this->merge([
                'slug' => \Str::slug($this->title),
            ]);
        }

        // Set default values
        $this->merge([
            'is_remote' => $this->boolean('is_remote', false),
            'is_featured' => $this->boolean('is_featured', false),
            'is_urgent' => $this->boolean('is_urgent', false),
            'is_confidential' => $this->boolean('is_confidential', false),
            'hide_salary' => $this->boolean('hide_salary', false),
            'auto_reject_after_deadline' => $this->boolean('auto_reject_after_deadline', false),
            'require_cover_letter' => $this->boolean('require_cover_letter', false),
            'require_resume' => $this->boolean('require_resume', true),
            'travel_required' => $this->boolean('travel_required', false),
            'overtime_required' => $this->boolean('overtime_required', false),
            'weekend_work' => $this->boolean('weekend_work', false),
            'shift_work' => $this->boolean('shift_work', false),
            'health_insurance' => $this->boolean('health_insurance', false),
            'dental_insurance' => $this->boolean('dental_insurance', false),
            'vision_insurance' => $this->boolean('vision_insurance', false),
            'retirement_plan' => $this->boolean('retirement_plan', false),
            'paid_time_off' => $this->boolean('paid_time_off', false),
            'flexible_schedule' => $this->boolean('flexible_schedule', false),
            'professional_development' => $this->boolean('professional_development', false),
            'gym_membership' => $this->boolean('gym_membership', false),
            'stock_options' => $this->boolean('stock_options', false),
            'bonus_eligible' => $this->boolean('bonus_eligible', false),
            'background_check_required' => $this->boolean('background_check_required', false),
            'drug_test_required' => $this->boolean('drug_test_required', false),
            'security_clearance_required' => $this->boolean('security_clearance_required', false),
            'driver_license_required' => $this->boolean('driver_license_required', false),
            'auto_renew' => $this->boolean('auto_renew', false),
            'status' => $this->status ?? 'draft',
            'visibility' => $this->visibility ?? 'public',
            'work_environment' => $this->work_environment ?? 'office',
            'salary_period' => $this->salary_period ?? 'monthly',
            'priority_score' => $this->integer('priority_score', 50),
            'max_applications' => $this->integer('max_applications', 100),
            'views_count' => $this->integer('views_count', 0),
            'applications_count' => $this->integer('applications_count', 0),
        ]);

        // Process arrays from strings
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('intval', explode(',', $this->skills))),
            ]);
        }

        if ($this->has('required_skills') && is_string($this->required_skills)) {
            $this->merge([
                'required_skills' => array_filter(array_map('intval', explode(',', $this->required_skills))),
            ]);
        }

        if ($this->has('preferred_skills') && is_string($this->preferred_skills)) {
            $this->merge([
                'preferred_skills' => array_filter(array_map('intval', explode(',', $this->preferred_skills))),
            ]);
        }

        if ($this->has('languages') && is_string($this->languages)) {
            $this->merge([
                'languages' => array_filter(array_map('intval', explode(',', $this->languages))),
            ]);
        }

        if ($this->has('certifications') && is_string($this->certifications)) {
            $this->merge([
                'certifications' => array_filter(array_map('trim', explode(',', $this->certifications))),
            ]);
        }

        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => array_filter(array_map('trim', explode(',', $this->keywords))),
            ]);
        }

        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }

        // Process custom fields JSON
        if ($this->has('custom_fields') && is_string($this->custom_fields)) {
            try {
                $customFields = json_decode($this->custom_fields, true);
                if (is_array($customFields)) {
                    $this->merge(['custom_fields' => $customFields]);
                }
            } catch (\Exception $e) {
                // Keep as string if JSON decode fails
            }
        }

        // Generate short description from description if not provided
        if (! $this->has('short_description') && $this->has('description')) {
            $this->merge([
                'short_description' => \Str::limit(strip_tags($this->description), 200),
            ]);
        }

        // Set default application deadline if not provided
        if (! $this->has('application_deadline')) {
            $this->merge([
                'application_deadline' => now()->addDays(30)->toDateString(),
            ]);
        }

        // Set default publish date if not provided
        if (! $this->has('publish_date')) {
            $this->merge([
                'publish_date' => now()->toDateString(),
            ]);
        }

        // Set default expire date if not provided
        if (! $this->has('expire_date')) {
            $this->merge([
                'expire_date' => now()->addDays(60)->toDateString(),
            ]);
        }

        // Log job posting attempt
        Log::info('Job posting attempt', [
            'title' => $this->title,
            'company_id' => $this->company_id,
            'job_category_id' => $this->job_category_id,
            'employment_type' => $this->employment_type,
            'salary_type' => $this->salary_type,
            'location' => $this->location,
            'is_remote' => $this->is_remote,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set processing metadata
        $this->merge([
            'job_id' => 'JOB-'.date('Ymd').'-'.strtoupper(substr(md5($this->title.time()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Set processing flags
        $this->merge([
            'requires_approval' => $this->shouldRequireApproval(),
            'auto_publish' => $this->shouldAutoPublish(),
            'send_notifications' => true,
            'index_for_search' => true,
        ]);
    }

    /**
     * Check if content contains inappropriate material.
     */
    private function containsInappropriateContent($content): bool
    {
        $inappropriateWords = [
            'scam', 'pyramid', 'mlm', 'get rich quick', 'work from home guaranteed',
            'no experience required high pay', 'easy money', 'guaranteed income',
        ];

        $content = strtolower($content);
        foreach ($inappropriateWords as $word) {
            if (strpos($content, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if company is active and can post jobs.
     */
    private function isCompanyActive($companyId): bool
    {
        $company = \DB::table('companies')
            ->where('id', $companyId)
            ->where('status', 'active')
            ->where('is_verified', true)
            ->first();

        return $company !== null;
    }

    /**
     * Determine if job requires approval.
     */
    private function shouldRequireApproval(): bool
    {
        return $this->is_featured ||
               $this->salary_type === 'negotiable' ||
               $this->containsInappropriateContent($this->title.' '.$this->description);
    }

    /**
     * Determine if job should be auto-published.
     */
    private function shouldAutoPublish(): bool
    {
        return $this->status === 'active' &&
               ! $this->shouldRequireApproval() &&
               $this->publish_date <= now()->toDateString();
    }
}
