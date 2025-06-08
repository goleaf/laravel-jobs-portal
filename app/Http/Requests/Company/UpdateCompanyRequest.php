<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMaliciousContent;

/**
 * Request validation for CompanyController::store
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
        return true; // TODO: Implement proper authorization logic based on user permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
    "name" => "required|string|max:255",
    "email" => "required|email|unique:companies,email",
    "phone" => "nullable|string|max:20",
    "website" => "nullable|url|max:255",
    "industry_id" => "nullable|exists:industries,id",
    "ownership_type_id" => "nullable|exists:ownership_types,id",
    "company_size_id" => "nullable|exists:company_sizes,id",
    "established_in" => "nullable|integer|min:1800|max:2025",
    "description" => "nullable|string|max:2000",
    "country_id" => "required|exists:countries,id",
    "state_id" => "nullable|exists:states,id",
    "city_id" => "nullable|exists:cities,id",
    "address" => "nullable|string|max:500",
    "postal_code" => "nullable|string|max:20",
    "logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
    "is_active" => "boolean"
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
    "name.required" => "Company name is required",
    "email.required" => "Email is required",
    "email.unique" => "Email already exists",
    "website.url" => "Please enter a valid website URL",
    "country_id.required" => "Country is required",
    "established_in.min" => "Establishment year cannot be before 1800",
    "established_in.max" => "Establishment year cannot be in the future",
    "logo.image" => "Logo must be an image",
    "logo.max" => "Logo size cannot exceed 2MB"
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
            'salary_to' => 'maximum salary'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if ($this->has('job_title')) {
            $this->merge([
                'job_title' => strip_tags($this->job_title)
            ]);
        }
        
        if ($this->has('job_description')) {
            $this->merge([
                'job_description' => strip_tags($this->job_description, '<p><br><ul><ol><li><strong><em>')
            ]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
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
}
