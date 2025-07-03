<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateLanguageRequest
 * Enterprise-grade validation for Language update operations
 */
class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        $languageId = $this->route('language')?->id ?? $this->input('id');

        return [
            'language' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-]+$/u',
                Rule::unique('languages', 'language')->ignore($languageId),
            ],
            'iso_code' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
                Rule::unique('languages', 'iso_code')->ignore($languageId),
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
            'language.unique' => __('validation.custom.language.name_unique'),
            'language.regex' => __('validation.custom.language.name_format'),
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
