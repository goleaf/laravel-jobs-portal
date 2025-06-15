<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CandidateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user owns this candidate profile or is admin
        $candidate = $this->route('candidate');

        return auth()->check() && (
            auth()->user()->id === $candidate->user_id
            || auth()->user()->hasRole('admin')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|string|in:male,female,other',
            'address' => 'sometimes|string|max:500',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'postal_code' => 'sometimes|string|max:20',
            'career_level_id' => 'sometimes|integer|exists:career_levels,id',
            'industry_id' => 'sometimes|integer|exists:industries,id',
            'job_experience_id' => 'sometimes|integer|exists:job_experiences,id',
            'current_salary' => 'sometimes|numeric|min:0',
            'expected_salary' => 'sometimes|numeric|min:0',
            'salary_currency_id' => 'sometimes|integer|exists:salary_currencies,id',
            'is_immediate_available' => 'sometimes|boolean',
            'experience_years' => 'sometimes|integer|min:0|max:50',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
            'languages' => 'sometimes|array',
            'languages.*' => 'integer|exists:languages,id',
            'bio' => 'sometimes|string|max:1000',
            'linkedin_url' => 'sometimes|url|max:255',
            'github_url' => 'sometimes|url|max:255',
            'website_url' => 'sometimes|url|max:255',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'visibility' => 'sometimes|string|in:public,private,limited',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required when provided.',
            'first_name.max' => 'First name cannot exceed 255 characters.',
            'last_name.required' => 'Last name is required when provided.',
            'last_name.max' => 'Last name cannot exceed 255 characters.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'gender.in' => 'Gender must be one of: male, female, other.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'country_id.exists' => 'The selected country does not exist.',
            'state_id.exists' => 'The selected state does not exist.',
            'city_id.exists' => 'The selected city does not exist.',
            'career_level_id.exists' => 'The selected career level does not exist.',
            'industry_id.exists' => 'The selected industry does not exist.',
            'current_salary.numeric' => 'Current salary must be a valid number.',
            'expected_salary.numeric' => 'Expected salary must be a valid number.',
            'salary_currency_id.exists' => 'The selected currency does not exist.',
            'is_immediate_available.boolean' => 'Immediate availability must be true or false.',
            'experience_years.integer' => 'Experience years must be a valid integer.',
            'experience_years.max' => 'Experience years cannot exceed 50.',
            'skills.array' => 'Skills must be provided as an array.',
            'skills.*.exists' => 'One or more selected skills do not exist.',
            'bio.max' => 'Bio cannot exceed 1000 characters.',
            'linkedin_url.url' => 'LinkedIn URL must be a valid URL.',
            'github_url.url' => 'GitHub URL must be a valid URL.',
            'website_url.url' => 'Website URL must be a valid URL.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, or JPG file.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.',
            'is_active.boolean' => 'Active status must be true or false.',
            'is_featured.boolean' => 'Featured status must be true or false.',
            'visibility.in' => 'Visibility must be one of: public, private, limited.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone' => 'phone number',
            'date_of_birth' => 'date of birth',
            'gender' => 'gender',
            'address' => 'address',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'postal_code' => 'postal code',
            'career_level_id' => 'career level',
            'industry_id' => 'industry',
            'current_salary' => 'current salary',
            'expected_salary' => 'expected salary',
            'salary_currency_id' => 'currency',
            'is_immediate_available' => 'immediate availability',
            'experience_years' => 'years of experience',
            'skills' => 'skills',
            'languages' => 'languages',
            'bio' => 'biography',
            'linkedin_url' => 'LinkedIn URL',
            'github_url' => 'GitHub URL',
            'website_url' => 'website URL',
            'avatar' => 'profile picture',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'visibility' => 'visibility setting',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate salary relationship
            if ($this->has(['current_salary', 'expected_salary'])) {
                if ($this->current_salary > 0 && $this->expected_salary > 0) {
                    if ($this->expected_salary < $this->current_salary) {
                        $validator->errors()->add('expected_salary', 'Expected salary should be at least equal to current salary.');
                    }
                }
            }

            // Validate location hierarchy
            if ($this->has('state_id') && !$this->has('country_id')) {
                // If updating state, ensure country is provided or already exists
                $candidate = $this->route('candidate');
                if (!$candidate->country_id) {
                    $validator->errors()->add('country_id', 'Country is required when updating state.');
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Candidate update validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format data
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone),
            ]);
        }

        // Convert boolean strings
        foreach (['is_immediate_available', 'is_active', 'is_featured'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
