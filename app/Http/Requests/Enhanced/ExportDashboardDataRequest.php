<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ExportDashboardDataRequest
 * Enterprise-grade validation for Enhanced dashboard data export operations
 */
class ExportDashboardDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'export_type' => [
                'required',
                'string',
                'in:dashboard_summary,analytics_report,performance_metrics,user_activity,system_health',
            ],
            'format' => [
                'required',
                'string',
                'in:csv,excel,pdf,json',
            ],
            'date_range' => [
                'sometimes',
                'array',
            ],
            'date_range.from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'date_range.to' => [
                'sometimes',
                'date',
                'after_or_equal:date_range.from',
                'before_or_equal:today',
            ],
            'filters' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'filters.user_types' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'filters.user_types.*' => [
                'string',
                'in:candidate,employer,admin',
            ],
            'filters.activity_types' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'filters.activity_types.*' => [
                'string',
                'in:login,logout,job_posted,application_received,profile_updated,data_export',
            ],
            'include_metadata' => [
                'sometimes',
                'boolean',
            ],
            'include_charts' => [
                'sometimes',
                'boolean',
            ],
            'compression' => [
                'sometimes',
                'string',
                'in:none,zip,gzip',
            ],
            'delivery_method' => [
                'sometimes',
                'string',
                'in:download,email,storage',
            ],
            'email_recipient' => [
                'required_if:delivery_method,email',
                'email:rfc,dns',
                'max:255',
            ],
            'custom_fields' => [
                'sometimes',
                'array',
                'max:50',
            ],
            'custom_fields.*' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'include_metadata' => true,
            'include_charts' => false,
            'compression' => 'none',
            'delivery_method' => 'download',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'export_type.required' => __('validation.custom.export.type_required'),
            'export_type.in' => __('validation.custom.export.type_invalid'),
            'format.required' => __('validation.custom.export.format_required'),
            'format.in' => __('validation.custom.export.format_invalid'),
            'date_range.to.after_or_equal' => __('validation.custom.export.date_range_invalid'),
            'filters.max' => __('validation.custom.export.filters_limit'),
            'email_recipient.required_if' => __('validation.custom.export.email_required'),
            'custom_fields.max' => __('validation.custom.export.fields_limit'),
            'custom_fields.*.regex' => __('validation.custom.export.field_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['filters.user_types', 'filters.activity_types', 'custom_fields'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }

        if ($this->has('email_recipient')) {
            $this->merge(['email_recipient' => strtolower(trim($this->email_recipient))]);
        }
    }
}
