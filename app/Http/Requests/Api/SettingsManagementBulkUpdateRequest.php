<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SettingsManagementBulkUpdateRequest
 * Enterprise-grade validation for API Settings Management bulk update operations
 */
class SettingsManagementBulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'model_type' => [
                'required',
                'string',
                'in:user,company,job,application,skill,profession,system',
            ],
            'operations' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'operations.*.model_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'operations.*.action' => [
                'required',
                'string',
                'in:update,delete,create,merge',
            ],
            'operations.*.settings' => [
                'required_if:operations.*.action,update,create,merge',
                'array',
                'max:20',
            ],
            'operations.*.settings.*' => [
                'array',
            ],
            'operations.*.settings.*.key' => [
                'required',
                'string',
                'min:1',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
            ],
            'operations.*.settings.*.value' => [
                'present',
            ],
            'operations.*.settings.*.type' => [
                'sometimes',
                'string',
                'in:string,integer,boolean,array,object,json',
            ],
            'operations.*.condition' => [
                'sometimes',
                'array',
            ],
            'operations.*.condition.field' => [
                'required_with:operations.*.condition',
                'string',
                'max:50',
            ],
            'operations.*.condition.operator' => [
                'required_with:operations.*.condition',
                'string',
                'in:equals,not_equals,contains,greater_than,less_than',
            ],
            'operations.*.condition.value' => [
                'required_with:operations.*.condition',
            ],
            'validation_mode' => [
                'sometimes',
                'string',
                'in:strict,lenient,skip',
            ],
            'transaction_mode' => [
                'sometimes',
                'boolean',
            ],
            'backup_before_update' => [
                'sometimes',
                'boolean',
            ],
            'rollback_on_error' => [
                'sometimes',
                'boolean',
            ],
            'batch_size' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'continue_on_error' => [
                'sometimes',
                'boolean',
            ],
            'metadata' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'metadata.batch_id' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'metadata.source' => [
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
            'validation_mode' => 'strict',
            'transaction_mode' => true,
            'backup_before_update' => true,
            'rollback_on_error' => true,
            'batch_size' => 50,
            'continue_on_error' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'model_type.in' => __('validation.custom.bulk_settings.model_type_invalid'),
            'operations.required' => __('validation.custom.bulk_settings.operations_required'),
            'operations.max' => __('validation.custom.bulk_settings.operations_limit'),
            'operations.*.action.in' => __('validation.custom.bulk_settings.action_invalid'),
            'operations.*.settings.*.key.regex' => __('validation.custom.bulk_settings.key_format'),
            'operations.*.condition.operator.in' => __('validation.custom.bulk_settings.operator_invalid'),
            'validation_mode.in' => __('validation.custom.bulk_settings.validation_mode_invalid'),
            'batch_size.max' => __('validation.custom.bulk_settings.batch_size_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('model_type')) {
            $this->merge(['model_type' => strtolower(trim($this->model_type))]);
        }

        if ($this->has('operations') && is_array($this->operations)) {
            $processedOperations = [];
            foreach ($this->operations as $operation) {
                if (isset($operation['action'])) {
                    $operation['action'] = strtolower(trim($operation['action']));
                }
                if (isset($operation['settings']) && is_array($operation['settings'])) {
                    foreach ($operation['settings'] as &$setting) {
                        if (isset($setting['key'])) {
                            $setting['key'] = trim($setting['key']);
                        }
                        if (isset($setting['type'])) {
                            $setting['type'] = strtolower(trim($setting['type']));
                        }
                    }
                }
                $processedOperations[] = $operation;
            }
            $this->merge(['operations' => $processedOperations]);
        }
    }
}
