<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\Location;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;

/**
 * Class UpdateCountryRequest
 * Enterprise-grade validation for updating an existing Country
 */
class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system, adjust if authorization is needed
    }

    public function rules(): array
    {
        $countryId = $this->route(\'country\') ? $this->route(\'country\')->id : null;

        return [
            \'name\' => [
                \'required\',
                \'string\',
                \'max:255\',
                Rule::unique(\'countries\', \'name\')->ignore($countryId),
            ],
            \'short_code\' => [
                \'required\',
                \'string\',
                \'max:50\',
                Rule::unique(\'countries\', \'short_code\')->ignore($countryId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            \'name.required\' => __(\'validation.custom.country.name_required\'),
            \'name.unique\' => __(\'validation.custom.country.name_unique\'),
            \'short_code.required\' => __(\'validation.custom.country.short_code_required\'),
            \'short_code.unique\' => __(\'validation.custom.country.short_code_unique\'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach ([\'name\', \'short_code\'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }
} 