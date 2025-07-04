<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class AuthRegisterRequest
 * Enterprise-grade validation for API Auth register operations
 */
class AuthRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'terms_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            'privacy_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            'marketing_consent' => [
                'sometimes',
                'boolean',
            ],
            'phone' => [
                'sometimes',
                'string',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'min:10',
                'max:20',
            ],
            'date_of_birth' => [
                'sometimes',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'gender' => [
                'sometimes',
                'string',
                'in:male,female,other,prefer_not_to_say',
            ],
            'nationality' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'preferred_language' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
            'referral_code' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'source' => [
                'sometimes',
                'string',
                'in:web,mobile,api,referral,social,advertisement',
            ],
            'device_info' => [
                'sometimes',
                'array',
            ],
            'device_info.type' => [
                'sometimes',
                'string',
                'in:desktop,mobile,tablet',
            ],
            'device_info.browser' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'device_info.os' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'captcha_token' => [
                'sometimes',
                'string',
                'max:1000',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'marketing_consent' => false,
            'preferred_language' => app()->getLocale(),
            'timezone' => config('app.timezone'),
            'source' => 'api',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.register.name_required'),
            'name.regex' => __('validation.custom.register.name_format'),
            'email.required' => __('validation.custom.register.email_required'),
            'email.unique' => __('validation.custom.register.email_taken'),
            'password.required' => __('validation.custom.register.password_required'),
            'password.confirmed' => __('validation.custom.register.password_confirmation'),
            'terms_accepted.required' => __('validation.custom.register.terms_required'),
            'terms_accepted.accepted' => __('validation.custom.register.terms_must_accept'),
            'privacy_accepted.required' => __('validation.custom.register.privacy_required'),
            'privacy_accepted.accepted' => __('validation.custom.register.privacy_must_accept'),
            'phone.regex' => __('validation.custom.register.phone_format'),
            'date_of_birth.before' => __('validation.custom.register.age_invalid'),
            'gender.in' => __('validation.custom.register.gender_invalid'),
            'nationality.regex' => __('validation.custom.register.nationality_format'),
            'preferred_language.regex' => __('validation.custom.register.language_format'),
            'timezone.timezone' => __('validation.custom.register.timezone_invalid'),
            'referral_code.regex' => __('validation.custom.register.referral_format'),
            'source.in' => __('validation.custom.register.source_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->name)]);
        }

        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }

        if ($this->has('nationality')) {
            $this->merge(['nationality' => strtoupper(trim($this->nationality))]);
        }

        if ($this->has('preferred_language')) {
            $this->merge(['preferred_language' => strtolower(trim($this->preferred_language))]);
        }

        if ($this->has('referral_code')) {
            $this->merge(['referral_code' => trim($this->referral_code)]);
        }
    }
}
