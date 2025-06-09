<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

/**
 * Class StoreCompanyRequest
 * 
 * Handles company creation requests with comprehensive validation,
 * business logic validation, file handling, and multilingual support.
 */
class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Users must be authenticated to create companies
        if (!$user) {
            return false;
        }

        // Check if user already has a company (if business rule restricts multiple companies)
        if ($user->company && !$user->hasRole(['admin', 'super-admin'])) {
            return false; // Only allow one company per user unless admin
        }

        // Check user role permissions
        if ($user->hasRole(['candidate', 'banned'])) {
            return false; // Candidates and banned users cannot create companies
        }

        // Admins can always create companies
        if ($user->hasRole(['admin', 'super-admin'])) {
            return true;
        }

        // Employers can create companies
        if ($user->hasRole('employer')) {
            return true;
        }

        // Users without specific roles can create companies (default behavior)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{N}\p{P}\p{Z}\s]+$/u', // Unicode-safe company names
                Rule::unique('companies', 'name')->whereNull('deleted_at'),
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('companies', 'email')->whereNull('deleted_at'),
            ],

            'phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[\d\s\-\(\)]+$/', // International phone format
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/[\w\-]+(\.[\w\-]+)+[/#?]?.*$/', // Valid website URL
            ],

            // Company Description
            'description' => [
                'required',
                'string',
                'min:50',
                'max:10000',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'mission_statement' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'vision_statement' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'values' => [
                'nullable',
                'array',
                'max:10',
            ],

            'values.*' => [
                'string',
                'max:100',
            ],

            // Company Details
            'founded_year' => [
                'required',
                'integer',
                'min:1800',
                'max:' . date('Y'),
            ],

            'employee_count' => [
                'required',
                'integer',
                'min:1',
                'max:1000000',
            ],

            'company_type' => [
                'nullable',
                'string',
                Rule::in(['startup', 'corporation', 'partnership', 'llc', 'nonprofit', 'government', 'other']),
            ],

            'revenue' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],

            'stock_symbol' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[A-Z0-9]{1,10}$/',
                Rule::unique('companies', 'stock_symbol')->whereNull('deleted_at'),
            ],

            // Industry and Classification
            'industry_id' => [
                'required',
                'integer',
                Rule::exists('industries', 'id')->where('is_active', true),
            ],

            'company_size_id' => [
                'required',
                'integer',
                Rule::exists('company_sizes', 'id'),
            ],

            'ownership_type_id' => [
                'nullable',
                'integer',
                Rule::exists('ownership_types', 'id')->where('is_active', true),
            ],

            // Location Information
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id'),
            ],

            'state_id' => [
                'required',
                'integer',
                Rule::exists('states', 'id'),
            ],

            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id'),
            ],

            'address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\w\s\-]+$/',
            ],

            'headquarters' => [
                'nullable',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            // Files and Media
            'logo' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB
                    ->dimensions(Rule::dimensions()->minWidth(100)->minHeight(100)->maxWidth(2000)->maxHeight(2000)),
            ],

            'cover_image' => [
                'nullable',
                File::image()
                    ->max(10 * 1024) // 10MB
                    ->dimensions(Rule::dimensions()->minWidth(800)->minHeight(400)->maxWidth(3000)->maxHeight(1500)),
            ],

            // Social Media
            'social_facebook' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?facebook\.com\/[\w\.\-]+\/?$/',
            ],

            'social_twitter' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?twitter\.com\/[\w\.\-]+\/?$/',
            ],

            'social_linkedin' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/(company|in)\/[\w\.\-]+\/?$/',
            ],

            'social_instagram' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?instagram\.com\/[\w\.\-]+\/?$/',
            ],

            'social_youtube' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?youtube\.com\/(channel|c|user)\/[\w\.\-]+\/?$/',
            ],

            'social_github' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?github\.com\/[\w\.\-]+\/?$/',
            ],

            // Company Culture and Benefits
            'culture_description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'benefits' => [
                'nullable',
                'array',
                'max:20',
            ],

            'benefits.*' => [
                'string',
                'max:100',
            ],

            'technologies' => [
                'nullable',
                'array',
                'max:30',
            ],

            'technologies.*' => [
                'string',
                'max:50',
            ],

            'working_hours' => [
                'nullable',
                'array',
            ],

            'working_hours.monday' => [
                'nullable',
                'array',
            ],

            'working_hours.monday.start' => [
                'nullable',
                'date_format:H:i',
            ],

            'working_hours.monday.end' => [
                'nullable',
                'date_format:H:i',
                'after:working_hours.monday.start',
            ],

            'dress_code' => [
                'nullable',
                'string',
                Rule::in(['formal', 'business_casual', 'casual', 'flexible']),
            ],

            // Certifications and Awards
            'certifications' => [
                'nullable',
                'array',
                'max:15',
            ],

            'certifications.*' => [
                'string',
                'max:100',
            ],

            'awards' => [
                'nullable',
                'array',
                'max:15',
            ],

            'awards.*' => [
                'string',
                'max:100',
            ],

            // Office Locations
            'office_locations' => [
                'nullable',
                'array',
                'max:10',
            ],

            'office_locations.*.name' => [
                'required_with:office_locations',
                'string',
                'max:100',
            ],

            'office_locations.*.address' => [
                'required_with:office_locations',
                'string',
                'max:255',
            ],

            'office_locations.*.is_headquarters' => [
                'nullable',
                'boolean',
            ],

            // Leadership Information
            'ceo_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\.\-\']+$/u',
            ],

            // Company Settings
            'is_private' => [
                'nullable',
                'boolean',
            ],

            'diversity_policy' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // Terms and Agreements
            'terms_accepted' => [
                'required',
                'accepted',
            ],

            'privacy_policy_accepted' => [
                'required',
                'accepted',
            ],

            // Optional Admin Fields
            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'is_verified' => [
                'nullable',
                'boolean',
            ],

            // reCAPTCHA for security
            'g-recaptcha-response' => [
                'required_if:' . (config('app.env') === 'production' ? 'true' : 'false'),
                'string',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Basic information messages
            'name.required' => __('validation.company_store.name.required'),
            'name.min' => __('validation.company_store.name.min'),
            'name.max' => __('validation.company_store.name.max'),
            'name.unique' => __('validation.company_store.name.unique'),
            'name.regex' => __('validation.company_store.name.format'),

            'email.required' => __('validation.company_store.email.required'),
            'email.email' => __('validation.company_store.email.format'),
            'email.unique' => __('validation.company_store.email.unique'),

            'phone.required' => __('validation.company_store.phone.required'),
            'phone.min' => __('validation.company_store.phone.min'),
            'phone.regex' => __('validation.company_store.phone.format'),

            'website.url' => __('validation.company_store.website.format'),
            'website.regex' => __('validation.company_store.website.valid_url'),

            // Description messages
            'description.required' => __('validation.company_store.description.required'),
            'description.min' => __('validation.company_store.description.min'),
            'description.max' => __('validation.company_store.description.max'),

            'short_description.max' => __('validation.company_store.short_description.max'),

            // Company details messages
            'founded_year.required' => __('validation.company_store.founded_year.required'),
            'founded_year.min' => __('validation.company_store.founded_year.min'),
            'founded_year.max' => __('validation.company_store.founded_year.max'),

            'employee_count.required' => __('validation.company_store.employee_count.required'),
            'employee_count.min' => __('validation.company_store.employee_count.min'),
            'employee_count.max' => __('validation.company_store.employee_count.max'),

            // Industry and classification messages
            'industry_id.required' => __('validation.company_store.industry_id.required'),
            'industry_id.exists' => __('validation.company_store.industry_id.exists'),

            'company_size_id.required' => __('validation.company_store.company_size_id.required'),
            'company_size_id.exists' => __('validation.company_store.company_size_id.exists'),

            // Location messages
            'country_id.required' => __('validation.company_store.country_id.required'),
            'country_id.exists' => __('validation.company_store.country_id.exists'),

            'state_id.required' => __('validation.company_store.state_id.required'),
            'state_id.exists' => __('validation.company_store.state_id.exists'),

            'city_id.required' => __('validation.company_store.city_id.required'),
            'city_id.exists' => __('validation.company_store.city_id.exists'),

            'address.required' => __('validation.company_store.address.required'),
            'address.min' => __('validation.company_store.address.min'),

            'postal_code.required' => __('validation.company_store.postal_code.required'),
            'postal_code.regex' => __('validation.company_store.postal_code.format'),

            // File upload messages
            'logo.image' => __('validation.company_store.logo.image'),
            'logo.max' => __('validation.company_store.logo.max_size'),
            'logo.dimensions' => __('validation.company_store.logo.dimensions'),

            'cover_image.image' => __('validation.company_store.cover_image.image'),
            'cover_image.max' => __('validation.company_store.cover_image.max_size'),
            'cover_image.dimensions' => __('validation.company_store.cover_image.dimensions'),

            // Social media messages
            'social_facebook.url' => __('validation.company_store.social_facebook.format'),
            'social_facebook.regex' => __('validation.company_store.social_facebook.valid_facebook'),

            'social_twitter.url' => __('validation.company_store.social_twitter.format'),
            'social_twitter.regex' => __('validation.company_store.social_twitter.valid_twitter'),

            'social_linkedin.url' => __('validation.company_store.social_linkedin.format'),
            'social_linkedin.regex' => __('validation.company_store.social_linkedin.valid_linkedin'),

            // Terms and agreements
            'terms_accepted.required' => __('validation.company_store.terms_accepted.required'),
            'terms_accepted.accepted' => __('validation.company_store.terms_accepted.accepted'),

            'privacy_policy_accepted.required' => __('validation.company_store.privacy_policy_accepted.required'),
            'privacy_policy_accepted.accepted' => __('validation.company_store.privacy_policy_accepted.accepted'),

            // reCAPTCHA
            'g-recaptcha-response.required_if' => __('validation.company_store.recaptcha.required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.company_name'),
            'email' => __('validation.attributes.company_email'),
            'phone' => __('validation.attributes.company_phone'),
            'website' => __('validation.attributes.company_website'),
            'description' => __('validation.attributes.company_description'),
            'short_description' => __('validation.attributes.short_description'),
            'founded_year' => __('validation.attributes.founded_year'),
            'employee_count' => __('validation.attributes.employee_count'),
            'industry_id' => __('validation.attributes.industry'),
            'company_size_id' => __('validation.attributes.company_size'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'address' => __('validation.attributes.address'),
            'postal_code' => __('validation.attributes.postal_code'),
            'logo' => __('validation.attributes.company_logo'),
            'cover_image' => __('validation.attributes.cover_image'),
            'social_facebook' => __('validation.attributes.facebook_url'),
            'social_twitter' => __('validation.attributes.twitter_url'),
            'social_linkedin' => __('validation.attributes.linkedin_url'),
            'ceo_name' => __('validation.attributes.ceo_name'),
            'mission_statement' => __('validation.attributes.mission_statement'),
            'vision_statement' => __('validation.attributes.vision_statement'),
            'terms_accepted' => __('validation.attributes.terms_acceptance'),
            'privacy_policy_accepted' => __('validation.attributes.privacy_policy_acceptance'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format data
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'website' => $this->website ? $this->formatUrl($this->website) : null,
            'phone' => $this->phone ? $this->formatPhone($this->phone) : null,
            'description' => $this->description ? trim($this->description) : null,
            'short_description' => $this->short_description ? trim($this->short_description) : null,
            'stock_symbol' => $this->stock_symbol ? strtoupper(trim($this->stock_symbol)) : null,
            'ceo_name' => $this->ceo_name ? trim($this->ceo_name) : null,
        ]);

        // Ensure boolean fields are properly typed
        $booleanFields = ['is_private', 'is_featured', 'is_verified', 'terms_accepted', 'privacy_policy_accepted'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $this->boolean($field)]);
            }
        }

        // Ensure numeric fields are properly typed
        $numericFields = ['founded_year', 'employee_count', 'revenue', 'latitude', 'longitude'];
        foreach ($numericFields as $field) {
            if ($this->has($field) && !empty($this->$field)) {
                $this->merge([$field => is_numeric($this->$field) ? (float) $this->$field : null]);
            }
        }

        // Clean array fields
        $arrayFields = ['benefits', 'technologies', 'certifications', 'awards', 'values'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && is_array($this->$field)) {
                $cleanArray = array_filter(array_map('trim', $this->$field), function ($item) {
                    return !empty($item);
                });
                $this->merge([$field => array_values($cleanArray)]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBusinessLogic($validator);
            $this->validateAdminFields($validator);
            $this->validateGeographicConsistency($validator);
            $this->validateSocialMediaConsistency($validator);
        });
    }

    /**
     * Validate business logic constraints.
     */
    protected function validateBusinessLogic($validator): void
    {
        // Validate company size vs employee count consistency
        if ($this->company_size_id && $this->employee_count) {
            $companySize = \App\Models\CompanySize::find($this->company_size_id);
            if ($companySize) {
                $min = $companySize->min_employees ?? 0;
                $max = $companySize->max_employees ?? PHP_INT_MAX;
                
                if ($this->employee_count < $min || $this->employee_count > $max) {
                    $validator->errors()->add('employee_count', __('validation.company_store.employee_count_size_mismatch'));
                }
            }
        }

        // Validate founded year vs company age
        if ($this->founded_year) {
            $age = now()->year - $this->founded_year;
            if ($age < 0) {
                $validator->errors()->add('founded_year', __('validation.company_store.founded_year_future'));
            }
            
            if ($age > 200) {
                $validator->errors()->add('founded_year', __('validation.company_store.founded_year_too_old'));
            }
        }

        // Validate stock symbol for public companies
        if ($this->company_type === 'corporation' && $this->revenue > 1000000 && !$this->stock_symbol) {
            // Large corporations might be expected to have stock symbols
            // This is a business rule that can be adjusted
        }

        // Validate headquarters in office locations
        if ($this->office_locations) {
            $headquartersCount = collect($this->office_locations)
                ->where('is_headquarters', true)
                ->count();
                
            if ($headquartersCount > 1) {
                $validator->errors()->add('office_locations', __('validation.company_store.multiple_headquarters'));
            }
        }
    }

    /**
     * Validate admin-specific fields.
     */
    protected function validateAdminFields($validator): void
    {
        $user = auth()->user();
        
        // Only admins can set featured/verified status
        $adminOnlyFields = ['is_featured', 'is_verified'];
        foreach ($adminOnlyFields as $field) {
            if ($this->has($field) && $this->$field && (!$user || !$user->hasRole(['admin', 'super-admin']))) {
                $validator->errors()->add($field, __('validation.company_store.admin_only_field'));
            }
        }
    }

    /**
     * Validate geographic consistency.
     */
    protected function validateGeographicConsistency($validator): void
    {
        // Validate state belongs to country
        if ($this->country_id && $this->state_id) {
            $stateExists = \App\Models\State::where('id', $this->state_id)
                ->where('country_id', $this->country_id)
                ->exists();
                
            if (!$stateExists) {
                $validator->errors()->add('state_id', __('validation.company_store.state_country_mismatch'));
            }
        }

        // Validate city belongs to state
        if ($this->state_id && $this->city_id) {
            $cityExists = \App\Models\City::where('id', $this->city_id)
                ->where('state_id', $this->state_id)
                ->exists();
                
            if (!$cityExists) {
                $validator->errors()->add('city_id', __('validation.company_store.city_state_mismatch'));
            }
        }
    }

    /**
     * Validate social media consistency.
     */
    protected function validateSocialMediaConsistency($validator): void
    {
        // Check for reasonable social media presence
        $socialFields = ['social_facebook', 'social_twitter', 'social_linkedin', 'social_instagram'];
        $socialCount = 0;
        
        foreach ($socialFields as $field) {
            if (!empty($this->$field)) {
                $socialCount++;
            }
        }
        
        // For companies with websites, suggest having at least one social media presence
        if ($this->website && $socialCount === 0) {
            // This is just a suggestion, not an error
            // Could be implemented as a warning in the UI
        }
    }

    /**
     * Format URL to ensure proper protocol.
     */
    private function formatUrl($url): string
    {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return 'https://' . $url;
        }
        return $url;
    }

    /**
     * Format phone number.
     */
    private function formatPhone($phone): string
    {
        // Remove any non-digit characters except + and spaces for international format
        return preg_replace('/[^\d\+\s\-\(\)]/', '', $phone);
    }

    /**
     * Get the company data for creation.
     */
    public function getCompanyData(): array
    {
        return [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'founded_year' => $this->founded_year,
            'employee_count' => $this->employee_count,
            'company_type' => $this->company_type,
            'revenue' => $this->revenue,
            'stock_symbol' => $this->stock_symbol,
            'industry_id' => $this->industry_id,
            'company_size_id' => $this->company_size_id,
            'ownership_type_id' => $this->ownership_type_id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'headquarters' => $this->headquarters,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'social_facebook' => $this->social_facebook,
            'social_twitter' => $this->social_twitter,
            'social_linkedin' => $this->social_linkedin,
            'social_instagram' => $this->social_instagram,
            'social_youtube' => $this->social_youtube,
            'social_github' => $this->social_github,
            'culture_description' => $this->culture_description,
            'benefits' => $this->benefits,
            'technologies' => $this->technologies,
            'working_hours' => $this->working_hours,
            'dress_code' => $this->dress_code,
            'certifications' => $this->certifications,
            'awards' => $this->awards,
            'office_locations' => $this->office_locations,
            'ceo_name' => $this->ceo_name,
            'mission_statement' => $this->mission_statement,
            'vision_statement' => $this->vision_statement,
            'values' => $this->values,
            'diversity_policy' => $this->diversity_policy,
            'is_private' => $this->is_private ?? false,
            'is_featured' => $this->is_featured ?? false,
            'is_verified' => $this->is_verified ?? false,
            'is_active' => true, // New companies are active by default
        ];
    }
}
