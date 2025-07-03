<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ModelSettingsUpdateRequest
 * Enterprise-grade validation for API Model Settings update operations
 */
class ModelSettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'settings' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'settings.*' => [
                'required',
                'array',
            ],
            'settings.*.key' => [
                'required',
                'string',
                'min:1',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
            ],
            'settings.*.value' => [
                'present',
            ],
            'settings.*.type' => [
                'sometimes',
                'string',
                'in:string,integer,boolean,array,object,json',
            ],
            'settings.*.description' => [
                'sometimes',
                'string',
                'max:500',
            ],
            'settings.*.category' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
            'model_type' => [
                'required',
                'string',
                'in:user,company,job,application,skill,profession',
            ],
            'model_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'overwrite_existing' => [
                'sometimes',
                'boolean',
            ],
            'validate_schema' => [
                'sometimes',
                'boolean',
            ],
            'backup_before_update' => [
                'sometimes',
                'boolean',
            ],
            'version_control' => [
                'sometimes',
                'boolean',
            ],
            'metadata' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'metadata.source' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'metadata.updated_by' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'metadata.reason' => [
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'overwrite_existing' => true,
            'validate_schema' => true,
            'backup_before_update' => true,
            'version_control' => true,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'settings.required' => __('validation.custom.model_settings.settings_required'),
            'settings.*.key.regex' => __('validation.custom.model_settings.key_format'),
            'settings.*.value.present' => __('validation.custom.model_settings.value_required'),
            'settings.*.type.in' => __('validation.custom.model_settings.type_invalid'),
            'model_type.in' => __('validation.custom.model_settings.model_type_invalid'),
            'model_id.min' => __('validation.custom.model_settings.model_id_invalid'),
            'metadata.max' => __('validation.custom.model_settings.metadata_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('model_type')) {
            $this->merge(['model_type' => strtolower(trim($this->model_type))]);
        }

        if ($this->has('settings') && is_array($this->settings)) {
            $processedSettings = [];
            foreach ($this->settings as $setting) {
                if (isset($setting['key'])) {
                    $setting['key'] = trim($setting['key']);
                }
                if (isset($setting['category'])) {
                    $setting['category'] = strtolower(trim($setting['category']));
                }
                $processedSettings[] = $setting;
            }
            $this->merge(['settings' => $processedSettings]);
        }
    }
}
