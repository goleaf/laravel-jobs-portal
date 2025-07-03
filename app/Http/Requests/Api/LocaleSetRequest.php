<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class LocaleSetRequest
 * Enterprise-grade validation for API Locale setting operations
 */
class LocaleSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'locale' => [
                'required',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
                Rule::exists('languages', 'iso_code')->where('is_active', 1),
            ],
            'persist' => [
                'sometimes',
                'boolean',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
            'date_format' => [
                'sometimes',
                'string',
                'in:Y-m-d,d/m/Y,m/d/Y,d.m.Y,d-m-Y',
            ],
            'time_format' => [
                'sometimes',
                'string',
                'in:H:i,h:i A,H:i:s,h:i:s A',
            ],
            'currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'number_format' => [
                'sometimes',
                'array',
            ],
            'number_format.decimal_separator' => [
                'sometimes',
                'string',
                'size:1',
                'in:.,',
            ],
            'number_format.thousands_separator' => [
                'sometimes',
                'string',
                'max:1',
                'in:,, ,,',
            ],
            'rtl' => [
                'sometimes',
                'boolean',
            ],
            'fallback_locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'user_preference' => [
                'sometimes',
                'boolean',
            ],
            'session_only' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'persist' => true,
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'currency' => 'USD',
            'rtl' => false,
            'fallback_locale' => 'en',
            'user_preference' => false,
            'session_only' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'locale.required' => __('validation.custom.locale.required'),
            'locale.regex' => __('validation.custom.locale.format'),
            'locale.exists' => __('validation.custom.locale.not_supported'),
            'timezone.timezone' => __('validation.custom.locale.timezone_invalid'),
            'date_format.in' => __('validation.custom.locale.date_format_invalid'),
            'time_format.in' => __('validation.custom.locale.time_format_invalid'),
            'currency.regex' => __('validation.custom.locale.currency_format'),
            'number_format.decimal_separator.in' => __('validation.custom.locale.decimal_separator_invalid'),
            'fallback_locale.regex' => __('validation.custom.locale.fallback_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        if ($this->has('currency')) {
            $this->merge(['currency' => strtoupper(trim($this->currency))]);
        }

        if ($this->has('fallback_locale')) {
            $this->merge(['fallback_locale' => strtolower(trim($this->fallback_locale))]);
        }
    }
}
