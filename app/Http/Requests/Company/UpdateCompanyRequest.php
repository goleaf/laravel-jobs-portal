<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use App\Rules\NoMaliciousContent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Request validation for CompanyController::store.
 *
 * @enhanced by RequestValidationImprover
 */
class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $company = $this->route('company');

        // Admin can update any company
        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        // Company owner can update their own company
        return $company && Auth::user()->id === $company->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        $company = $this->route('company');

        return [
            // Basic company information
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($company->id),
                'regex:/^[a-zA-Z0-9\s\-\.\&\(\)]+$/',
            ],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($company->user_id),
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
            ],

            // Location information
            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],
            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id',
            ],
            'city_id' => [
                'nullable',
                'integer',
                'exists:cities,id',
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

            // Files
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
            ],

            // Flags (admin only)
            'is_active' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) {
                    if (!Auth::user()->hasRole('Admin')) {
                        $fail(__('companies.validation.admin_only_field'));
                    }
                },
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) {
                    if (!Auth::user()->hasRole('Admin')) {
                        $fail(__('companies.validation.admin_only_field'));
                    }
                },
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => __('companies.validation.name_unique'),
            'name.regex' => __('companies.validation.name_format'),
            'email.unique' => __('companies.validation.email_unique'),
            'phone.regex' => __('companies.validation.phone_format'),
            'website.url' => __('companies.validation.website_url'),
            'country_id.exists' => __('companies.validation.country_not_found'),
            'state_id.exists' => __('companies.validation.state_not_found'),
            'city_id.exists' => __('companies.validation.city_not_found'),
            'established_in.min' => __('companies.validation.established_in_min'),
            'established_in.max' => __('companies.validation.established_in_max'),
            'no_of_employees.min' => __('companies.validation.employees_min'),
            'no_of_employees.max' => __('companies.validation.employees_max'),
            'industry_id.exists' => __('companies.validation.industry_not_found'),
            'ownership_type_id.exists' => __('companies.validation.ownership_type_not_found'),
            'company_size_id.exists' => __('companies.validation.company_size_not_found'),
            'facebook_url.regex' => __('companies.validation.facebook_url_format'),
            'twitter_url.regex' => __('companies.validation.twitter_url_format'),
            'linkedin_url.regex' => __('companies.validation.linkedin_url_format'),
            'logo.image' => __('companies.validation.logo_image'),
            'logo.mimes' => __('companies.validation.logo_mimes'),
            'logo.max' => __('companies.validation.logo_max_size'),
            'logo.dimensions' => __('companies.validation.logo_dimensions'),
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
            'user.first_name' => 'first name',
            'user.last_name' => 'last name',
            'user.email' => 'email address',
            'user.phone' => 'phone number',
            'job_title' => 'job title',
            'job_description' => 'job description',
            'job_expiry_date' => 'job expiry date',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Add custom validation logic here
            if ($this->has('salary_from') && $this->has('salary_to')) {
                if ($this->salary_from > $this->salary_to) {
                    $validator->errors()->add('salary_to', 'Maximum salary must be greater than minimum salary');
                }
            }

            // Check for malicious content in text fields
            foreach (['job_description', 'job_requirement', 'job_benefit'] as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $rule = new NoMaliciousContent();
                    if (!$rule->passes($field, $this->{$field})) {
                        $validator->errors()->add($field, $rule->message());
                    }
                }
            }
        });
    }

    /**
     * Get the processed data for update.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();

        // Remove admin-only fields if user is not admin
        if (!Auth::user()->hasRole('Admin')) {
            unset($data['is_active'], $data['is_featured']);
        }

        return $data;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if ($this->has('job_title')) {
            $this->merge([
                'job_title' => strip_tags($this->job_title),
            ]);
        }

        if ($this->has('job_description')) {
            $this->merge([
                'job_description' => strip_tags($this->job_description, '<p><br><ul><ol><li><strong><em>'),
            ]);
        }
    }
}
