<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RealtimeAnalyticsRequest
 * Enterprise-grade validation for API Realtime Analytics operations
 */
class RealtimeAnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'metrics' => [
                'required',
                'array',
                'min:1',
                'max:20',
            ],
            'metrics.*' => [
                'string',
                'in:users_online,page_views,job_searches,applications_submitted,companies_active,system_load,api_requests,errors_count,response_time,memory_usage,database_queries,cache_hits,storage_usage,network_traffic,security_events',
            ],
            'time_window' => [
                'sometimes',
                'string',
                'in:1m,5m,15m,30m,1h,3h,6h,12h,24h',
            ],
            'granularity' => [
                'sometimes',
                'string',
                'in:second,minute,hour,day',
            ],
            'filters' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'filters.date_range' => [
                'sometimes',
                'array',
            ],
            'filters.date_range.start' => [
                'required_with:filters.date_range',
                'date',
            ],
            'filters.date_range.end' => [
                'required_with:filters.date_range',
                'date',
                'after:filters.date_range.start',
            ],
            'filters.locations' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'filters.locations.*' => [
                'string',
                'max:100',
            ],
            'filters.user_types' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'filters.user_types.*' => [
                'string',
                'in:guest,candidate,employer,admin',
            ],
            'filters.device_types' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'filters.device_types.*' => [
                'string',
                'in:desktop,mobile,tablet,api_client',
            ],
            'aggregation' => [
                'sometimes',
                'string',
                'in:sum,avg,max,min,count,distinct_count',
            ],
            'comparison' => [
                'sometimes',
                'array',
            ],
            'comparison.enabled' => [
                'boolean',
            ],
            'comparison.period' => [
                'sometimes',
                'string',
                'in:previous_period,previous_week,previous_month,previous_year',
            ],
            'real_time' => [
                'sometimes',
                'boolean',
            ],
            'refresh_interval' => [
                'sometimes',
                'integer',
                'min:1',
                'max:300',
            ],
            'include_predictions' => [
                'sometimes',
                'boolean',
            ],
            'prediction_model' => [
                'sometimes',
                'string',
                'in:linear,exponential,seasonal',
            ],
            'include_alerts' => [
                'sometimes',
                'boolean',
            ],
            'alert_thresholds' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'alert_thresholds.*.metric' => [
                'required',
                'string',
            ],
            'alert_thresholds.*.condition' => [
                'required',
                'string',
                'in:greater_than,less_than,equals,not_equals',
            ],
            'alert_thresholds.*.value' => [
                'required',
                'numeric',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,csv,chart_data',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'time_window' => '1h',
            'granularity' => 'minute',
            'aggregation' => 'avg',
            'real_time' => true,
            'refresh_interval' => 30,
            'include_predictions' => false,
            'prediction_model' => 'linear',
            'include_alerts' => false,
            'format' => 'json',
            'timezone' => config('app.timezone'),
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'metrics.required' => __('validation.custom.analytics.metrics_required'),
            'metrics.max' => __('validation.custom.analytics.metrics_limit'),
            'metrics.*.in' => __('validation.custom.analytics.metric_invalid'),
            'time_window.in' => __('validation.custom.analytics.time_window_invalid'),
            'granularity.in' => __('validation.custom.analytics.granularity_invalid'),
            'filters.date_range.end.after' => __('validation.custom.analytics.date_range_invalid'),
            'filters.locations.max' => __('validation.custom.analytics.locations_limit'),
            'filters.user_types.*.in' => __('validation.custom.analytics.user_type_invalid'),
            'filters.device_types.*.in' => __('validation.custom.analytics.device_type_invalid'),
            'aggregation.in' => __('validation.custom.analytics.aggregation_invalid'),
            'refresh_interval.max' => __('validation.custom.analytics.refresh_interval_limit'),
            'prediction_model.in' => __('validation.custom.analytics.prediction_model_invalid'),
            'alert_thresholds.max' => __('validation.custom.analytics.alert_thresholds_limit'),
            'format.in' => __('validation.custom.analytics.format_invalid'),
            'timezone.timezone' => __('validation.custom.analytics.timezone_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('metrics') && is_string($this->input('metrics'))) {
            $this->merge(['metrics' => explode(',', $this->input('metrics'))]);
        }

        foreach (['time_window', 'granularity', 'aggregation', 'format'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strtolower(trim($this->input($field)))]);
            }
        }

        if ($this->has('filters.locations') && is_string($this->input('filters.locations'))) {
            $filters = $this->input('filters', []);
            $filters['locations'] = explode(',', $this->input('filters.locations'));
            $this->merge(['filters' => $filters]);
        }

        if ($this->has('filters.user_types') && is_string($this->input('filters.user_types'))) {
            $filters = $this->input('filters', []);
            $filters['user_types'] = explode(',', $this->input('filters.user_types'));
            $this->merge(['filters' => $filters]);
        }

        if ($this->has('filters.device_types') && is_string($this->input('filters.device_types'))) {
            $filters = $this->input('filters', []);
            $filters['device_types'] = explode(',', $this->input('filters.device_types'));
            $this->merge(['filters' => $filters]);
        }
    }
}
