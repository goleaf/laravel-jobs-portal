<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UniversalValidationRequest
 * Enterprise-grade validation for Universal Validation operations across all entities
 */
class UniversalValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'entity_type' => [
                'required',
                'string',
                'in:job,company,candidate,skill,profession,category,application,user,profile,setting',
            ],
            'validation_type' => [
                'required',
                'string',
                'in:data_integrity,business_rules,security,performance,compliance,schema',
            ],
            'data' => [
                'required',
                'array',
                'min:1',
                'max:1000',
            ],
            'validation_rules' => [
                'sometimes',
                'array',
                'max:50',
            ],
            'validation_rules.*' => [
                'array',
            ],
            'validation_rules.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'validation_rules.*.rule' => [
                'required',
                'string',
                'max:200',
            ],
            'validation_rules.*.message' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'validation_rules.*.severity' => [
                'sometimes',
                'string',
                'in:error,warning,info',
            ],
            'custom_validators' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'custom_validators.*' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
            'validation_context' => [
                'sometimes',
                'array',
            ],
            'validation_context.environment' => [
                'sometimes',
                'string',
                'in:development,staging,production,testing',
            ],
            'validation_context.user_role' => [
                'sometimes',
                'string',
                'in:admin,user,guest,system',
            ],
            'validation_context.operation' => [
                'sometimes',
                'string',
                'in:create,update,delete,import,export',
            ],
            'strict_mode' => [
                'sometimes',
                'boolean',
            ],
            'fail_fast' => [
                'sometimes',
                'boolean',
            ],
            'collect_all_errors' => [
                'sometimes',
                'boolean',
            ],
            'include_warnings' => [
                'sometimes',
                'boolean',
            ],
            'business_rules' => [
                'sometimes',
                'array',
                'max:30',
            ],
            'business_rules.*' => [
                'array',
            ],
            'business_rules.*.name' => [
                'required',
                'string',
                'max:100',
            ],
            'business_rules.*.condition' => [
                'required',
                'string',
                'max:500',
            ],
            'business_rules.*.action' => [
                'required',
                'string',
                'in:reject,warn,modify,log',
            ],
            'schema_validation' => [
                'sometimes',
                'array',
            ],
            'schema_validation.enabled' => [
                'boolean',
            ],
            'schema_validation.schema_version' => [
                'sometimes',
                'string',
                'max:20',
            ],
            'schema_validation.allow_additional_fields' => [
                'sometimes',
                'boolean',
            ],
            'performance_limits' => [
                'sometimes',
                'array',
            ],
            'performance_limits.max_execution_time' => [
                'sometimes',
                'integer',
                'min:1',
                'max:300',
            ],
            'performance_limits.max_memory_mb' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1024',
            ],
            'output_format' => [
                'sometimes',
                'string',
                'in:json,xml,detailed,summary',
            ],
            'include_suggestions' => [
                'sometimes',
                'boolean',
            ],
            'localization' => [
                'sometimes',
                'array',
            ],
            'localization.locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'localization.fallback_locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'logging' => [
                'sometimes',
                'array',
            ],
            'logging.enabled' => [
                'boolean',
            ],
            'logging.level' => [
                'sometimes',
                'string',
                'in:debug,info,warning,error',
            ],
            'logging.include_data' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'validation_type' => 'data_integrity',
            'strict_mode' => true,
            'fail_fast' => false,
            'collect_all_errors' => true,
            'include_warnings' => true,
            'schema_validation.enabled' => true,
            'schema_validation.allow_additional_fields' => false,
            'performance_limits.max_execution_time' => 30,
            'performance_limits.max_memory_mb' => 128,
            'output_format' => 'detailed',
            'include_suggestions' => true,
            'localization.locale' => app()->getLocale(),
            'localization.fallback_locale' => 'en',
            'logging.enabled' => true,
            'logging.level' => 'info',
            'logging.include_data' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'entity_type.required' => __('validation.custom.universal_validation.entity_type_required'),
            'entity_type.in' => __('validation.custom.universal_validation.entity_type_invalid'),
            'validation_type.required' => __('validation.custom.universal_validation.validation_type_required'),
            'validation_type.in' => __('validation.custom.universal_validation.validation_type_invalid'),
            'data.required' => __('validation.custom.universal_validation.data_required'),
            'data.max' => __('validation.custom.universal_validation.data_limit'),
            'validation_rules.max' => __('validation.custom.universal_validation.rules_limit'),
            'custom_validators.max' => __('validation.custom.universal_validation.validators_limit'),
            'custom_validators.*.regex' => __('validation.custom.universal_validation.validator_format'),
            'business_rules.max' => __('validation.custom.universal_validation.business_rules_limit'),
            'performance_limits.max_execution_time.max' => __('validation.custom.universal_validation.execution_time_limit'),
            'performance_limits.max_memory_mb.max' => __('validation.custom.universal_validation.memory_limit'),
            'output_format.in' => __('validation.custom.universal_validation.output_format_invalid'),
            'localization.locale.regex' => __('validation.custom.universal_validation.locale_format'),
            'logging.level.in' => __('validation.custom.universal_validation.log_level_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['entity_type', 'validation_type', 'output_format'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strtolower(trim($this->input($field)))]);
            }
        }

        if ($this->has('localization.locale')) {
            $localization = $this->input('localization', []);
            $localization['locale'] = strtolower(trim($this->input('localization.locale')));
            $this->merge(['localization' => $localization]);
        }

        if ($this->has('localization.fallback_locale')) {
            $localization = $this->input('localization', []);
            $localization['fallback_locale'] = strtolower(trim($this->input('localization.fallback_locale')));
            $this->merge(['localization' => $localization]);
        }

        if ($this->has('custom_validators') && is_string($this->input('custom_validators'))) {
            $this->merge(['custom_validators' => explode(',', $this->input('custom_validators'))]);
        }
    }
}
