<?php

namespace App\Http\Requests\Resume;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StoreResumeRequest extends FormRequest
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
            // Basic resume information
            'title' => [
                'required',
                'string',
                'max:255',
                'min:5',
            ],

            'description' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240', // 10MB
            ],

            'file_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'file_size' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10485760', // 10MB in bytes
            ],

            // Professional information
            'professional_summary' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'objective' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'career_level' => [
                'sometimes',
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

            'years_of_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'industry_id' => [
                'sometimes',
                'integer',
                'exists:industries,id',
            ],

            'functional_area_id' => [
                'sometimes',
                'integer',
                'exists:functional_areas,id',
            ],

            // Skills and competencies
            'skills' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'technical_skills' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'technical_skills.*' => [
                'string',
                'max:100',
            ],

            'soft_skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'soft_skills.*' => [
                'string',
                'max:100',
            ],

            'languages' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'languages.*.language_id' => [
                'required_with:languages',
                'integer',
                'exists:languages,id',
            ],

            'languages.*.proficiency' => [
                'required_with:languages',
                'string',
                Rule::in(['basic', 'conversational', 'fluent', 'native']),
            ],

            // Work experience
            'work_experiences' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'work_experiences.*.company_name' => [
                'required_with:work_experiences',
                'string',
                'max:255',
            ],

            'work_experiences.*.job_title' => [
                'required_with:work_experiences',
                'string',
                'max:255',
            ],

            'work_experiences.*.start_date' => [
                'required_with:work_experiences',
                'date',
                'before_or_equal:today',
            ],

            'work_experiences.*.end_date' => [
                'sometimes',
                'date',
                'after:work_experiences.*.start_date',
                'before_or_equal:today',
            ],

            'work_experiences.*.is_current' => [
                'sometimes',
                'boolean',
            ],

            'work_experiences.*.description' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'work_experiences.*.achievements' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'work_experiences.*.achievements.*' => [
                'string',
                'max:500',
            ],

            'work_experiences.*.location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'work_experiences.*.employment_type' => [
                'sometimes',
                'string',
                Rule::in(['full_time', 'part_time', 'contract', 'freelance', 'internship', 'temporary']),
            ],

            // Education
            'educations' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'educations.*.institution' => [
                'required_with:educations',
                'string',
                'max:255',
            ],

            'educations.*.degree' => [
                'required_with:educations',
                'string',
                'max:255',
            ],

            'educations.*.field_of_study' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'educations.*.start_date' => [
                'required_with:educations',
                'date',
                'before_or_equal:today',
            ],

            'educations.*.end_date' => [
                'sometimes',
                'date',
                'after:educations.*.start_date',
            ],

            'educations.*.is_current' => [
                'sometimes',
                'boolean',
            ],

            'educations.*.gpa' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:4.0',
            ],

            'educations.*.honors' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'educations.*.activities' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Certifications
            'certifications' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'certifications.*.name' => [
                'required_with:certifications',
                'string',
                'max:255',
            ],

            'certifications.*.issuing_organization' => [
                'required_with:certifications',
                'string',
                'max:255',
            ],

            'certifications.*.issue_date' => [
                'required_with:certifications',
                'date',
                'before_or_equal:today',
            ],

            'certifications.*.expiry_date' => [
                'sometimes',
                'date',
                'after:certifications.*.issue_date',
            ],

            'certifications.*.credential_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'certifications.*.credential_url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            // Projects
            'projects' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'projects.*.name' => [
                'required_with:projects',
                'string',
                'max:255',
            ],

            'projects.*.description' => [
                'required_with:projects',
                'string',
                'max:2000',
            ],

            'projects.*.start_date' => [
                'required_with:projects',
                'date',
                'before_or_equal:today',
            ],

            'projects.*.end_date' => [
                'sometimes',
                'date',
                'after:projects.*.start_date',
                'before_or_equal:today',
            ],

            'projects.*.is_ongoing' => [
                'sometimes',
                'boolean',
            ],

            'projects.*.technologies' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'projects.*.technologies.*' => [
                'string',
                'max:100',
            ],

            'projects.*.url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            'projects.*.github_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?github\.com\/[a-zA-Z0-9\-_]+\/[a-zA-Z0-9\-_]+\/?$/',
            ],

            // Awards and achievements
            'awards' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'awards.*.title' => [
                'required_with:awards',
                'string',
                'max:255',
            ],

            'awards.*.issuer' => [
                'required_with:awards',
                'string',
                'max:255',
            ],

            'awards.*.date' => [
                'required_with:awards',
                'date',
                'before_or_equal:today',
            ],

            'awards.*.description' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Publications
            'publications' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'publications.*.title' => [
                'required_with:publications',
                'string',
                'max:255',
            ],

            'publications.*.publisher' => [
                'required_with:publications',
                'string',
                'max:255',
            ],

            'publications.*.publication_date' => [
                'required_with:publications',
                'date',
                'before_or_equal:today',
            ],

            'publications.*.url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            'publications.*.description' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Volunteer experience
            'volunteer_experiences' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'volunteer_experiences.*.organization' => [
                'required_with:volunteer_experiences',
                'string',
                'max:255',
            ],

            'volunteer_experiences.*.role' => [
                'required_with:volunteer_experiences',
                'string',
                'max:255',
            ],

            'volunteer_experiences.*.start_date' => [
                'required_with:volunteer_experiences',
                'date',
                'before_or_equal:today',
            ],

            'volunteer_experiences.*.end_date' => [
                'sometimes',
                'date',
                'after:volunteer_experiences.*.start_date',
                'before_or_equal:today',
            ],

            'volunteer_experiences.*.is_current' => [
                'sometimes',
                'boolean',
            ],

            'volunteer_experiences.*.description' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // References
            'references' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'references.*.name' => [
                'required_with:references',
                'string',
                'max:100',
            ],

            'references.*.company' => [
                'required_with:references',
                'string',
                'max:255',
            ],

            'references.*.position' => [
                'required_with:references',
                'string',
                'max:255',
            ],

            'references.*.email' => [
                'required_with:references',
                'email',
                'max:255',
            ],

            'references.*.phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            'references.*.relationship' => [
                'required_with:references',
                'string',
                Rule::in(['supervisor', 'colleague', 'client', 'mentor', 'other']),
            ],

            // Salary and preferences
            'expected_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'salary_currency_id' => [
                'required_with:expected_salary',
                'integer',
                'exists:salary_currencies,id',
            ],

            'salary_period' => [
                'required_with:expected_salary',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'yearly']),
            ],

            'job_types' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'job_types.*' => [
                'integer',
                'exists:job_types,id',
            ],

            'preferred_locations' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'preferred_locations.*' => [
                'integer',
                'exists:cities,id',
            ],

            'remote_work_preference' => [
                'sometimes',
                'string',
                Rule::in(['no_remote', 'hybrid', 'fully_remote', 'flexible']),
            ],

            'willing_to_relocate' => [
                'sometimes',
                'boolean',
            ],

            'travel_willingness' => [
                'sometimes',
                'string',
                Rule::in(['none', 'minimal', 'moderate', 'extensive']),
            ],

            'availability' => [
                'sometimes',
                'string',
                Rule::in(['immediate', 'two_weeks', 'one_month', 'three_months', 'negotiable']),
            ],

            // Privacy and visibility
            'is_public' => [
                'sometimes',
                'boolean',
            ],

            'is_searchable' => [
                'sometimes',
                'boolean',
            ],

            'show_contact_info' => [
                'sometimes',
                'boolean',
            ],

            'allow_download' => [
                'sometimes',
                'boolean',
            ],

            // Status and metadata
            'status' => [
                'sometimes',
                'string',
                Rule::in(['draft', 'active', 'inactive', 'archived']),
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],

            'version' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'tags' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],

            'notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // SEO and optimization
            'keywords' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'keywords.*' => [
                'string',
                'max:100',
            ],

            'meta_description' => [
                'sometimes',
                'string',
                'max:160',
            ],

            // Analytics and tracking
            'view_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'download_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'last_viewed_at' => [
                'sometimes',
                'date',
            ],

            'last_updated_at' => [
                'sometimes',
                'date',
            ],

            // Integration fields
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

            'import_source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'sync_status' => [
                'sometimes',
                'string',
                Rule::in(['pending', 'synced', 'failed', 'disabled']),
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
            'title.required' => __('validation.required_field', ['field' => __('validation.attributes.resume_title')]),
            'title.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.resume_title'), 'min' => 5]),
            'title.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.resume_title'), 'max' => 255]),

            'description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.description'), 'max' => 2000]),

            'file.required' => __('validation.required_field', ['field' => __('validation.attributes.resume_file')]),
            'file.file' => __('validation.file', ['attribute' => __('validation.attributes.resume_file')]),
            'file.mimes' => __('validation.mimes', ['attribute' => __('validation.attributes.resume_file'), 'values' => 'PDF, DOC, DOCX']),
            'file.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.resume_file'), 'max' => '10MB']),

            'professional_summary.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.professional_summary'), 'max' => 1000]),
            'objective.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.objective'), 'max' => 500]),

            'career_level.in' => __('validation.invalid_career_level'),
            'years_of_experience.min' => __('validation.min_value', ['attribute' => __('validation.attributes.years_of_experience'), 'min' => 0]),
            'years_of_experience.max' => __('validation.max_value', ['attribute' => __('validation.attributes.years_of_experience'), 'max' => 50]),

            'industry_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.industry')]),
            'functional_area_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.functional_area')]),

            'skills.array' => __('validation.array', ['attribute' => __('validation.attributes.skills')]),
            'skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.skills'), 'max' => 50]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.skill')]),

            'technical_skills.array' => __('validation.array', ['attribute' => __('validation.attributes.technical_skills')]),
            'technical_skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.technical_skills'), 'max' => 30]),
            'technical_skills.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.technical_skill'), 'max' => 100]),

            'soft_skills.array' => __('validation.array', ['attribute' => __('validation.attributes.soft_skills')]),
            'soft_skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.soft_skills'), 'max' => 20]),
            'soft_skills.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.soft_skill'), 'max' => 100]),

            'languages.array' => __('validation.array', ['attribute' => __('validation.attributes.languages')]),
            'languages.max' => __('validation.max_items', ['attribute' => __('validation.attributes.languages'), 'max' => 10]),
            'languages.*.language_id.required_with' => __('validation.language_id_required'),
            'languages.*.language_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.language')]),
            'languages.*.proficiency.required_with' => __('validation.language_proficiency_required'),
            'languages.*.proficiency.in' => __('validation.invalid_language_proficiency'),

            'work_experiences.array' => __('validation.array', ['attribute' => __('validation.attributes.work_experiences')]),
            'work_experiences.max' => __('validation.max_items', ['attribute' => __('validation.attributes.work_experiences'), 'max' => 20]),
            'work_experiences.*.company_name.required_with' => __('validation.company_name_required'),
            'work_experiences.*.job_title.required_with' => __('validation.job_title_required'),
            'work_experiences.*.start_date.required_with' => __('validation.start_date_required'),
            'work_experiences.*.start_date.before_or_equal' => __('validation.start_date_future'),
            'work_experiences.*.end_date.after' => __('validation.end_date_before_start'),
            'work_experiences.*.end_date.before_or_equal' => __('validation.end_date_future'),
            'work_experiences.*.description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.work_description'), 'max' => 2000]),
            'work_experiences.*.employment_type.in' => __('validation.invalid_employment_type'),

            'educations.array' => __('validation.array', ['attribute' => __('validation.attributes.educations')]),
            'educations.max' => __('validation.max_items', ['attribute' => __('validation.attributes.educations'), 'max' => 10]),
            'educations.*.institution.required_with' => __('validation.institution_required'),
            'educations.*.degree.required_with' => __('validation.degree_required'),
            'educations.*.start_date.required_with' => __('validation.start_date_required'),
            'educations.*.end_date.after' => __('validation.end_date_before_start'),
            'educations.*.gpa.min' => __('validation.min_value', ['attribute' => __('validation.attributes.gpa'), 'min' => 0]),
            'educations.*.gpa.max' => __('validation.max_value', ['attribute' => __('validation.attributes.gpa'), 'max' => 4.0]),

            'certifications.array' => __('validation.array', ['attribute' => __('validation.attributes.certifications')]),
            'certifications.max' => __('validation.max_items', ['attribute' => __('validation.attributes.certifications'), 'max' => 20]),
            'certifications.*.name.required_with' => __('validation.certification_name_required'),
            'certifications.*.issuing_organization.required_with' => __('validation.issuing_organization_required'),
            'certifications.*.issue_date.required_with' => __('validation.issue_date_required'),
            'certifications.*.expiry_date.after' => __('validation.expiry_date_before_issue'),
            'certifications.*.credential_url.url' => __('validation.url', ['attribute' => __('validation.attributes.credential_url')]),

            'projects.array' => __('validation.array', ['attribute' => __('validation.attributes.projects')]),
            'projects.max' => __('validation.max_items', ['attribute' => __('validation.attributes.projects'), 'max' => 15]),
            'projects.*.name.required_with' => __('validation.project_name_required'),
            'projects.*.description.required_with' => __('validation.project_description_required'),
            'projects.*.start_date.required_with' => __('validation.start_date_required'),
            'projects.*.end_date.after' => __('validation.end_date_before_start'),
            'projects.*.url.url' => __('validation.url', ['attribute' => __('validation.attributes.project_url')]),
            'projects.*.github_url.url' => __('validation.url', ['attribute' => __('validation.attributes.github_url')]),
            'projects.*.github_url.regex' => __('validation.invalid_github_url'),

            'awards.array' => __('validation.array', ['attribute' => __('validation.attributes.awards')]),
            'awards.max' => __('validation.max_items', ['attribute' => __('validation.attributes.awards'), 'max' => 10]),
            'awards.*.title.required_with' => __('validation.award_title_required'),
            'awards.*.issuer.required_with' => __('validation.award_issuer_required'),
            'awards.*.date.required_with' => __('validation.award_date_required'),

            'publications.array' => __('validation.array', ['attribute' => __('validation.attributes.publications')]),
            'publications.max' => __('validation.max_items', ['attribute' => __('validation.attributes.publications'), 'max' => 10]),
            'publications.*.title.required_with' => __('validation.publication_title_required'),
            'publications.*.publisher.required_with' => __('validation.publisher_required'),
            'publications.*.publication_date.required_with' => __('validation.publication_date_required'),
            'publications.*.url.url' => __('validation.url', ['attribute' => __('validation.attributes.publication_url')]),

            'volunteer_experiences.array' => __('validation.array', ['attribute' => __('validation.attributes.volunteer_experiences')]),
            'volunteer_experiences.max' => __('validation.max_items', ['attribute' => __('validation.attributes.volunteer_experiences'), 'max' => 10]),
            'volunteer_experiences.*.organization.required_with' => __('validation.volunteer_organization_required'),
            'volunteer_experiences.*.role.required_with' => __('validation.volunteer_role_required'),
            'volunteer_experiences.*.start_date.required_with' => __('validation.start_date_required'),
            'volunteer_experiences.*.end_date.after' => __('validation.end_date_before_start'),

            'references.array' => __('validation.array', ['attribute' => __('validation.attributes.references')]),
            'references.max' => __('validation.max_items', ['attribute' => __('validation.attributes.references'), 'max' => 5]),
            'references.*.name.required_with' => __('validation.reference_name_required'),
            'references.*.company.required_with' => __('validation.reference_company_required'),
            'references.*.position.required_with' => __('validation.reference_position_required'),
            'references.*.email.required_with' => __('validation.reference_email_required'),
            'references.*.email.email' => __('validation.email', ['attribute' => __('validation.attributes.reference_email')]),
            'references.*.phone.regex' => __('validation.invalid_phone_format'),
            'references.*.relationship.in' => __('validation.invalid_reference_relationship'),

            'expected_salary.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.expected_salary')]),
            'expected_salary.max' => __('validation.max_value', ['attribute' => __('validation.attributes.expected_salary'), 'max' => 10000000]),
            'salary_currency_id.required_with' => __('validation.salary_currency_required'),
            'salary_currency_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.salary_currency')]),
            'salary_period.required_with' => __('validation.salary_period_required'),
            'salary_period.in' => __('validation.invalid_salary_period'),

            'job_types.array' => __('validation.array', ['attribute' => __('validation.attributes.job_types')]),
            'job_types.max' => __('validation.max_items', ['attribute' => __('validation.attributes.job_types'), 'max' => 10]),
            'job_types.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_type')]),

            'preferred_locations.array' => __('validation.array', ['attribute' => __('validation.attributes.preferred_locations')]),
            'preferred_locations.max' => __('validation.max_items', ['attribute' => __('validation.attributes.preferred_locations'), 'max' => 20]),
            'preferred_locations.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.location')]),

            'remote_work_preference.in' => __('validation.invalid_remote_work_preference'),
            'travel_willingness.in' => __('validation.invalid_travel_willingness'),
            'availability.in' => __('validation.invalid_availability'),

            'status.in' => __('validation.invalid_status'),

            'tags.array' => __('validation.array', ['attribute' => __('validation.attributes.tags')]),
            'tags.max' => __('validation.max_items', ['attribute' => __('validation.attributes.tags'), 'max' => 20]),
            'tags.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.tag'), 'max' => 50]),
            'tags.*.regex' => __('validation.invalid_tag_format'),

            'notes.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.notes'), 'max' => 1000]),

            'keywords.array' => __('validation.array', ['attribute' => __('validation.attributes.keywords')]),
            'keywords.max' => __('validation.max_items', ['attribute' => __('validation.attributes.keywords'), 'max' => 30]),
            'keywords.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.keyword'), 'max' => 100]),

            'meta_description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.meta_description'), 'max' => 160]),

            'sync_status.in' => __('validation.invalid_sync_status'),
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
            'title' => __('validation.attributes.resume_title'),
            'description' => __('validation.attributes.description'),
            'file' => __('validation.attributes.resume_file'),
            'file_name' => __('validation.attributes.file_name'),
            'file_size' => __('validation.attributes.file_size'),
            'professional_summary' => __('validation.attributes.professional_summary'),
            'objective' => __('validation.attributes.objective'),
            'career_level' => __('validation.attributes.career_level'),
            'years_of_experience' => __('validation.attributes.years_of_experience'),
            'industry_id' => __('validation.attributes.industry'),
            'functional_area_id' => __('validation.attributes.functional_area'),
            'skills' => __('validation.attributes.skills'),
            'technical_skills' => __('validation.attributes.technical_skills'),
            'soft_skills' => __('validation.attributes.soft_skills'),
            'languages' => __('validation.attributes.languages'),
            'work_experiences' => __('validation.attributes.work_experiences'),
            'educations' => __('validation.attributes.educations'),
            'certifications' => __('validation.attributes.certifications'),
            'projects' => __('validation.attributes.projects'),
            'awards' => __('validation.attributes.awards'),
            'publications' => __('validation.attributes.publications'),
            'volunteer_experiences' => __('validation.attributes.volunteer_experiences'),
            'references' => __('validation.attributes.references'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'salary_currency_id' => __('validation.attributes.salary_currency'),
            'salary_period' => __('validation.attributes.salary_period'),
            'job_types' => __('validation.attributes.job_types'),
            'preferred_locations' => __('validation.attributes.preferred_locations'),
            'remote_work_preference' => __('validation.attributes.remote_work_preference'),
            'willing_to_relocate' => __('validation.attributes.willing_to_relocate'),
            'travel_willingness' => __('validation.attributes.travel_willingness'),
            'availability' => __('validation.attributes.availability'),
            'is_public' => __('validation.attributes.is_public'),
            'is_searchable' => __('validation.attributes.is_searchable'),
            'show_contact_info' => __('validation.attributes.show_contact_info'),
            'allow_download' => __('validation.attributes.allow_download'),
            'status' => __('validation.attributes.status'),
            'is_primary' => __('validation.attributes.is_primary'),
            'version' => __('validation.attributes.version'),
            'tags' => __('validation.attributes.tags'),
            'notes' => __('validation.attributes.notes'),
            'keywords' => __('validation.attributes.keywords'),
            'meta_description' => __('validation.attributes.meta_description'),
            'view_count' => __('validation.attributes.view_count'),
            'download_count' => __('validation.attributes.download_count'),
            'last_viewed_at' => __('validation.attributes.last_viewed_at'),
            'last_updated_at' => __('validation.attributes.last_updated_at'),
            'external_id' => __('validation.attributes.external_id'),
            'source' => __('validation.attributes.source'),
            'import_source' => __('validation.attributes.import_source'),
            'sync_status' => __('validation.attributes.sync_status'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_public' => $this->boolean('is_public', true),
            'is_searchable' => $this->boolean('is_searchable', true),
            'show_contact_info' => $this->boolean('show_contact_info', true),
            'allow_download' => $this->boolean('allow_download', false),
            'is_primary' => $this->boolean('is_primary', false),
            'willing_to_relocate' => $this->boolean('willing_to_relocate', false),
            'status' => $this->status ?? 'active',
            'remote_work_preference' => $this->remote_work_preference ?? 'flexible',
            'travel_willingness' => $this->travel_willingness ?? 'minimal',
            'availability' => $this->availability ?? 'negotiable',
            'salary_period' => $this->salary_period ?? 'yearly',
            'view_count' => $this->integer('view_count', 0),
            'download_count' => $this->integer('download_count', 0),
            'sync_status' => $this->sync_status ?? 'pending',
        ]);

        // Process file information
        if ($this->hasFile('file')) {
            $file = $this->file('file');
            $this->merge([
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }

        // Process arrays from strings
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('intval', explode(',', $this->skills))),
            ]);
        }

        if ($this->has('technical_skills') && is_string($this->technical_skills)) {
            $this->merge([
                'technical_skills' => array_filter(array_map('trim', explode(',', $this->technical_skills))),
            ]);
        }

        if ($this->has('soft_skills') && is_string($this->soft_skills)) {
            $this->merge([
                'soft_skills' => array_filter(array_map('trim', explode(',', $this->soft_skills))),
            ]);
        }

        if ($this->has('job_types') && is_string($this->job_types)) {
            $this->merge([
                'job_types' => array_filter(array_map('intval', explode(',', $this->job_types))),
            ]);
        }

        if ($this->has('preferred_locations') && is_string($this->preferred_locations)) {
            $this->merge([
                'preferred_locations' => array_filter(array_map('intval', explode(',', $this->preferred_locations))),
            ]);
        }

        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }

        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => array_filter(array_map('trim', explode(',', $this->keywords))),
            ]);
        }

        // Process JSON arrays
        $jsonFields = ['languages', 'work_experiences', 'educations', 'certifications', 'projects', 'awards', 'publications', 'volunteer_experiences', 'references'];

        foreach ($jsonFields as $field) {
            if ($this->has($field) && is_string($this->$field)) {
                try {
                    $data = json_decode($this->$field, true);
                    if (is_array($data)) {
                        $this->merge([$field => $data]);
                    }
                } catch (\Exception $e) {
                    // Keep as string if JSON decode fails
                }
            }
        }

        // Log resume creation attempt
        Log::info('Resume creation attempt', [
            'title' => $this->title,
            'career_level' => $this->career_level,
            'years_of_experience' => $this->years_of_experience,
            'industry_id' => $this->industry_id,
            'file_size' => $this->file_size,
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
            'resume_id' => 'RESUME-'.date('Ymd').'-'.strtoupper(substr(md5($this->title.time()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Set processing flags
        $this->merge([
            'requires_parsing' => $this->shouldRequireParsing(),
            'auto_publish' => $this->shouldAutoPublish(),
            'generate_preview' => true,
            'extract_keywords' => true,
        ]);
    }

    /**
     * Determine if resume parsing is required.
     */
    private function shouldRequireParsing(): bool
    {
        return $this->hasFile('file') &&
               in_array($this->file('file')->getClientOriginalExtension(), ['pdf', 'doc', 'docx']);
    }

    /**
     * Determine if resume should be auto-published.
     */
    private function shouldAutoPublish(): bool
    {
        return $this->is_public &&
               $this->status === 'active' &&
               ! empty($this->professional_summary);
    }
}
