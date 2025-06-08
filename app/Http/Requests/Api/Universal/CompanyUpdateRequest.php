<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $company = $this->route('company');
        return auth()->check() && (
            auth()->user()->id === $company->user_id ||
            auth()->user()->hasRole('admin')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $companyId = $this->route('company')->id;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($companyId),
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('companies', 'slug')->ignore($companyId),
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('companies', 'email')->ignore($companyId),
            ],
            'phone' => 'sometimes|string|max:20',
            'website' => 'sometimes|url|max:255',
            'description' => 'sometimes|string|max:2000',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,svg|max:2048',
            'cover_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
            'industry_id' => 'sometimes|integer|exists:industries,id',
            'company_size_id' => 'sometimes|integer|exists:company_sizes,id',
            'ownership_type_id' => 'sometimes|integer|exists:ownership_types,id',
            'employee_count' => 'sometimes|integer|min:0|max:1000000',
            'founded_year' => 'sometimes|integer|min:1800|max:' . date('Y'),
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
            'is_active' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,inactive,pending,suspended',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required when provided.',
            'name.unique' => 'A company with this name already exists.',
            'slug.unique' => 'This company slug is already taken.',
            'email.required' => 'Company email is required when provided.',
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
            'employee_count.max' => 'Employee count cannot exceed 1,000,000.',
            'founded_year.min' => 'Founded year cannot be before 1800.',
            'founded_year.max' => 'Founded year cannot be in the future.',
            'revenue.numeric' => 'Revenue must be a valid number.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'status.in' => 'Status must be one of: active, inactive, pending, suspended.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
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
            'employee_count' => 'number of employees',
            'founded_year' => 'year founded',
            'revenue' => 'annual revenue',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'is_featured' => 'featured status',
            'is_verified' => 'verification status',
            'is_active' => 'active status',
            'status' => 'company status',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Company update validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Update slug if name is being updated
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone)
            ]);
        }

        // Convert boolean strings
        foreach (['is_featured', 'is_verified', 'is_remote_friendly', 'is_active'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate location hierarchy
            if ($this->has('state_id') && !$this->has('country_id')) {
                $company = $this->route('company');
                if (!$company->country_id) {
                    $validator->errors()->add('country_id', 'Country is required when updating state.');
                }
            }

            // Validate revenue currency requirement
            if ($this->has('revenue') && $this->revenue > 0 && !$this->has('revenue_currency_id')) {
                $company = $this->route('company');
                if (!$company->revenue_currency_id) {
                    $validator->errors()->add('revenue_currency_id', 'Currency is required when revenue is specified.');
                }
            }
        });
    }
} 