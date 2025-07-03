<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DashboardChartDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system, but dashboard requires access
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Period selection
            'period' => [
                'sometimes',
                'string',
                Rule::in(['hour', 'day', 'week', 'month', 'quarter', 'year', 'custom']),
            ],

            // Date range parameters
            'start_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:end_date',
                function ($attribute, $value, $fail) {
                    if ($this->input('period') === 'custom' && ! $value) {
                        $fail(__('validation.start_date_required_for_custom_period'));
                    }
                },
            ],

            'end_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) {
                    if ($this->input('period') === 'custom' && ! $value) {
                        $fail(__('validation.end_date_required_for_custom_period'));
                    }
                },
            ],

            // Chart configuration
            'chart_type' => [
                'sometimes',
                'string',
                Rule::in(['line', 'bar', 'pie', 'doughnut', 'area', 'scatter', 'radar', 'polar']),
            ],

            'chart_style' => [
                'sometimes',
                'string',
                Rule::in(['classic', 'modern', 'minimal', 'dark', 'light']),
            ],

            // Data metrics selection
            'metrics' => [
                'sometimes',
                'array',
                'min:1',
                'max:10',
            ],

            'metrics.*' => [
                'string',
                Rule::in([
                    'revenue', 'applications', 'jobs', 'hires', 'interviews', 'views',
                    'conversion_rate', 'response_rate', 'time_to_hire', 'cost_per_hire',
                    'user_engagement', 'system_performance',
                ]),
            ],

            // Filtering parameters
            'industry_ids' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'industry_ids.*' => [
                'integer',
                'exists:industries,id',
            ],

            'location_ids' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'location_ids.*' => [
                'integer',
                'exists:cities,id',
            ],

            // Role-specific filters
            'company_id' => [
                'sometimes',
                'integer',
                'exists:companies,id',
            ],

            'candidate_id' => [
                'sometimes',
                'integer',
                'exists:candidates,id',
            ],

            // Performance and caching
            'use_cache' => [
                'sometimes',
                'boolean',
            ],

            'cache_duration' => [
                'sometimes',
                'integer',
                'min:0',
                'max:3600', // 1 hour max
            ],

            'force_refresh' => [
                'sometimes',
                'boolean',
            ],

            // Real-time features
            'real_time' => [
                'sometimes',
                'boolean',
            ],

            'update_interval' => [
                'sometimes',
                'integer',
                'min:5', // 5 seconds minimum
                'max:300', // 5 minutes maximum
                'required_if:real_time,true',
            ],

            // Export options
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['png', 'jpg', 'svg', 'pdf', 'json', 'csv']),
            ],

            // Analytics features
            'include_insights' => [
                'sometimes',
                'boolean',
            ],

            'ai_analysis' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.before_or_equal' => __('validation.start_date_cannot_be_future'),
            'end_date.after_or_equal' => __('validation.end_date_must_be_after_start'),
            'period.in' => __('validation.invalid_period_selection'),
            'chart_type.in' => __('validation.invalid_chart_type'),
            'metrics.min' => __('validation.at_least_one_metric_required'),
            'metrics.max' => __('validation.too_many_metrics_selected'),
            'update_interval.required_if' => __('validation.update_interval_required_for_realtime'),
            'cache_duration.max' => __('validation.cache_duration_too_long'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => __('validation.attributes.start_date'),
            'end_date' => __('validation.attributes.end_date'),
            'chart_type' => __('validation.attributes.chart_type'),
            'chart_style' => __('validation.attributes.chart_style'),
            'industry_ids' => __('validation.attributes.industries'),
            'location_ids' => __('validation.attributes.locations'),
            'update_interval' => __('validation.attributes.update_interval'),
            'cache_duration' => __('validation.attributes.cache_duration'),
            'export_format' => __('validation.attributes.export_format'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (! $this->has('period')) {
            $this->merge(['period' => 'week']);
        }

        if (! $this->has('chart_type')) {
            $this->merge(['chart_type' => 'line']);
        }

        if (! $this->has('chart_style')) {
            $this->merge(['chart_style' => 'modern']);
        }

        if (! $this->has('cache_duration')) {
            $this->merge(['cache_duration' => 600]); // 10 minutes
        }

        if (! $this->has('use_cache')) {
            $this->merge(['use_cache' => true]);
        }

        // Set default date range based on period
        if ($this->input('period') !== 'custom' && ! $this->has('start_date')) {
            $dates = $this->getDefaultDateRange($this->input('period'));
            $this->merge($dates);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'use_cache', 'force_refresh', 'real_time', 'include_insights', 'ai_analysis',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = ['metrics', 'industry_ids', 'location_ids'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && ! is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field))),
                ]);
            }
        }

        // Log dashboard chart request for analytics
        Log::info('Dashboard chart data request', [
            'period' => $this->input('period'),
            'chart_type' => $this->input('chart_type'),
            'metrics_count' => $this->has('metrics') ? count($this->input('metrics')) : 0,
            'real_time' => $this->input('real_time', false),
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful dashboard chart request
        Log::info('Dashboard chart data request validated', [
            'period' => $this->input('period'),
            'chart_type' => $this->input('chart_type'),
            'metrics_requested' => $this->input('metrics', []),
            'real_time' => $this->input('real_time', false),
            'use_cache' => $this->input('use_cache', true),
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Get default date range based on period.
     */
    private function getDefaultDateRange(string $period): array
    {
        $endDate = now();
        $startDate = match ($period) {
            'hour' => $endDate->copy()->subHour(),
            'day' => $endDate->copy()->subDay(),
            'week' => $endDate->copy()->subWeek(),
            'month' => $endDate->copy()->subMonth(),
            'quarter' => $endDate->copy()->subQuarter(),
            'year' => $endDate->copy()->subYear(),
            default => $endDate->copy()->subWeek(),
        };

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
}
