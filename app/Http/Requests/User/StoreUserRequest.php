<?php

namespace App\Http\Requests\User;

use App\Models\City;
use App\Models\State;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Log;

class StoreUserRequest extends FormRequest
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
            // Basic user information
            'first_name' => [
                'required',
                'string',
                'max:100',
                'min:2',
                'regex:/^[a-zA-Z\s\-\.\']+$/',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
                'min:2',
                'regex:/^[a-zA-Z\s\-\.\']+$/',
            ],

            'username' => [
                'sometimes',
                'string',
                'max:50',
                'min:3',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidEmailDomain($value)) {
                        $fail(__('validation.invalid_email_domain'));
                    }
                },
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'unique:users,phone',
            ],

            'date_of_birth' => [
                'sometimes',
                'date',
                'before:' . now()->subYears(13)->toDateString(),
                'after:' . now()->subYears(100)->toDateString(),
            ],

            'gender' => [
                'sometimes',
                'string',
                Rule::in(['male', 'female', 'other', 'prefer_not_to_say']),
            ],

            // Address information
            'address' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'city_id' => [
                'sometimes',
                'integer',
                'exists:cities,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
            ],

            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],

            'postal_code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\s\-]+$/i',
            ],

            // Professional information
            'job_title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'company_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'industry_id' => [
                'sometimes',
                'integer',
                'exists:industries,id',
            ],

            'experience_level' => [
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

            'current_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'expected_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'salary_currency_id' => [
                'required_with:current_salary,expected_salary',
                'integer',
                'exists:salary_currencies,id',
            ],

            'salary_period' => [
                'required_with:current_salary,expected_salary',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'yearly']),
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
                'max:255',
            ],

            // Education
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

            'field_of_study' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'university' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'graduation_year' => [
                'sometimes',
                'integer',
                'min:1950',
                'max:' . (date('Y') + 10),
            ],

            'gpa' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:4.0',
            ],

            // Profile and preferences
            'bio' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'summary' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'website' => [
                'sometimes',
                'url',
                'max:255',
            ],

            'linkedin_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?linkedin\.com\/in\/[a-zA-Z0-9\-]+\/?$/',
            ],

            'github_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?github\.com\/[a-zA-Z0-9\-_]+\/?$/',
            ],

            'portfolio_url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            // Job preferences
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

            // File uploads
            'avatar' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],

            'resume' => [
                'sometimes',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240', // 10MB
            ],

            'cover_letter' => [
                'sometimes',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB
            ],

            'portfolio_files' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'portfolio_files.*' => [
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip',
                'max:20480', // 20MB
            ],

            // Privacy and notification settings
            'profile_visibility' => [
                'sometimes',
                'string',
                Rule::in(['public', 'private', 'recruiters_only', 'connections_only']),
            ],

            'show_contact_info' => [
                'sometimes',
                'boolean',
            ],

            'show_salary_expectations' => [
                'sometimes',
                'boolean',
            ],

            'allow_recruiter_contact' => [
                'sometimes',
                'boolean',
            ],

            'email_notifications' => [
                'sometimes',
                'boolean',
            ],

            'sms_notifications' => [
                'sometimes',
                'boolean',
            ],

            'job_alert_frequency' => [
                'sometimes',
                'string',
                Rule::in(['immediate', 'daily', 'weekly', 'monthly', 'never']),
            ],

            'newsletter_subscription' => [
                'sometimes',
                'boolean',
            ],

            // Social media
            'twitter_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?twitter\.com\/[a-zA-Z0-9_]+\/?$/',
            ],

            'facebook_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?facebook\.com\/[a-zA-Z0-9\.\-]+\/?$/',
            ],

            'instagram_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9\._]+\/?$/',
            ],

            // Account settings
            'timezone' => [
                'sometimes',
                'string',
                'max:100',
                Rule::in(timezone_identifiers_list()),
            ],

            'language_preference' => [
                'sometimes',
                'string',
                'size:2',
                Rule::in(['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ar', 'zh']),
            ],

            'currency_preference' => [
                'sometimes',
                'string',
                'size:3',
                'exists:currencies,code',
            ],

            'date_format' => [
                'sometimes',
                'string',
                Rule::in(['Y-m-d', 'm/d/Y', 'd/m/Y', 'd-m-Y']),
            ],

            'time_format' => [
                'sometimes',
                'string',
                Rule::in(['24', '12']),
            ],

            // Verification and compliance
            'email_verified' => [
                'sometimes',
                'boolean',
            ],

            'phone_verified' => [
                'sometimes',
                'boolean',
            ],

            'identity_verified' => [
                'sometimes',
                'boolean',
            ],

            'background_check_consent' => [
                'sometimes',
                'boolean',
            ],

            'terms_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],

            'privacy_policy_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],

            'marketing_consent' => [
                'sometimes',
                'boolean',
            ],

            'data_processing_consent' => [
                'required',
                'boolean',
                'accepted',
            ],

            // Professional references
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
            ],

            'references.*.relationship' => [
                'required_with:references',
                'string',
                Rule::in(['supervisor', 'colleague', 'client', 'mentor', 'other']),
            ],

            // Emergency contact
            'emergency_contact_name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'emergency_contact_relationship' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'emergency_contact_phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            // Additional metadata
            'custom_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'custom_fields.*' => [
                'string',
                'max:500',
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

            'notes' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Status and flags
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'pending', 'suspended', 'archived']),
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'is_premium' => [
                'sometimes',
                'boolean',
            ],

            'priority_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            // Integration settings
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

            'referral_code' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'utm_source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_medium' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_campaign' => [
                'sometimes',
                'string',
                'max:100',
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
            'first_name.required' => __('validation.required_field', ['field' => __('validation.attributes.first_name')]),
            'first_name.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.first_name'), 'min' => 2]),
            'first_name.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.first_name'), 'max' => 100]),
            'first_name.regex' => __('validation.invalid_name_format'),
            
            'last_name.required' => __('validation.required_field', ['field' => __('validation.attributes.last_name')]),
            'last_name.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.last_name'), 'min' => 2]),
            'last_name.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.last_name'), 'max' => 100]),
            'last_name.regex' => __('validation.invalid_name_format'),
            
            'username.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.username'), 'min' => 3]),
            'username.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.username'), 'max' => 50]),
            'username.regex' => __('validation.invalid_username_format'),
            'username.unique' => __('validation.unique', ['attribute' => __('validation.attributes.username')]),
            
            'email.required' => __('validation.required_field', ['field' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            
            'phone.regex' => __('validation.invalid_phone_format'),
            'phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
            
            'date_of_birth.before' => __('validation.minimum_age_required'),
            'date_of_birth.after' => __('validation.invalid_birth_date'),
            
            'gender.in' => __('validation.invalid_gender'),
            
            'city_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.city')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.state')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),
            
            'postal_code.regex' => __('validation.invalid_postal_code'),
            
            'industry_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.industry')]),
            
            'experience_level.in' => __('validation.invalid_experience_level'),
            
            'years_of_experience.min' => __('validation.min_value', ['attribute' => __('validation.attributes.years_of_experience'), 'min' => 0]),
            'years_of_experience.max' => __('validation.max_value', ['attribute' => __('validation.attributes.years_of_experience'), 'max' => 50]),
            
            'current_salary.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.current_salary')]),
            'current_salary.max' => __('validation.max_value', ['attribute' => __('validation.attributes.current_salary'), 'max' => 10000000]),
            
            'expected_salary.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.expected_salary')]),
            'expected_salary.max' => __('validation.max_value', ['attribute' => __('validation.attributes.expected_salary'), 'max' => 10000000]),
            
            'salary_currency_id.required_with' => __('validation.salary_currency_required'),
            'salary_currency_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.salary_currency')]),
            
            'salary_period.required_with' => __('validation.salary_period_required'),
            'salary_period.in' => __('validation.invalid_salary_period'),
            
            'skills.array' => __('validation.array', ['attribute' => __('validation.attributes.skills')]),
            'skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.skills'), 'max' => 50]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.skill')]),
            
            'languages.array' => __('validation.array', ['attribute' => __('validation.attributes.languages')]),
            'languages.max' => __('validation.max_items', ['attribute' => __('validation.attributes.languages'), 'max' => 10]),
            'languages.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.language')]),
            
            'certifications.array' => __('validation.array', ['attribute' => __('validation.attributes.certifications')]),
            'certifications.max' => __('validation.max_items', ['attribute' => __('validation.attributes.certifications'), 'max' => 20]),
            
            'education_level_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.education_level')]),
            'degree_level_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.degree_level')]),
            
            'graduation_year.min' => __('validation.min_value', ['attribute' => __('validation.attributes.graduation_year'), 'min' => 1950]),
            'graduation_year.max' => __('validation.max_value', ['attribute' => __('validation.attributes.graduation_year'), 'max' => date('Y') + 10]),
            
            'gpa.min' => __('validation.min_value', ['attribute' => __('validation.attributes.gpa'), 'min' => 0]),
            'gpa.max' => __('validation.max_value', ['attribute' => __('validation.attributes.gpa'), 'max' => 4.0]),
            
            'bio.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.bio'), 'max' => 2000]),
            'summary.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.summary'), 'max' => 1000]),
            
            'website.url' => __('validation.url', ['attribute' => __('validation.attributes.website')]),
            
            'linkedin_url.url' => __('validation.url', ['attribute' => __('validation.attributes.linkedin_url')]),
            'linkedin_url.regex' => __('validation.invalid_linkedin_url'),
            
            'github_url.url' => __('validation.url', ['attribute' => __('validation.attributes.github_url')]),
            'github_url.regex' => __('validation.invalid_github_url'),
            
            'portfolio_url.url' => __('validation.url', ['attribute' => __('validation.attributes.portfolio_url')]),
            
            'job_types.array' => __('validation.array', ['attribute' => __('validation.attributes.job_types')]),
            'job_types.max' => __('validation.max_items', ['attribute' => __('validation.attributes.job_types'), 'max' => 10]),
            'job_types.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_type')]),
            
            'preferred_locations.array' => __('validation.array', ['attribute' => __('validation.attributes.preferred_locations')]),
            'preferred_locations.max' => __('validation.max_items', ['attribute' => __('validation.attributes.preferred_locations'), 'max' => 20]),
            'preferred_locations.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.location')]),
            
            'remote_work_preference.in' => __('validation.invalid_remote_work_preference'),
            'travel_willingness.in' => __('validation.invalid_travel_willingness'),
            'availability.in' => __('validation.invalid_availability'),
            
            'avatar.image' => __('validation.image', ['attribute' => __('validation.attributes.avatar')]),
            'avatar.mimes' => __('validation.mimes', ['attribute' => __('validation.attributes.avatar'), 'values' => 'jpeg, png, jpg, gif, webp']),
            'avatar.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.avatar'), 'max' => '5MB']),
            'avatar.dimensions' => __('validation.avatar_dimensions'),
            
            'resume.file' => __('validation.file', ['attribute' => __('validation.attributes.resume')]),
            'resume.mimes' => __('validation.mimes', ['attribute' => __('validation.attributes.resume'), 'values' => 'pdf, doc, docx']),
            'resume.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.resume'), 'max' => '10MB']),
            
            'cover_letter.file' => __('validation.file', ['attribute' => __('validation.attributes.cover_letter')]),
            'cover_letter.mimes' => __('validation.mimes', ['attribute' => __('validation.attributes.cover_letter'), 'values' => 'pdf, doc, docx']),
            'cover_letter.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.cover_letter'), 'max' => '5MB']),
            
            'portfolio_files.array' => __('validation.array', ['attribute' => __('validation.attributes.portfolio_files')]),
            'portfolio_files.max' => __('validation.max_items', ['attribute' => __('validation.attributes.portfolio_files'), 'max' => 10]),
            'portfolio_files.*.file' => __('validation.file', ['attribute' => __('validation.attributes.portfolio_file')]),
            'portfolio_files.*.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.portfolio_file'), 'max' => '20MB']),
            
            'profile_visibility.in' => __('validation.invalid_profile_visibility'),
            'job_alert_frequency.in' => __('validation.invalid_job_alert_frequency'),
            
            'twitter_url.url' => __('validation.url', ['attribute' => __('validation.attributes.twitter_url')]),
            'twitter_url.regex' => __('validation.invalid_twitter_url'),
            
            'facebook_url.url' => __('validation.url', ['attribute' => __('validation.attributes.facebook_url')]),
            'facebook_url.regex' => __('validation.invalid_facebook_url'),
            
            'instagram_url.url' => __('validation.url', ['attribute' => __('validation.attributes.instagram_url')]),
            'instagram_url.regex' => __('validation.invalid_instagram_url'),
            
            'timezone.in' => __('validation.invalid_timezone'),
            'language_preference.in' => __('validation.invalid_language_preference'),
            'currency_preference.exists' => __('validation.exists', ['attribute' => __('validation.attributes.currency')]),
            'date_format.in' => __('validation.invalid_date_format'),
            'time_format.in' => __('validation.invalid_time_format'),
            
            'terms_accepted.required' => __('validation.terms_required'),
            'terms_accepted.accepted' => __('validation.terms_must_accept'),
            
            'privacy_policy_accepted.required' => __('validation.privacy_policy_required'),
            'privacy_policy_accepted.accepted' => __('validation.privacy_policy_must_accept'),
            
            'data_processing_consent.required' => __('validation.data_processing_required'),
            'data_processing_consent.accepted' => __('validation.data_processing_must_accept'),
            
            'references.array' => __('validation.array', ['attribute' => __('validation.attributes.references')]),
            'references.max' => __('validation.max_items', ['attribute' => __('validation.attributes.references'), 'max' => 5]),
            'references.*.name.required_with' => __('validation.reference_name_required'),
            'references.*.company.required_with' => __('validation.reference_company_required'),
            'references.*.position.required_with' => __('validation.reference_position_required'),
            'references.*.email.required_with' => __('validation.reference_email_required'),
            'references.*.email.email' => __('validation.email', ['attribute' => __('validation.attributes.reference_email')]),
            'references.*.relationship.in' => __('validation.invalid_reference_relationship'),
            
            'emergency_contact_phone.regex' => __('validation.invalid_phone_format'),
            
            'custom_fields.array' => __('validation.array', ['attribute' => __('validation.attributes.custom_fields')]),
            'custom_fields.max' => __('validation.max_items', ['attribute' => __('validation.attributes.custom_fields'), 'max' => 20]),
            'custom_fields.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.custom_field'), 'max' => 500]),
            
            'tags.array' => __('validation.array', ['attribute' => __('validation.attributes.tags')]),
            'tags.max' => __('validation.max_items', ['attribute' => __('validation.attributes.tags'), 'max' => 30]),
            'tags.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.tag'), 'max' => 50]),
            'tags.*.regex' => __('validation.invalid_tag_format'),
            
            'notes.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.notes'), 'max' => 2000]),
            
            'status.in' => __('validation.invalid_status'),
            
            'priority_score.min' => __('validation.min_value', ['attribute' => __('validation.attributes.priority_score'), 'min' => 0]),
            'priority_score.max' => __('validation.max_value', ['attribute' => __('validation.attributes.priority_score'), 'max' => 100]),
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
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'username' => __('validation.attributes.username'),
            'email' => __('validation.attributes.email'),
            'phone' => __('validation.attributes.phone'),
            'date_of_birth' => __('validation.attributes.date_of_birth'),
            'gender' => __('validation.attributes.gender'),
            'address' => __('validation.attributes.address'),
            'city_id' => __('validation.attributes.city'),
            'state_id' => __('validation.attributes.state'),
            'country_id' => __('validation.attributes.country'),
            'postal_code' => __('validation.attributes.postal_code'),
            'job_title' => __('validation.attributes.job_title'),
            'company_name' => __('validation.attributes.company_name'),
            'industry_id' => __('validation.attributes.industry'),
            'experience_level' => __('validation.attributes.experience_level'),
            'years_of_experience' => __('validation.attributes.years_of_experience'),
            'current_salary' => __('validation.attributes.current_salary'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'salary_currency_id' => __('validation.attributes.salary_currency'),
            'salary_period' => __('validation.attributes.salary_period'),
            'skills' => __('validation.attributes.skills'),
            'languages' => __('validation.attributes.languages'),
            'certifications' => __('validation.attributes.certifications'),
            'education_level_id' => __('validation.attributes.education_level'),
            'degree_level_id' => __('validation.attributes.degree_level'),
            'field_of_study' => __('validation.attributes.field_of_study'),
            'university' => __('validation.attributes.university'),
            'graduation_year' => __('validation.attributes.graduation_year'),
            'gpa' => __('validation.attributes.gpa'),
            'bio' => __('validation.attributes.bio'),
            'summary' => __('validation.attributes.summary'),
            'website' => __('validation.attributes.website'),
            'linkedin_url' => __('validation.attributes.linkedin_url'),
            'github_url' => __('validation.attributes.github_url'),
            'portfolio_url' => __('validation.attributes.portfolio_url'),
            'job_types' => __('validation.attributes.job_types'),
            'preferred_locations' => __('validation.attributes.preferred_locations'),
            'remote_work_preference' => __('validation.attributes.remote_work_preference'),
            'willing_to_relocate' => __('validation.attributes.willing_to_relocate'),
            'travel_willingness' => __('validation.attributes.travel_willingness'),
            'availability' => __('validation.attributes.availability'),
            'avatar' => __('validation.attributes.avatar'),
            'resume' => __('validation.attributes.resume'),
            'cover_letter' => __('validation.attributes.cover_letter'),
            'portfolio_files' => __('validation.attributes.portfolio_files'),
            'profile_visibility' => __('validation.attributes.profile_visibility'),
            'show_contact_info' => __('validation.attributes.show_contact_info'),
            'show_salary_expectations' => __('validation.attributes.show_salary_expectations'),
            'allow_recruiter_contact' => __('validation.attributes.allow_recruiter_contact'),
            'email_notifications' => __('validation.attributes.email_notifications'),
            'sms_notifications' => __('validation.attributes.sms_notifications'),
            'job_alert_frequency' => __('validation.attributes.job_alert_frequency'),
            'newsletter_subscription' => __('validation.attributes.newsletter_subscription'),
            'twitter_url' => __('validation.attributes.twitter_url'),
            'facebook_url' => __('validation.attributes.facebook_url'),
            'instagram_url' => __('validation.attributes.instagram_url'),
            'timezone' => __('validation.attributes.timezone'),
            'language_preference' => __('validation.attributes.language_preference'),
            'currency_preference' => __('validation.attributes.currency_preference'),
            'date_format' => __('validation.attributes.date_format'),
            'time_format' => __('validation.attributes.time_format'),
            'email_verified' => __('validation.attributes.email_verified'),
            'phone_verified' => __('validation.attributes.phone_verified'),
            'identity_verified' => __('validation.attributes.identity_verified'),
            'background_check_consent' => __('validation.attributes.background_check_consent'),
            'terms_accepted' => __('validation.attributes.terms_accepted'),
            'privacy_policy_accepted' => __('validation.attributes.privacy_policy_accepted'),
            'marketing_consent' => __('validation.attributes.marketing_consent'),
            'data_processing_consent' => __('validation.attributes.data_processing_consent'),
            'references' => __('validation.attributes.references'),
            'emergency_contact_name' => __('validation.attributes.emergency_contact_name'),
            'emergency_contact_relationship' => __('validation.attributes.emergency_contact_relationship'),
            'emergency_contact_phone' => __('validation.attributes.emergency_contact_phone'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'tags' => __('validation.attributes.tags'),
            'notes' => __('validation.attributes.notes'),
            'status' => __('validation.attributes.status'),
            'is_featured' => __('validation.attributes.is_featured'),
            'is_premium' => __('validation.attributes.is_premium'),
            'priority_score' => __('validation.attributes.priority_score'),
            'external_id' => __('validation.attributes.external_id'),
            'source' => __('validation.attributes.source'),
            'referral_code' => __('validation.attributes.referral_code'),
            'utm_source' => __('validation.attributes.utm_source'),
            'utm_medium' => __('validation.attributes.utm_medium'),
            'utm_campaign' => __('validation.attributes.utm_campaign'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate username from email if not provided
        if (!$this->has('username') && $this->has('email')) {
            $this->merge([
                'username' => strtolower(explode('@', $this->email)[0]),
            ]);
        }

        // Set default values
        $this->merge([
            'email_verified' => $this->boolean('email_verified', false),
            'phone_verified' => $this->boolean('phone_verified', false),
            'identity_verified' => $this->boolean('identity_verified', false),
            'background_check_consent' => $this->boolean('background_check_consent', false),
            'show_contact_info' => $this->boolean('show_contact_info', true),
            'show_salary_expectations' => $this->boolean('show_salary_expectations', false),
            'allow_recruiter_contact' => $this->boolean('allow_recruiter_contact', true),
            'email_notifications' => $this->boolean('email_notifications', true),
            'sms_notifications' => $this->boolean('sms_notifications', false),
            'newsletter_subscription' => $this->boolean('newsletter_subscription', false),
            'willing_to_relocate' => $this->boolean('willing_to_relocate', false),
            'is_featured' => $this->boolean('is_featured', false),
            'is_premium' => $this->boolean('is_premium', false),
            'marketing_consent' => $this->boolean('marketing_consent', false),
            'status' => $this->status ?? 'active',
            'profile_visibility' => $this->profile_visibility ?? 'public',
            'remote_work_preference' => $this->remote_work_preference ?? 'flexible',
            'travel_willingness' => $this->travel_willingness ?? 'minimal',
            'availability' => $this->availability ?? 'negotiable',
            'job_alert_frequency' => $this->job_alert_frequency ?? 'weekly',
            'timezone' => $this->timezone ?? 'UTC',
            'language_preference' => $this->language_preference ?? 'en',
            'currency_preference' => $this->currency_preference ?? 'USD',
            'date_format' => $this->date_format ?? 'Y-m-d',
            'time_format' => $this->time_format ?? '24',
            'priority_score' => $this->integer('priority_score', 50),
        ]);

        // Process arrays from strings
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('intval', explode(',', $this->skills))),
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

        // Process references array
        if ($this->has('references') && is_string($this->references)) {
            try {
                $references = json_decode($this->references, true);
                if (is_array($references)) {
                    $this->merge(['references' => $references]);
                }
            } catch (\Exception $e) {
                // Keep as string if JSON decode fails
            }
        }

        // Normalize phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^\+0-9]/', '', $this->phone),
            ]);
        }

        // Log user registration attempt
        Log::info('User registration attempt', [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'industry_id' => $this->industry_id,
            'experience_level' => $this->experience_level,
            'country_id' => $this->country_id,
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
            'user_id' => 'USER-' . date('Ymd') . '-' . strtoupper(substr(md5($this->email . time()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Set processing flags
        $this->merge([
            'requires_verification' => $this->shouldRequireVerification(),
            'auto_approve' => $this->shouldAutoApprove(),
            'send_welcome_email' => true,
            'create_profile' => true,
        ]);
    }

    /**
     * Validate if email domain is acceptable.
     */
    private function isValidEmailDomain($email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        
        // Block known temporary email domains
        $blockedDomains = [
            '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
            'mailinator.com', 'throwaway.email', 'temp-mail.org'
        ];

        return !in_array(strtolower($domain), $blockedDomains);
    }

    /**
     * Determine if verification is required.
     */
    private function shouldRequireVerification(): bool
    {
        return $this->is_premium ||
               $this->allow_recruiter_contact ||
               $this->has('resume') ||
               $this->profile_visibility === 'recruiters_only';
    }

    /**
     * Determine if user should be auto-approved.
     */
    private function shouldAutoApprove(): bool
    {
        return !$this->is_premium &&
               !$this->shouldRequireVerification() &&
               $this->profile_visibility === 'public';
    }
}
