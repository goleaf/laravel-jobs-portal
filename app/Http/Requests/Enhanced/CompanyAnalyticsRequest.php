<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CompanyAnalyticsRequest
 * Enterprise-grade validation for Enhanced company analytics operations
 */
class CompanyAnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('companies', 'id'),
            ],
            'metrics' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'metrics.*' => [
                'string',
                'in:job_views,applications_received,profile_views,candidate_interactions,conversion_rates,hiring_success',
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
            'group_by' => [
                'sometimes',
                'string',
                'in:day,week,month,quarter,year',
            ],
            'filter_by' => [
                'sometimes',
                'array',
            ],
            'filter_by.job_categories' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'filter_by.job_categories.*' => [
                'integer',
                'min:1',
                Rule::exists('job_categories', 'id'),
            ],
            'filter_by.experience_levels' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'filter_by.experience_levels.*' => [
                'string',
                'in:entry_level,mid_level,senior_level,executive',
            ],
            'include_benchmarks' => [
                'sometimes',
                'boolean',
            ],
            'include_forecasts' => [
                'sometimes',
                'boolean',
            ],
            'export_format' => [
                'sometimes',
                'string',
                'in:json,csv,excel,pdf',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'group_by' => 'day',
            'include_benchmarks' => false,
            'include_forecasts' => false,
            'export_format' => 'json',
            'timezone' => config('app.timezone'),
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'company_id.required' => __('validation.custom.analytics.company_required'),
            'company_id.exists' => __('validation.custom.analytics.company_not_found'),
            'metrics.max' => __('validation.custom.analytics.metrics_limit'),
            'metrics.*.in' => __('validation.custom.analytics.metric_invalid'),
            'date_range.to.after_or_equal' => __('validation.custom.analytics.date_range_invalid'),
            'group_by.in' => __('validation.custom.analytics.group_by_invalid'),
            'export_format.in' => __('validation.custom.analytics.format_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['metrics', 'filter_by.job_categories', 'filter_by.experience_levels'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
