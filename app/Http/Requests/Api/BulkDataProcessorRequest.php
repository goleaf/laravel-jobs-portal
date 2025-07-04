<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BulkDataProcessorRequest
 * Enterprise-grade validation for API Bulk Data Processor operations
 */
class BulkDataProcessorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'operation' => [
                'required',
                'string',
                'in:create,update,delete,import,export,sync,validate',
            ],
            'data_type' => [
                'required',
                'string',
                'in:jobs,companies,users,applications,skills,professions,categories',
            ],
            'data' => [
                'required_unless:operation,export',
                'array',
                'min:1',
                'max:1000',
            ],
            'data.*.id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'validation_rules' => [
                'sometimes',
                'array',
            ],
            'validation_mode' => [
                'sometimes',
                'string',
                'in:strict,lenient,skip',
            ],
            'batch_size' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'transaction_mode' => [
                'sometimes',
                'boolean',
            ],
            'rollback_on_error' => [
                'sometimes',
                'boolean',
            ],
            'continue_on_error' => [
                'sometimes',
                'boolean',
            ],
            'dry_run' => [
                'sometimes',
                'boolean',
            ],
            'backup_before_operation' => [
                'sometimes',
                'boolean',
            ],
            'async_processing' => [
                'sometimes',
                'boolean',
            ],
            'callback_url' => [
                'sometimes',
                'url',
                'max:255',
            ],
            'filters' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'filters.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'filters.*.operator' => [
                'required',
                'string',
                'in:equals,not_equals,contains,starts_with,ends_with,greater_than,less_than,in,not_in',
            ],
            'filters.*.value' => [
                'required',
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,csv,xml,xlsx',
            ],
            'compression' => [
                'sometimes',
                'string',
                'in:none,gzip,zip',
            ],
            'chunk_processing' => [
                'sometimes',
                'boolean',
            ],
            'chunk_size' => [
                'sometimes',
                'integer',
                'min:10',
                'max:1000',
            ],
            'metadata' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'metadata.job_id' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'metadata.source' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'metadata.description' => [
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
            'batch_size' => 50,
            'transaction_mode' => true,
            'rollback_on_error' => true,
            'continue_on_error' => false,
            'dry_run' => false,
            'backup_before_operation' => true,
            'async_processing' => false,
            'sort_direction' => 'asc',
            'format' => 'json',
            'compression' => 'none',
            'chunk_processing' => false,
            'chunk_size' => 100,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'operation.required' => __('validation.custom.bulk_processor.operation_required'),
            'operation.in' => __('validation.custom.bulk_processor.operation_invalid'),
            'data_type.required' => __('validation.custom.bulk_processor.data_type_required'),
            'data_type.in' => __('validation.custom.bulk_processor.data_type_invalid'),
            'data.required_unless' => __('validation.custom.bulk_processor.data_required'),
            'data.max' => __('validation.custom.bulk_processor.data_limit'),
            'batch_size.max' => __('validation.custom.bulk_processor.batch_size_limit'),
            'validation_mode.in' => __('validation.custom.bulk_processor.validation_mode_invalid'),
            'callback_url.url' => __('validation.custom.bulk_processor.callback_url_invalid'),
            'filters.max' => __('validation.custom.bulk_processor.filters_limit'),
            'filters.*.operator.in' => __('validation.custom.bulk_processor.filter_operator_invalid'),
            'format.in' => __('validation.custom.bulk_processor.format_invalid'),
            'compression.in' => __('validation.custom.bulk_processor.compression_invalid'),
            'chunk_size.max' => __('validation.custom.bulk_processor.chunk_size_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('operation')) {
            $this->merge(['operation' => strtolower(trim($this->operation))]);
        }

        if ($this->has('data_type')) {
            $this->merge(['data_type' => strtolower(trim($this->data_type))]);
        }

        if ($this->has('validation_mode')) {
            $this->merge(['validation_mode' => strtolower(trim($this->validation_mode))]);
        }

        if ($this->has('format')) {
            $this->merge(['format' => strtolower(trim($this->format))]);
        }

        if ($this->has('compression')) {
            $this->merge(['compression' => strtolower(trim($this->compression))]);
        }
    }
}
