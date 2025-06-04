<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
  'first_name' => 'required|string|max:255',
  'last_name' => 'nullable|string|max:255',
  'email' => 'required|email|unique:users,email',
  'password' => 'required|string|min:8|confirmed',
  'phone' => 'nullable|string|max:20',
  'is_active' => 'boolean',
);
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return array (
  'first_name.required' => 'First name is required',
  'email.required' => 'Email is required',
  'email.unique' => 'Email already exists',
  'password.required' => 'Password is required',
  'password.min' => 'Password must be at least 8 characters',
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
