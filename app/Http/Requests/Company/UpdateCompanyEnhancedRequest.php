<?php

namespace App\Http\Requests\Company;

use App\Models\City;
use App\Models\State;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Enhanced Enhanced Form Request for Update Company
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Following proven MasterData pattern with update-specific rules.
 */
class UpdateCompanyEnhancedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enhanced Pattern: Role-based authorization with ownership check
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $company = $this->route('company');

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $company && $company->user_id === $user->id);
    }

    /**
     * Get the validation rules that apply to the request.
     * Enhanced Pattern: Update-specific validation with security.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        $companyId = $this->route('company')?->id;
        $userId = $this->route('company')?->user_id;

        return [
            // Company Basic Information (unique except current)
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($companyId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
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

            // User Information (optional for updates)
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8'],

            // Status and Settings (Admin only for some fields)
            'is_active' => ['boolean'],
            'is_featured' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && ! auth()->user()->hasRole('Admin')) {
                        $fail(__('validation.admin_only_field'));
                    }
                },
            ],

            // File Uploads
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max

            // Featured settings (Admin only)
            'featured_until' => [
                'nullable',
                'date',
                'after:today',
                function ($attribute, $value, $fail) {
                    if ($value && ! auth()->user()->hasRole('Admin')) {
                        $fail(__('validation.admin_only_field'));
                    }
                },
            ],
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
            'name.unique' => __('validation.company_name_unique_update'),
            'name.max' => __('validation.company_name_max'),
            'email.required' => __('validation.email_required'),
            'email.email' => __('validation.email_format'),
            'email.unique' => __('validation.email_unique_update'),
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
            'featured_until.after' => __('validation.featured_until_future'),
            'is_featured.admin_only' => __('validation.admin_only_field'),
            'featured_until.admin_only' => __('validation.admin_only_field'),
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
            'is_featured' => __('validation.attributes.featured_status'),
            'featured_until' => __('validation.attributes.featured_until'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Enhanced validation with update logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasUnauthorizedChanges()) {
                $validator->errors()->add('permission', __('validation.unauthorized_update'));
            }

            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('details', __('validation.suspicious_content'));
            }

            if ($this->hasInvalidSocialMediaUrls()) {
                $validator->errors()->add('social_media', __('validation.invalid_social_media'));
            }

            if ($this->hasInvalidLocationChanges()) {
                $validator->errors()->add('location', __('validation.invalid_location_change'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Enhanced Pattern: Data normalization for updates.
     */
    protected function prepareForValidation(): void
    {
        $data = [
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
            'no_of_offices' => $this->no_of_offices ? (int) $this->no_of_offices : null,
            'established_in' => $this->established_in ? (int) $this->established_in : null,
        ];

        // Only allow featured settings for admins
        if (auth()->user() && auth()->user()->hasRole('Admin')) {
            $data['is_featured'] = filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        $this->merge($data);
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with audit trail.
     */
    protected function failedValidation(Validator $validator): void
    {
        $company = $this->route('company');

        logger()->warning('Enhanced validation failed for UpdateCompanyEnhancedRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'Company',
            'action' => 'Update',
            'company_id' => $company?->id,
            'company_name' => $this->name,
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'suspicious_patterns' => $this->hasSuspiciousContent(),
            'invalid_social_urls' => $this->hasInvalidSocialMediaUrls(),
            'unauthorized_changes' => $this->hasUnauthorizedChanges(),
            'invalid_location' => $this->hasInvalidLocationChanges(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Enhanced Pattern: Check for unauthorized changes.
     */
    private function hasUnauthorizedChanges(): bool
    {
        $user = auth()->user();
        $company = $this->route('company');

        if (! $user || ! $company) {
            return true;
        }

        // Only admins can change featured status
        if ($this->has('is_featured') && ! $user->hasRole('Admin')) {
            return true;
        }

        // Employers can only edit their own companies
        if ($user->hasRole('Employer') && $company->user_id !== $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Enhanced Pattern: Content security validation.
     */
    private function hasSuspiciousContent(): bool
    {
        if (! $this->details) {
            return false;
        }

        $suspiciousPatterns = [
            'spam', 'scam', 'free money', 'click here',
            'guaranteed income', 'work from home guaranteed',
            'virus', 'malware', 'phishing',
        ];

        $content = strtolower($this->details);

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
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
            if ($url && ! empty($url) && strpos($url, $expectedDomain) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enhanced Pattern: Location validation for updates.
     */
    private function hasInvalidLocationChanges(): bool
    {
        // Validate state belongs to country
        if ($this->country_id && $this->state_id) {
            $stateExists = State::where('id', $this->state_id)
                ->where('country_id', $this->country_id)
                ->exists();

            if (! $stateExists) {
                return true;
            }
        }

        // Validate city belongs to state
        if ($this->state_id && $this->city_id) {
            $cityExists = City::where('id', $this->city_id)
                ->where('state_id', $this->state_id)
                ->exists();

            if (! $cityExists) {
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
        if (! preg_match('/^https?:\/\//', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }
}
