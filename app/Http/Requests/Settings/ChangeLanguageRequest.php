<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ChangeLanguageRequest
 * Enterprise-grade validation for language switching operations
 */
class ChangeLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
                Rule::exists('languages', 'iso_code')->where('is_active', 1),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'locale.exists' => __('validation.custom.language.locale_not_available'),
            'locale.regex' => __('validation.custom.language.locale_format'),
            'locale.size' => __('validation.custom.language.locale_size'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Get locale from route parameter if not in request
        if (! $this->has('locale') && $this->route('locale')) {
            $this->merge(['locale' => $this->route('locale')]);
        }

        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }
    }
}
