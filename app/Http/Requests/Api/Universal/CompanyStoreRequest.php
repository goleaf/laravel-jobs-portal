<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Requires authentication
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255|unique:companies,name',
            'slug' => 'sometimes|string|max:255|unique:companies,slug',
            'email' => 'required|email|max:255|unique:companies,email',
            'phone' => 'sometimes|string|max:20',
            'website' => 'sometimes|url|max:255',
            'description' => 'sometimes|string|max:2000',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,svg|max:2048',
            'cover_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
            'industry_id' => 'sometimes|integer|exists:industries,id',
            'company_size_id' => 'sometimes|integer|exists:company_sizes,id',
            'ownership_type_id' => 'sometimes|integer|exists:ownership_types,id',
            'employee_count' => 'sometimes|integer|min:0|max:1000000',
            'founded_year' => 'sometimes|integer|min:1800|max:'.date('Y'),
            'revenue' => 'sometimes|numeric|min:0',
            'revenue_currency_id' => 'sometimes|integer|exists:salary_currencies,id',
            'address' => 'sometimes|string|max:500',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'postal_code' => 'sometimes|string|max:20',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'social_facebook' => 'sometimes|url|max:255',
            'social_twitter' => 'sometimes|url|max:255',
            'social_linkedin' => 'sometimes|url|max:255',
            'social_instagram' => 'sometimes|url|max:255',
            'social_youtube' => 'sometimes|url|max:255',
            'benefits' => 'sometimes|array',
            'benefits.*' => 'string|max:100',
            'specialties' => 'sometimes|array',
            'specialties.*' => 'string|max:100',
            'culture_tags' => 'sometimes|array',
            'culture_tags.*' => 'string|max:50',
            'is_featured' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'is_remote_friendly' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required to create a company.',
            'user_id.exists' => 'The specified user does not exist.',
            'name.required' => 'Company name is required.',
            'name.unique' => 'A company with this name already exists.',
            'slug.unique' => 'This company slug is already taken.',
            'email.required' => 'Company email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered to another company.',
            'website.url' => 'Website must be a valid URL.',
            'description.max' => 'Company description cannot exceed 2000 characters.',
            'logo.image' => 'Company logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or SVG file.',
            'logo.max' => 'Logo file size cannot exceed 2MB.',
            'cover_image.image' => 'Cover image must be an image file.',
            'cover_image.max' => 'Cover image file size cannot exceed 5MB.',
            'industry_id.exists' => 'The selected industry does not exist.',
            'company_size_id.exists' => 'The selected company size does not exist.',
            'ownership_type_id.exists' => 'The selected ownership type does not exist.',
            'employee_count.integer' => 'Employee count must be a valid number.',
            'employee_count.max' => 'Employee count cannot exceed 1,000,000.',
            'founded_year.integer' => 'Founded year must be a valid year.',
            'founded_year.min' => 'Founded year cannot be before 1800.',
            'founded_year.max' => 'Founded year cannot be in the future.',
            'revenue.numeric' => 'Revenue must be a valid number.',
            'country_id.exists' => 'The selected country does not exist.',
            'state_id.exists' => 'The selected state does not exist.',
            'city_id.exists' => 'The selected city does not exist.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'social_facebook.url' => 'Facebook URL must be a valid URL.',
            'social_twitter.url' => 'Twitter URL must be a valid URL.',
            'social_linkedin.url' => 'LinkedIn URL must be a valid URL.',
            'benefits.array' => 'Benefits must be provided as an array.',
            'benefits.*.max' => 'Each benefit cannot exceed 100 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'name' => 'company name',
            'slug' => 'company slug',
            'email' => 'company email',
            'phone' => 'phone number',
            'website' => 'website URL',
            'description' => 'company description',
            'logo' => 'company logo',
            'cover_image' => 'cover image',
            'industry_id' => 'industry',
            'company_size_id' => 'company size',
            'ownership_type_id' => 'ownership type',
            'employee_count' => 'number of employees',
            'founded_year' => 'year founded',
            'revenue' => 'annual revenue',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'postal_code' => 'postal code',
            'social_facebook' => 'Facebook URL',
            'social_twitter' => 'Twitter URL',
            'social_linkedin' => 'LinkedIn URL',
            'benefits' => 'company benefits',
            'specialties' => 'company specialties',
            'is_featured' => 'featured status',
            'is_verified' => 'verification status',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate location hierarchy
            if ($this->has('state_id') && !$this->has('country_id')) {
                $validator->errors()->add('country_id', 'Country is required when state is specified.');
            }

            if ($this->has('city_id') && !$this->has('state_id')) {
                $validator->errors()->add('state_id', 'State is required when city is specified.');
            }

            // Validate revenue currency requirement
            if ($this->has('revenue') && $this->revenue > 0 && !$this->has('revenue_currency_id')) {
                $validator->errors()->add('revenue_currency_id', 'Currency is required when revenue is specified.');
            }

            // Validate social media URLs
            $socialFields = ['social_facebook', 'social_twitter', 'social_linkedin'];
            foreach ($socialFields as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $platform = str_replace('social_', '', $field);
                    if (!str_contains($this->{$field}, $platform.'.com')) {
                        $validator->errors()->add($field, "Please provide a valid {$platform} URL.");
                    }
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
                'message' => 'Company creation validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug from name if not provided
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Str::slug($this->name),
            ]);
        }

        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone),
            ]);
        }

        // Convert boolean strings
        foreach (['is_featured', 'is_verified', 'is_remote_friendly'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
