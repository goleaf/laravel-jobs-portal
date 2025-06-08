<?php

namespace App\Http\Controllers\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
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
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ],
            'remember' => 'nullable|boolean',
            'device_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'remember.boolean' => 'The remember field must be true or false.',
            'device_name.string' => 'The device name must be a string.',
            'device_name.max' => 'The device name may not be greater than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email Address',
            'password' => 'Password',
            'remember' => 'Remember Me',
            'device_name' => 'Device Name',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        if (!$this->has('device_name') || empty($this->device_name)) {
            $this->merge([
                'device_name' => $this->userAgent() ?: 'Unknown Device'
            ]);
        }
    }
}