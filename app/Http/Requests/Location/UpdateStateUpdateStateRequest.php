<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\Location;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;

/**
 * Class UpdateStateRequest
 * Enterprise-grade validation for updating an existing State
 */
class UpdateStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system, adjust if authorization is needed
    }

    public function rules(): array
    {
        $stateId = $this->route(\'state\') ? $this->route(\'state\')->id : null;
        $countryId = $this->input(\'country_id\');

        return [
            \'name\' => [
                \'required\',
                \'string\',
                \'max:255\',
                Rule::unique(\'states\', \'name\')->where(function ($query) use ($countryId) {
                    return $query->where(\'country_id\', $countryId);
                })->ignore($stateId),
            ],
            \'country_id\' => [
                \'required\',
                \'integer\',
                Rule::exists(\'countries\', \'id\'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            \'name.required\' => __(\'validation.custom.state.name_required\'),
            \'name.unique\' => __(\'validation.custom.state.name_unique\'),
            \'country_id.required\' => __(\'validation.custom.state.country_required\'),
            \'country_id.exists\' => __(\'validation.custom.state.country_exists\'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has(\'name\')) {
            $this->merge([\'name\' => trim($this->input(\'name\'))]);
        }
    }
} 