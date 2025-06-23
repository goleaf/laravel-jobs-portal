<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Enhanced Enhanced Form Request for Store Company
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Following proven MasterData pattern.
 */
class StoreCompanyEnhancedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enhanced Pattern: Role-based authorization
        return auth()->check() && (
            auth()->user()->hasRole('Admin')
            || auth()->user()->hasRole('Employer')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Enhanced Pattern: Comprehensive validation with security.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Company Basic Information
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'ceo' => ['nullable', 'string', 'max:255'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'ownership_type_id' => ['required', 'integer', 'exists:ownership_types,id'],
            'company_size_id' => ['required', 'integer', 'exists:company_sizes,id'],

            // Contact Information
            'phone' => ['nullable', 'string', 'max:20'],
            'fax' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],

            // Location Information
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'location2' => ['nullable', 'string', 'max:255'],

            // Company Details
            'details' => ['nullable', 'string', 'max:65000'],
            'no_of_offices' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'established_in' => ['nullable', 'integer', 'min:1800', 'max:'.date('Y')],

            // Social Media
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'google_plus_url' => ['nullable', 'url', 'max:255'],
            'pinterest_url' => ['nullable', 'url', 'max:255'],

            // User Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],

            // Status and Settings
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],

            // File Uploads
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max

            // Security
            'g-recaptcha-response' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (config('app.recaptcha_enabled', false) && empty($value)) {
                        $fail(__('validation.recaptcha_required'));
                    }
                },
            ],

            // Terms and Privacy
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Enhanced Pattern: Multilingual error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.company_name_required'),
            'name.unique' => __('validation.company_name_unique'),
            'name.max' => __('validation.company_name_max'),
            'email.required' => __('validation.email_required'),
            'email.email' => __('validation.email_format'),
            'email.unique' => __('validation.email_unique'),
            'industry_id.required' => __('validation.industry_required'),
            'industry_id.exists' => __('validation.industry_exists'),
            'ownership_type_id.required' => __('validation.ownership_type_required'),
            'ownership_type_id.exists' => __('validation.ownership_type_exists'),
            'company_size_id.required' => __('validation.company_size_required'),
            'company_size_id.exists' => __('validation.company_size_exists'),
            'country_id.required' => __('validation.country_required'),
            'country_id.exists' => __('validation.country_exists'),
            'state_id.exists' => __('validation.state_exists'),
            'city_id.exists' => __('validation.city_exists'),
            'website.url' => __('validation.website_format'),
            'facebook_url.url' => __('validation.facebook_url_format'),
            'twitter_url.url' => __('validation.twitter_url_format'),
            'linkedin_url.url' => __('validation.linkedin_url_format'),
            'first_name.required' => __('validation.first_name_required'),
            'last_name.required' => __('validation.last_name_required'),
            'password.required' => __('validation.password_required'),
            'password.min' => __('validation.password_min'),
            'password.confirmed' => __('validation.password_confirmed'),
            'established_in.min' => __('validation.established_in_min'),
            'established_in.max' => __('validation.established_in_max'),
            'image.image' => __('validation.image_format'),
            'image.mimes' => __('validation.image_mimes'),
            'image.max' => __('validation.image_max'),
            'logo.image' => __('validation.logo_format'),
            'logo.mimes' => __('validation.logo_mimes'),
            'logo.max' => __('validation.logo_max'),
            'terms_accepted.required' => __('validation.terms_required'),
            'terms_accepted.accepted' => __('validation.terms_accepted'),
            'privacy_accepted.required' => __('validation.privacy_required'),
            'privacy_accepted.accepted' => __('validation.privacy_accepted'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Enhanced Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.company_name'),
            'email' => __('validation.attributes.email'),
            'ceo' => __('validation.attributes.ceo'),
            'industry_id' => __('validation.attributes.industry'),
            'ownership_type_id' => __('validation.attributes.ownership_type'),
            'company_size_id' => __('validation.attributes.company_size'),
            'phone' => __('validation.attributes.phone'),
            'fax' => __('validation.attributes.fax'),
            'website' => __('validation.attributes.website'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'location' => __('validation.attributes.location'),
            'details' => __('validation.attributes.company_details'),
            'no_of_offices' => __('validation.attributes.number_of_offices'),
            'established_in' => __('validation.attributes.established_year'),
            'facebook_url' => __('validation.attributes.facebook_url'),
            'twitter_url' => __('validation.attributes.twitter_url'),
            'linkedin_url' => __('validation.attributes.linkedin_url'),
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
            'image' => __('validation.attributes.company_image'),
            'logo' => __('validation.attributes.company_logo'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Performance optimization.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasEnhancedValidationConflicts()) {
                $validator->errors()->add('name', __('validation.company_conflict'));
            }

            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('details', __('validation.suspicious_content'));
            }

            if ($this->hasInvalidSocialMediaUrls()) {
                $validator->errors()->add('social_media', __('validation.invalid_social_media'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Enhanced Pattern: Data normalization.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'first_name' => trim($this->first_name ?? ''),
            'last_name' => trim($this->last_name ?? ''),
            'website' => $this->normalizeUrl($this->website),
            'facebook_url' => $this->normalizeUrl($this->facebook_url),
            'twitter_url' => $this->normalizeUrl($this->twitter_url),
            'linkedin_url' => $this->normalizeUrl($this->linkedin_url),
            'google_plus_url' => $this->normalizeUrl($this->google_plus_url),
            'pinterest_url' => $this->normalizeUrl($this->pinterest_url),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'no_of_offices' => $this->no_of_offices ? (int) $this->no_of_offices : null,
            'established_in' => $this->established_in ? (int) $this->established_in : null,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with security monitoring.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Enhanced validation failed for StoreCompanyEnhancedRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'Company',
            'action' => 'Store',
            'company_name' => $this->name,
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'suspicious_patterns' => $this->hasSuspiciousContent(),
            'invalid_social_urls' => $this->hasInvalidSocialMediaUrls(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Enhanced Pattern: Enhanced business logic validation.
     */
    private function hasEnhancedValidationConflicts(): bool
    {
        // Check for existing company with similar name/email
        if ($this->name && $this->email) {
            $existingCompany = Company::where('name', 'LIKE', '%'.$this->name.'%')
                ->orWhereHas('user', function ($query) {
                    $query->where('email', $this->email);
                })
                ->exists()
            ;

            return $existingCompany;
        }

        return false;
    }

    /**
     * Enhanced Pattern: Content security validation.
     */
    private function hasSuspiciousContent(): bool
    {
        if (!$this->details) {
            return false;
        }

        $suspiciousPatterns = [
            'spam', 'scam', 'free money', 'click here',
            'guaranteed income', 'work from home guaranteed',
        ];

        $content = strtolower($this->details);

        foreach ($suspiciousPatterns as $pattern) {
            if (false !== strpos($content, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enhanced Pattern: Social media URL validation.
     */
    private function hasInvalidSocialMediaUrls(): bool
    {
        $socialUrls = [
            'facebook_url' => 'facebook.com',
            'twitter_url' => 'twitter.com',
            'linkedin_url' => 'linkedin.com',
        ];

        foreach ($socialUrls as $field => $expectedDomain) {
            $url = $this->{$field};
            if ($url && !empty($url) && false === strpos($url, $expectedDomain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enhanced Pattern: URL normalization helper.
     */
    private function normalizeUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        // Add https:// if no protocol specified
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }
}
