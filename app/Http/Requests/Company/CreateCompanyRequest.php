<?php

namespace App\Http\Requests\Company;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\State;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can create companies directly
        return Auth::check() && Auth::user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Basic company information
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:companies,name',
                'regex:/^[a-zA-Z0-9\s\-\.\&\(\)]+$/',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],
            'website' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            ],

            // Location information
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->country_id) {
                        $state = State::find($value);
                        if (!$state || $state->country_id != $this->country_id) {
                            $fail(__('companies.validation.state_country_mismatch'));
                        }
                    }
                },
            ],
            'city_id' => [
                'nullable',
                'integer',
                'exists:cities,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->state_id) {
                        $city = City::find($value);
                        if (!$city || $city->state_id != $this->state_id) {
                            $fail(__('companies.validation.city_state_mismatch'));
                        }
                    }
                },
            ],
            'location' => 'nullable|string|max:500',

            // Company details
            'details' => 'nullable|string|max:5000',
            'established_in' => [
                'nullable',
                'integer',
                'min:1800',
                'max:'.date('Y'),
            ],
            'no_of_employees' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000000',
            ],

            // Industry and ownership
            'industry_id' => [
                'nullable',
                'integer',
                'exists:industries,id',
            ],
            'ownership_type_id' => [
                'nullable',
                'integer',
                'exists:ownership_types,id',
            ],
            'company_size_id' => [
                'nullable',
                'integer',
                'exists:company_sizes,id',
            ],

            // Social media links
            'facebook_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?facebook\.com\/.*$/',
            ],
            'twitter_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?twitter\.com\/.*$/',
            ],
            'linkedin_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.*$/',
            ],
            'google_plus_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?plus\.google\.com\/.*$/',
            ],
            'pinterest_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?pinterest\.com\/.*$/',
            ],

            // User account information
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'password_confirmation' => 'required|string|min:8|max:255',

            // Files
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
            ],

            // Flags
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',

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
     */
    public function messages(): array
    {
        return [
            'name.required' => __('companies.validation.name_required'),
            'name.unique' => __('companies.validation.name_unique'),
            'name.regex' => __('companies.validation.name_format'),
            'email.required' => __('companies.validation.email_required'),
            'email.email' => __('companies.validation.email_invalid'),
            'email.unique' => __('companies.validation.email_unique'),
            'phone.regex' => __('companies.validation.phone_format'),
            'website.url' => __('companies.validation.website_invalid'),
            'website.regex' => __('companies.validation.website_format'),
            'country_id.required' => __('companies.validation.country_required'),
            'country_id.exists' => __('companies.validation.country_invalid'),
            'state_id.exists' => __('companies.validation.state_invalid'),
            'city_id.exists' => __('companies.validation.city_invalid'),
            'established_in.min' => __('companies.validation.established_in_min'),
            'established_in.max' => __('companies.validation.established_in_max'),
            'no_of_employees.min' => __('companies.validation.employees_min'),
            'no_of_employees.max' => __('companies.validation.employees_max'),
            'facebook_url.regex' => __('companies.validation.facebook_format'),
            'twitter_url.regex' => __('companies.validation.twitter_format'),
            'linkedin_url.regex' => __('companies.validation.linkedin_format'),
            'first_name.required' => __('companies.validation.first_name_required'),
            'first_name.regex' => __('companies.validation.first_name_format'),
            'last_name.required' => __('companies.validation.last_name_required'),
            'last_name.regex' => __('companies.validation.last_name_format'),
            'password.required' => __('companies.validation.password_required'),
            'password.min' => __('companies.validation.password_min'),
            'password.regex' => __('companies.validation.password_format'),
            'password.confirmed' => __('companies.validation.password_confirmed'),
            'logo.image' => __('companies.validation.logo_image'),
            'logo.mimes' => __('companies.validation.logo_mimes'),
            'logo.max' => __('companies.validation.logo_max'),
            'logo.dimensions' => __('companies.validation.logo_dimensions'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('companies.attributes.name'),
            'email' => __('companies.attributes.email'),
            'phone' => __('companies.attributes.phone'),
            'website' => __('companies.attributes.website'),
            'country_id' => __('companies.attributes.country'),
            'state_id' => __('companies.attributes.state'),
            'city_id' => __('companies.attributes.city'),
            'location' => __('companies.attributes.location'),
            'details' => __('companies.attributes.details'),
            'established_in' => __('companies.attributes.established_in'),
            'no_of_employees' => __('companies.attributes.no_of_employees'),
            'industry_id' => __('companies.attributes.industry'),
            'ownership_type_id' => __('companies.attributes.ownership_type'),
            'company_size_id' => __('companies.attributes.company_size'),
            'facebook_url' => __('companies.attributes.facebook_url'),
            'twitter_url' => __('companies.attributes.twitter_url'),
            'linkedin_url' => __('companies.attributes.linkedin_url'),
            'first_name' => __('companies.attributes.first_name'),
            'last_name' => __('companies.attributes.last_name'),
            'password' => __('companies.attributes.password'),
            'logo' => __('companies.attributes.logo'),
            'is_active' => __('companies.attributes.is_active'),
            'is_featured' => __('companies.attributes.is_featured'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for duplicate company names (case-insensitive)
            if ($this->name) {
                $existingCompany = Company::whereRaw('LOWER(name) = ?', [strtolower($this->name)])->first();
                if ($existingCompany) {
                    $validator->errors()->add('name', __('companies.validation.name_exists'));
                }
            }

            // Validate website domain doesn't belong to another company
            if ($this->website) {
                $domain = parse_url($this->website, PHP_URL_HOST);
                if ($domain) {
                    $existingCompany = Company::where('website', 'like', '%'.$domain.'%')->first();
                    if ($existingCompany) {
                        $validator->errors()->add('website', __('companies.validation.website_exists'));
                    }
                }
            }

            // Business logic validation
            if ($this->hasBusinessLogicConflicts()) {
                $validator->errors()->add('general', __('companies.validation.business_conflict'));
            }

            // Content security validation
            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('general', __('companies.validation.suspicious_content'));
            }
        });
    }

    /**
     * Get validated data with processed fields.
     */
    public function getProcessedData(): array
    {
        $validated = $this->validated();

        return array_merge($validated, [
            'slug' => Str::slug($validated['name']),
            'created_by' => Auth::id(),
            'user_data' => [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'country_id' => $validated['country_id'],
                'state_id' => $validated['state_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ],
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'website' => $this->website ? strtolower(trim($this->website)) : null,
            'phone' => $this->phone ? preg_replace('/[^\d\+\-\(\)\s]/', '', $this->phone) : null,
            'first_name' => trim($this->first_name ?? ''),
            'last_name' => trim($this->last_name ?? ''),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Company creation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Check for business logic conflicts.
     */
    private function hasBusinessLogicConflicts(): bool
    {
        // Check if company size matches employee count
        if ($this->company_size_id && $this->no_of_employees) {
            $companySize = CompanySize::find($this->company_size_id);
            if ($companySize) {
                // Add logic to validate employee count against company size ranges
                // This would depend on your company size definitions
            }
        }

        return false;
    }

    /**
     * Check for suspicious content.
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware', 'hack', 'exploit', 'phishing'];
        $content = strtolower(($this->name ?? '').' '.($this->details ?? '').' '.($this->website ?? ''));

        foreach ($suspiciousPatterns as $pattern) {
            if (false !== strpos($content, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
