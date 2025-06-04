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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array (
  'name' => 'required|string|max:255',
  'email' => 'required|email|unique:companies,email',
  'phone' => 'nullable|string|max:20',
  'website' => 'nullable|url',
  'industry_id' => 'nullable|exists:industries,id',
  'ownership_type_id' => 'nullable|exists:ownership_types,id',
  'company_size_id' => 'nullable|exists:company_sizes,id',
  'established_in' => 'nullable|integer|min:1800|max:2025',
  'description' => 'nullable|string',
  'country_id' => 'required|exists:countries,id',
  'state_id' => 'nullable|exists:states,id',
  'city_id' => 'nullable|exists:cities,id',
  'address' => 'nullable|string',
  'postal_code' => 'nullable|string|max:20',
  'phone_verified' => 'boolean',
  'email_verified' => 'boolean',
  'is_active' => 'boolean',
);
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return array (
  'name.required' => 'Company name is required',
  'email.required' => 'Email is required',
  'email.unique' => 'Email already exists',
  'website.url' => 'Please enter a valid website URL',
  'country_id.required' => 'Country is required',
  'established_in.min' => 'Establishment year cannot be before 1800',
  'established_in.max' => 'Establishment year cannot be in the future',
);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('messages.common.first_name'),
            'last_name' => __('messages.common.last_name'),
            'email' => __('messages.common.email'),
            'password' => __('messages.common.password'),
            'phone' => __('messages.common.phone'),
            'name' => __('messages.common.name'),
            'description' => __('messages.common.description'),
            'address' => __('messages.common.address'),
            'website' => __('messages.common.website'),
            'country_id' => __('messages.common.country'),
            'state_id' => __('messages.common.state'),
            'city_id' => __('messages.common.city'),
            'job_title' => __('messages.job.job_title'),
            'job_description' => __('messages.job.job_description'),
            'salary_from' => __('messages.job.salary_from'),
            'salary_to' => __('messages.job.salary_to'),
            'job_expiry_date' => __('messages.job.job_expiry_date'),
        ];
    }
}
