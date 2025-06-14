<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'is_active' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:0,1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('validation_custom.user.first_name_required'),
            'last_name.required' => __('validation_custom.user.last_name_required'),
            'email.required' => __('validation_custom.user.email_required'),
            'email.unique' => __('validation_custom.user.email_unique'),
            'password.required' => __('validation_custom.user.password_required'),
            'password.min' => __('validation_custom.user.password_min'),
            'password.confirmed' => __('validation_custom.user.password_confirmed'),
            'password_confirmation.required' => 'The password confirmation field is required.',
            'email.email' => 'Please enter a valid email address.',
            'dob.before' => 'Date of birth must be before today.',
            'gender.in' => 'Please select a valid gender option.',
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
            'dob' => 'date of birth',
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
