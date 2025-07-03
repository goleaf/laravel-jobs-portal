<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AuthLoginRequest
 * Enterprise-grade validation for API Auth login operations
 */
class AuthLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
            'remember' => [
                'sometimes',
                'boolean',
            ],
            'device_name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'device_type' => [
                'sometimes',
                'string',
                'in:mobile,tablet,desktop,api_client',
            ],
            'ip_address' => [
                'sometimes',
                'ip',
            ],
            'user_agent' => [
                'sometimes',
                'string',
                'max:500',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'captcha_token' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            'two_factor_code' => [
                'sometimes',
                'string',
                'regex:/^[0-9]{6}$/',
            ],
            'api_version' => [
                'sometimes',
                'string',
                'in:v1,v2,latest',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'remember' => false,
            'device_type' => 'api_client',
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.custom.auth.email_required'),
            'email.email' => __('validation.custom.auth.email_format'),
            'password.required' => __('validation.custom.auth.password_required'),
            'password.min' => __('validation.custom.auth.password_min_length'),
            'device_type.in' => __('validation.custom.auth.device_type_invalid'),
            'timezone.timezone' => __('validation.custom.auth.timezone_invalid'),
            'locale.regex' => __('validation.custom.auth.locale_format'),
            'two_factor_code.regex' => __('validation.custom.auth.two_factor_format'),
            'api_version.in' => __('validation.custom.auth.api_version_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }

        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        if ($this->has('device_name')) {
            $this->merge(['device_name' => trim($this->device_name)]);
        }
    }
}
