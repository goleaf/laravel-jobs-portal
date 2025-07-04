<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeepAnalyticsRequest
 * Enterprise-grade validation for Enhanced deep analytics operations
 */
class DeepAnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'analysis_type' => [
                'required',
                'string',
                'in:user_behavior,market_trends,performance_metrics,predictive_analysis,correlation_study',
            ],
            'data_sources' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'data_sources.*' => [
                'string',
                'in:jobs,applications,companies,candidates,interactions,system_logs,external_apis',
            ],
            'time_frame' => [
                'required',
                'array',
            ],
            'time_frame.start' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'time_frame.end' => [
                'required',
                'date',
                'after_or_equal:time_frame.start',
                'before_or_equal:today',
            ],
            'metrics' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'metrics.*' => [
                'string',
                'in:conversion_rates,engagement_scores,retention_rates,growth_patterns,user_satisfaction',
            ],
            'segmentation' => [
                'sometimes',
                'array',
            ],
            'segmentation.by_location' => [
                'sometimes',
                'boolean',
            ],
            'segmentation.by_industry' => [
                'sometimes',
                'boolean',
            ],
            'segmentation.by_experience' => [
                'sometimes',
                'boolean',
            ],
            'advanced_options' => [
                'sometimes',
                'array',
            ],
            'advanced_options.machine_learning' => [
                'sometimes',
                'boolean',
            ],
            'advanced_options.statistical_models' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'advanced_options.statistical_models.*' => [
                'string',
                'in:regression,clustering,classification,time_series,anomaly_detection',
            ],
            'output_format' => [
                'sometimes',
                'string',
                'in:interactive_dashboard,static_report,raw_data,visualization_only',
            ],
            'confidence_level' => [
                'sometimes',
                'numeric',
                'min:0.8',
                'max:0.99',
            ],
            'sample_size' => [
                'sometimes',
                'integer',
                'min:100',
                'max:1000000',
            ],
            'include_predictions' => [
                'sometimes',
                'boolean',
            ],
            'prediction_horizon' => [
                'required_if:include_predictions,true',
                'string',
                'in:1_week,1_month,3_months,6_months,1_year',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'output_format' => 'interactive_dashboard',
            'confidence_level' => 0.95,
            'include_predictions' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'analysis_type.required' => __('validation.custom.analytics.type_required'),
            'analysis_type.in' => __('validation.custom.analytics.type_invalid'),
            'data_sources.required' => __('validation.custom.analytics.sources_required'),
            'data_sources.min' => __('validation.custom.analytics.sources_min'),
            'data_sources.max' => __('validation.custom.analytics.sources_limit'),
            'time_frame.start.required' => __('validation.custom.analytics.start_date_required'),
            'time_frame.end.after_or_equal' => __('validation.custom.analytics.date_range_invalid'),
            'metrics.max' => __('validation.custom.analytics.metrics_limit'),
            'confidence_level.min' => __('validation.custom.analytics.confidence_too_low'),
            'prediction_horizon.required_if' => __('validation.custom.analytics.horizon_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['data_sources', 'metrics', 'advanced_options.statistical_models'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
