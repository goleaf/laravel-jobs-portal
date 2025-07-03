<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UniversalExportRequest
 * Enterprise-grade validation for Universal Export operations across all entities
 */
class UniversalExportRequest extends FormRequest
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
                'in:jobs,companies,candidates,skills,professions,categories,applications,analytics,reports,system_data',
            ],
            'format' => [
                'required',
                'string',
                'in:csv,xlsx,json,xml,pdf,sql,parquet',
            ],
            'filters' => [
                'sometimes',
                'array',
                'max:25',
            ],
            'filters.*.field' => [
                'required',
                'string',
                'max:50',
            ],
            'filters.*.operator' => [
                'required',
                'string',
                'in:equals,not_equals,contains,starts_with,ends_with,greater_than,less_than,between,in,not_in',
            ],
            'filters.*.value' => [
                'required',
            ],
            'columns' => [
                'sometimes',
                'array',
                'max:100',
            ],
            'columns.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\.]+$/',
            ],
            'exclude_columns' => [
                'sometimes',
                'array',
                'max:50',
            ],
            'exclude_columns.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\.]+$/',
            ],
            'relationships' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'relationships.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\.]+$/',
            ],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100000',
            ],
            'chunk_size' => [
                'sometimes',
                'integer',
                'min:100',
                'max:10000',
            ],
            'compression' => [
                'sometimes',
                'string',
                'in:none,zip,gzip,bz2',
            ],
            'encryption' => [
                'sometimes',
                'boolean',
            ],
            'password_protection' => [
                'sometimes',
                'boolean',
            ],
            'password' => [
                'required_if:password_protection,true',
                'string',
                'min:8',
                'max:50',
            ],
            'delivery_method' => [
                'sometimes',
                'string',
                'in:download,email,ftp,s3,webhook',
            ],
            'delivery_options' => [
                'sometimes',
                'array',
            ],
            'delivery_options.email' => [
                'required_if:delivery_method,email',
                'email',
                'max:255',
            ],
            'delivery_options.webhook_url' => [
                'required_if:delivery_method,webhook',
                'url',
                'max:255',
            ],
            'schedule' => [
                'sometimes',
                'array',
            ],
            'schedule.enabled' => [
                'boolean',
            ],
            'schedule.frequency' => [
                'sometimes',
                'string',
                'in:once,daily,weekly,monthly,quarterly,yearly',
            ],
            'schedule.time' => [
                'sometimes',
                'date_format:H:i',
            ],
            'schedule.timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
            'template' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
            'custom_headers' => [
                'sometimes',
                'array',
                'max:50',
            ],
            'custom_headers.*' => [
                'string',
                'max:100',
            ],
            'date_format' => [
                'sometimes',
                'string',
                'in:Y-m-d,d/m/Y,m/d/Y,d.m.Y,ISO8601',
            ],
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'include_metadata' => [
                'sometimes',
                'boolean',
            ],
            'metadata_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'metadata_fields.*' => [
                'string',
                'in:created_at,updated_at,created_by,export_date,total_records,schema_version',
            ],
            'async_processing' => [
                'sometimes',
                'boolean',
            ],
            'job_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
            'callback_url' => [
                'sometimes',
                'url',
                'max:255',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'format' => 'csv',
            'limit' => 10000,
            'chunk_size' => 1000,
            'compression' => 'none',
            'encryption' => false,
            'password_protection' => false,
            'delivery_method' => 'download',
            'date_format' => 'Y-m-d',
            'locale' => app()->getLocale(),
            'include_metadata' => false,
            'async_processing' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'entity_type.required' => __('validation.custom.export.entity_type_required'),
            'entity_type.in' => __('validation.custom.export.entity_type_invalid'),
            'format.required' => __('validation.custom.export.format_required'),
            'format.in' => __('validation.custom.export.format_invalid'),
            'filters.max' => __('validation.custom.export.filters_limit'),
            'columns.max' => __('validation.custom.export.columns_limit'),
            'columns.*.regex' => __('validation.custom.export.column_format'),
            'limit.max' => __('validation.custom.export.limit_exceeded'),
            'chunk_size.max' => __('validation.custom.export.chunk_size_limit'),
            'compression.in' => __('validation.custom.export.compression_invalid'),
            'password.required_if' => __('validation.custom.export.password_required'),
            'delivery_method.in' => __('validation.custom.export.delivery_method_invalid'),
            'delivery_options.email.required_if' => __('validation.custom.export.email_required'),
            'delivery_options.webhook_url.required_if' => __('validation.custom.export.webhook_required'),
            'schedule.frequency.in' => __('validation.custom.export.frequency_invalid'),
            'locale.regex' => __('validation.custom.export.locale_format'),
            'job_id.regex' => __('validation.custom.export.job_id_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('entity_type')) {
            $this->merge(['entity_type' => strtolower(trim($this->entity_type))]);
        }

        if ($this->has('format')) {
            $this->merge(['format' => strtolower(trim($this->format))]);
        }

        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        foreach (['columns', 'exclude_columns', 'relationships', 'custom_headers', 'metadata_fields'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
