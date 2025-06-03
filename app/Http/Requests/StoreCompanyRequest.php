<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employer'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // User data
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            
            // Company data
            'ceo' => 'required|string|max:180',
            'industry_id' => 'required|exists:industries,id',
            'ownership_type_id' => 'required|exists:ownership_types,id',
            'company_size_id' => 'required|exists:company_sizes,id',
            'established_in' => 'required|integer|min:1900|max:' . date('Y'),
            'website' => 'nullable|url|max:255',
            'location' => 'required|string|max:255',
            'no_of_offices' => 'required|integer|min:1|max:1000',
            'details' => 'nullable|string|max:5000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'ceo.required' => 'CEO name is required.',
            'industry_id.required' => 'Please select an industry.',
            'industry_id.exists' => 'Selected industry is invalid.',
            'ownership_type_id.required' => 'Please select an ownership type.',
            'ownership_type_id.exists' => 'Selected ownership type is invalid.',
            'company_size_id.required' => 'Please select company size.',
            'company_size_id.exists' => 'Selected company size is invalid.',
            'established_in.required' => 'Establishment year is required.',
            'established_in.min' => 'Establishment year must be after 1900.',
            'established_in.max' => 'Establishment year cannot be in the future.',
            'website.url' => 'Website must be a valid URL.',
            'location.required' => 'Company location is required.',
            'no_of_offices.required' => 'Number of offices is required.',
            'no_of_offices.min' => 'Number of offices must be at least 1.',
            'no_of_offices.max' => 'Number of offices cannot exceed 1000.',
            'details.max' => 'Company details cannot exceed 5000 characters.',
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
            'email' => 'email address',
            'ceo' => 'CEO name',
            'industry_id' => 'industry',
            'ownership_type_id' => 'ownership type',
            'company_size_id' => 'company size',
            'established_in' => 'establishment year',
            'no_of_offices' => 'number of offices',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
} 