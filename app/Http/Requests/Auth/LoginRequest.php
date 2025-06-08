<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMaliciousContent;

/**
 * Request validation for AuthController::login
 * 
 * @enhanced by RequestValidationImprover
 */
class LoginRequest extends FormRequest
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
    "email" => "required|email",
    "password" => "required|string",
    "remember" => "boolean"
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
    "email.required" => "Email is required",
    "email.email" => "Please enter a valid email address",
    "password.required" => "Password is required"
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
