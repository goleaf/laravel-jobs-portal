<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreLanguageRequest
 * Enterprise-grade validation for Language creation operations
 */
class StoreLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'language' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-]+$/u',
                Rule::unique('languages', 'language'),
            ],
            'iso_code' => [
                'required',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
                Rule::unique('languages', 'iso_code'),
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_default' => [
                'sometimes',
                'boolean',
            ],
            'rtl' => [
                'sometimes',
                'boolean',
            ],
            'flag_image' => [
                'sometimes',
                'file',
                'mimes:png,jpg,jpeg,svg,webp',
                'max:1024',
                'dimensions:max_width=500,max_height=500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'language.required' => __('validation.custom.language.name_required'),
            'language.unique' => __('validation.custom.language.name_unique'),
            'language.regex' => __('validation.custom.language.name_format'),
            'iso_code.required' => __('validation.custom.language.iso_code_required'),
            'iso_code.unique' => __('validation.custom.language.iso_code_unique'),
            'iso_code.regex' => __('validation.custom.language.iso_code_format'),
            'iso_code.size' => __('validation.custom.language.iso_code_size'),
            'flag_image.dimensions' => __('validation.custom.language.flag_dimensions'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('iso_code')) {
            $this->merge(['iso_code' => strtolower(trim($this->iso_code))]);
        }

        if ($this->has('language')) {
            $this->merge(['language' => trim($this->language)]);
        }
    }
}
