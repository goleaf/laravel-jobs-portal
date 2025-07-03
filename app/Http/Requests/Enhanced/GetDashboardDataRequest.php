<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class GetDashboardDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
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
            // Dashboard scope and filtering
            'scope' => [
                'sometimes',
                'string',
                Rule::in(['overview', 'detailed', 'minimal', 'analytics', 'performance', 'custom']),
            ],

            'user_type' => [
                'sometimes',
                'string',
                Rule::in(['candidate', 'employer', 'admin', 'recruiter', 'company']),
            ],

            'dashboard_view' => [
                'sometimes',
                'string',
                Rule::in(['default', 'compact', 'expanded', 'cards', 'list', 'grid', 'timeline']),
            ],

            // Time range and filtering
            'timeframe' => [
                'sometimes',
                'string',
                Rule::in(['1h', '6h', '12h', '24h', '7d', '30d', '90d', 'custom']),
            ],

            'start_date' => [
                'sometimes',
                'date',
                'before_or_equal:end_date',
                'before_or_equal:today',
                'after:'.now()->subYear()->format('Y-m-d'),
                'required_if:timeframe,custom',
            ],

            'end_date' => [
                'sometimes',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:today',
                'required_if:timeframe,custom',
            ],

            'timezone' => [
                'sometimes',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (! in_array($value, timezone_identifiers_list())) {
                        $fail(__('validation.invalid_timezone'));
                    }
                },
            ],

            // Data inclusion options
            'include_stats' => [
                'sometimes',
                'boolean',
            ],

            'include_charts' => [
                'sometimes',
                'boolean',
            ],

            'include_activities' => [
                'sometimes',
                'boolean',
            ],

            'include_notifications' => [
                'sometimes',
                'boolean',
            ],

            'include_metrics' => [
                'sometimes',
                'boolean',
            ],

            'include_health' => [
                'sometimes',
                'boolean',
            ],

            'include_performance' => [
                'sometimes',
                'boolean',
            ],

            'include_analytics' => [
                'sometimes',
                'boolean',
            ],

            // Advanced filtering options
            'metric_types' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'metric_types.*' => [
                'string',
                Rule::in([
                    'jobs_posted',
                    'applications_received',
                    'interviews_scheduled',
                    'candidates_hired',
                    'views',
                    'clicks',
                    'conversions',
                    'revenue',
                    'growth',
                    'performance',
                    'engagement',
                    'response_time',
                    'success_rate',
                    'completion_rate',
                    'bounce_rate',
                    'user_activity',
                    'system_load',
                    'error_rate',
                    'uptime',
                    'cache_hit_ratio',
                ]),
            ],

            'activity_types' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'activity_types.*' => [
                'string',
                Rule::in([
                    'job_posted',
                    'application_submitted',
                    'interview_scheduled',
                    'status_changed',
                    'profile_updated',
                    'message_sent',
                    'document_uploaded',
                    'search_performed',
                    'login',
                    'logout',
                    'view',
                    'click',
                    'download',
                    'share',
                    'export',
                ]),
            ],

            'status_filters' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'status_filters.*' => [
                'string',
                Rule::in(['active', 'pending', 'completed', 'expired', 'draft', 'archived', 'paused']),
            ],

            'priority_levels' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'priority_levels.*' => [
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent', 'critical']),
            ],

            // Geographic and location filtering
            'countries' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'countries.*' => [
                'string',
                'size:2', // ISO 3166-1 alpha-2
                'regex:/^[A-Z]{2}$/',
            ],

            'cities' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'cities.*' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],

            'regions' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'regions.*' => [
                'string',
                'max:100',
            ],

            // Industry and category filtering
            'industries' => [
                'sometimes',
                'array',
                'max:25',
            ],

            'industries.*' => [
                'integer',
                'exists:categories,id',
            ],

            'job_categories' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'job_categories.*' => [
                'integer',
                'exists:job_categories,id',
            ],

            'company_sizes' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'company_sizes.*' => [
                'string',
                Rule::in(['startup', 'small', 'medium', 'large', 'enterprise', 'corporation']),
            ],

            // Performance and caching options
            'cache_ttl' => [
                'sometimes',
                'integer',
                'min:30',
                'max:3600', // 1 hour maximum
            ],

            'force_refresh' => [
                'sometimes',
                'boolean',
            ],

            'use_cache' => [
                'sometimes',
                'boolean',
            ],

            'cache_strategy' => [
                'sometimes',
                'string',
                Rule::in(['aggressive', 'normal', 'conservative', 'none']),
            ],

            'data_compression' => [
                'sometimes',
                'boolean',
            ],

            'optimize_images' => [
                'sometimes',
                'boolean',
            ],

            'lazy_loading' => [
                'sometimes',
                'boolean',
            ],

            // Real-time options
            'real_time' => [
                'sometimes',
                'boolean',
            ],

            'websocket_channel' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
                'required_if:real_time,true',
            ],

            'polling_interval' => [
                'sometimes',
                'integer',
                'min:5',
                'max:300', // 5 minutes maximum
            ],

            'auto_refresh' => [
                'sometimes',
                'boolean',
            ],

            'refresh_interval' => [
                'sometimes',
                'integer',
                'min:10',
                'max:3600',
                'required_if:auto_refresh,true',
            ],

            // Pagination and limits
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            'offset' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50000',
            ],

            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:500',
            ],

            // Aggregation and grouping
            'group_by' => [
                'sometimes',
                'string',
                Rule::in(['date', 'hour', 'day', 'week', 'month', 'quarter', 'year', 'category', 'status', 'type', 'location', 'industry']),
            ],

            'aggregate_functions' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'aggregate_functions.*' => [
                'string',
                Rule::in(['count', 'sum', 'avg', 'min', 'max', 'median', 'stddev', 'variance']),
            ],

            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['date', 'name', 'count', 'value', 'priority', 'status', 'performance', 'relevance']),
            ],

            'sort_order' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Data export and format options
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'csv', 'excel', 'pdf', 'xml', 'api']),
            ],

            'include_metadata' => [
                'sometimes',
                'boolean',
            ],

            'include_totals' => [
                'sometimes',
                'boolean',
            ],

            'include_percentages' => [
                'sometimes',
                'boolean',
            ],

            'include_trends' => [
                'sometimes',
                'boolean',
            ],

            'decimal_places' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10',
            ],

            // Localization and formatting
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                Rule::in(['en', 'lt', 'es', 'fr', 'de', 'it', 'pt', 'nl', 'pl', 'ru']),
            ],

            'currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],

            'number_format' => [
                'sometimes',
                'string',
                Rule::in(['decimal', 'percentage', 'currency', 'scientific', 'compact']),
            ],

            'date_format' => [
                'sometimes',
                'string',
                Rule::in(['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y-m-d H:i:s', 'd-m-Y', 'M d, Y', 'relative']),
            ],

            // Visualization options
            'chart_types' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'chart_types.*' => [
                'string',
                Rule::in([
                    'line', 'bar', 'pie', 'donut', 'area', 'scatter', 'bubble',
                    'heatmap', 'radar', 'gauge', 'funnel', 'waterfall', 'treemap',
                    'histogram', 'box_plot',
                ]),
            ],

            'chart_colors' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'chart_colors.*' => [
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'chart_theme' => [
                'sometimes',
                'string',
                Rule::in(['light', 'dark', 'auto', 'custom']),
            ],

            // Advanced analytics
            'enable_ai_insights' => [
                'sometimes',
                'boolean',
            ],

            'predictive_analytics' => [
                'sometimes',
                'boolean',
            ],

            'anomaly_detection' => [
                'sometimes',
                'boolean',
            ],

            'sentiment_analysis' => [
                'sometimes',
                'boolean',
            ],

            'trend_analysis' => [
                'sometimes',
                'boolean',
            ],

            'correlation_analysis' => [
                'sometimes',
                'boolean',
            ],

            // Security and privacy
            'anonymize_data' => [
                'sometimes',
                'boolean',
            ],

            'data_masking' => [
                'sometimes',
                'boolean',
            ],

            'privacy_level' => [
                'sometimes',
                'string',
                Rule::in(['public', 'internal', 'restricted', 'confidential']),
            ],

            'gdpr_compliant' => [
                'sometimes',
                'boolean',
            ],

            'audit_logging' => [
                'sometimes',
                'boolean',
            ],

            // API and integration options
            'api_version' => [
                'sometimes',
                'string',
                Rule::in(['v1', 'v2', 'v3', 'latest']),
            ],

            'response_format' => [
                'sometimes',
                'string',
                Rule::in(['standard', 'compact', 'verbose', 'minimal']),
            ],

            'include_links' => [
                'sometimes',
                'boolean',
            ],

            'include_relations' => [
                'sometimes',
                'boolean',
            ],

            'webhook_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            'callback_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            // Custom fields and extensions
            'custom_filters' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_filters.*.field' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],

            'custom_filters.*.operator' => [
                'string',
                Rule::in(['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN']),
            ],

            'custom_filters.*.value' => [
                'required',
                'max:500',
            ],

            'custom_metrics' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'custom_metrics.*.name' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\s]+$/',
            ],

            'custom_metrics.*.query' => [
                'string',
                'max:1000',
            ],

            'custom_metrics.*.type' => [
                'string',
                Rule::in(['count', 'sum', 'avg', 'percentage', 'ratio']),
            ],

            // Performance monitoring
            'track_performance' => [
                'sometimes',
                'boolean',
            ],

            'benchmark_against' => [
                'sometimes',
                'string',
                Rule::in(['previous_period', 'industry_average', 'best_practice', 'custom_target']),
            ],

            'performance_threshold' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000',
            ],

            'alert_conditions' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'alert_conditions.*.metric' => [
                'string',
                'max:100',
            ],

            'alert_conditions.*.threshold' => [
                'numeric',
                'min:0',
            ],

            'alert_conditions.*.operator' => [
                'string',
                Rule::in(['>', '<', '>=', '<=', '=', '!=']),
            ],

            'alert_conditions.*.action' => [
                'string',
                Rule::in(['email', 'webhook', 'slack', 'sms', 'push']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'scope.in' => __('validation.invalid_dashboard_scope'),
            'user_type.in' => __('validation.invalid_user_type'),
            'dashboard_view.in' => __('validation.invalid_dashboard_view'),
            'timeframe.in' => __('validation.invalid_timeframe'),
            'start_date.required_if' => __('validation.start_date_required_for_custom_timeframe'),
            'end_date.required_if' => __('validation.end_date_required_for_custom_timeframe'),
            'start_date.after' => __('validation.start_date_too_old'),
            'start_date.before_or_equal' => __('validation.start_date_future_not_allowed'),
            'end_date.after_or_equal' => __('validation.end_date_before_start'),
            'countries.*.size' => __('validation.country_code_invalid_format'),
            'countries.*.regex' => __('validation.country_code_must_be_uppercase'),
            'cities.*.regex' => __('validation.city_name_invalid_characters'),
            'chart_colors.*.regex' => __('validation.invalid_hex_color'),
            'websocket_channel.required_if' => __('validation.websocket_channel_required_for_realtime'),
            'refresh_interval.required_if' => __('validation.refresh_interval_required_for_auto_refresh'),
            'currency.regex' => __('validation.currency_code_invalid_format'),
            'cache_ttl.min' => __('validation.cache_ttl_too_short'),
            'cache_ttl.max' => __('validation.cache_ttl_too_long'),
            'polling_interval.min' => __('validation.polling_interval_too_short'),
            'polling_interval.max' => __('validation.polling_interval_too_long'),
            'limit.max' => __('validation.limit_exceeds_maximum'),
            'offset.max' => __('validation.offset_exceeds_maximum'),
            'per_page.min' => __('validation.per_page_too_small'),
            'per_page.max' => __('validation.per_page_too_large'),
            'decimal_places.max' => __('validation.too_many_decimal_places'),
            'custom_filters.max' => __('validation.too_many_custom_filters'),
            'custom_metrics.max' => __('validation.too_many_custom_metrics'),
            'alert_conditions.max' => __('validation.too_many_alert_conditions'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'scope' => __('validation.attributes.dashboard_scope'),
            'user_type' => __('validation.attributes.user_type'),
            'dashboard_view' => __('validation.attributes.dashboard_view'),
            'timeframe' => __('validation.attributes.timeframe'),
            'start_date' => __('validation.attributes.start_date'),
            'end_date' => __('validation.attributes.end_date'),
            'timezone' => __('validation.attributes.timezone'),
            'metric_types' => __('validation.attributes.metric_types'),
            'activity_types' => __('validation.attributes.activity_types'),
            'status_filters' => __('validation.attributes.status_filters'),
            'countries' => __('validation.attributes.countries'),
            'cities' => __('validation.attributes.cities'),
            'industries' => __('validation.attributes.industries'),
            'job_categories' => __('validation.attributes.job_categories'),
            'cache_ttl' => __('validation.attributes.cache_ttl'),
            'websocket_channel' => __('validation.attributes.websocket_channel'),
            'polling_interval' => __('validation.attributes.polling_interval'),
            'refresh_interval' => __('validation.attributes.refresh_interval'),
            'limit' => __('validation.attributes.limit'),
            'offset' => __('validation.attributes.offset'),
            'per_page' => __('validation.attributes.per_page'),
            'group_by' => __('validation.attributes.group_by'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_order' => __('validation.attributes.sort_order'),
            'format' => __('validation.attributes.format'),
            'locale' => __('validation.attributes.locale'),
            'currency' => __('validation.attributes.currency'),
            'chart_types' => __('validation.attributes.chart_types'),
            'chart_colors' => __('validation.attributes.chart_colors'),
            'privacy_level' => __('validation.attributes.privacy_level'),
            'api_version' => __('validation.attributes.api_version'),
            'response_format' => __('validation.attributes.response_format'),
            'custom_filters' => __('validation.attributes.custom_filters'),
            'custom_metrics' => __('validation.attributes.custom_metrics'),
            'alert_conditions' => __('validation.attributes.alert_conditions'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set intelligent defaults based on context
        if (! $this->has('scope')) {
            $this->merge(['scope' => 'overview']);
        }

        if (! $this->has('timeframe')) {
            $this->merge(['timeframe' => '24h']);
        }

        if (! $this->has('dashboard_view')) {
            $this->merge(['dashboard_view' => 'default']);
        }

        if (! $this->has('cache_strategy')) {
            $this->merge(['cache_strategy' => 'normal']);
        }

        if (! $this->has('locale')) {
            $this->merge(['locale' => app()->getLocale()]);
        }

        if (! $this->has('currency')) {
            $this->merge(['currency' => config('app.currency', 'USD')]);
        }

        if (! $this->has('timezone')) {
            $this->merge(['timezone' => config('app.timezone')]);
        }

        if (! $this->has('sort_order')) {
            $this->merge(['sort_order' => 'desc']);
        }

        if (! $this->has('format')) {
            $this->merge(['format' => 'json']);
        }

        if (! $this->has('api_version')) {
            $this->merge(['api_version' => 'latest']);
        }

        if (! $this->has('privacy_level')) {
            $this->merge(['privacy_level' => 'internal']);
        }

        if (! $this->has('chart_theme')) {
            $this->merge(['chart_theme' => 'auto']);
        }

        if (! $this->has('number_format')) {
            $this->merge(['number_format' => 'decimal']);
        }

        if (! $this->has('date_format')) {
            $this->merge(['date_format' => 'Y-m-d']);
        }

        if (! $this->has('decimal_places')) {
            $this->merge(['decimal_places' => 2]);
        }

        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 25]);
        }

        if (! $this->has('cache_ttl')) {
            $this->merge(['cache_ttl' => 300]); // 5 minutes default
        }

        if (! $this->has('polling_interval')) {
            $this->merge(['polling_interval' => 30]); // 30 seconds default
        }

        // Enable default inclusions for overview scope
        if ($this->input('scope') === 'overview') {
            $defaultInclusions = [
                'include_stats' => true,
                'include_activities' => true,
                'include_notifications' => true,
                'include_metrics' => true,
                'include_health' => false,
                'include_performance' => false,
                'include_analytics' => false,
            ];

            foreach ($defaultInclusions as $key => $defaultValue) {
                if (! $this->has($key)) {
                    $this->merge([$key => $defaultValue]);
                }
            }
        }

        // Performance optimizations for minimal scope
        if ($this->input('scope') === 'minimal') {
            $this->merge([
                'use_cache' => true,
                'data_compression' => true,
                'lazy_loading' => true,
                'optimize_images' => true,
            ]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'include_stats', 'include_charts', 'include_activities', 'include_notifications',
            'include_metrics', 'include_health', 'include_performance', 'include_analytics',
            'force_refresh', 'use_cache', 'data_compression', 'optimize_images', 'lazy_loading',
            'real_time', 'auto_refresh', 'include_metadata', 'include_totals', 'include_percentages',
            'include_trends', 'enable_ai_insights', 'predictive_analytics', 'anomaly_detection',
            'sentiment_analysis', 'trend_analysis', 'correlation_analysis', 'anonymize_data',
            'data_masking', 'gdpr_compliant', 'audit_logging', 'include_links', 'include_relations',
            'track_performance',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = [
            'metric_types', 'activity_types', 'status_filters', 'priority_levels',
            'countries', 'cities', 'regions', 'industries', 'job_categories', 'company_sizes',
            'aggregate_functions', 'chart_types', 'chart_colors',
        ];

        foreach ($arrayFields as $field) {
            if ($this->has($field) && ! is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field))),
                ]);
            }
        }

        // Set timeframe dates for predefined ranges
        if ($this->input('timeframe') !== 'custom' && $this->input('timeframe')) {
            $timeframe = $this->input('timeframe');
            $endDate = now();
            $startDate = $this->getStartDateForTimeframe($timeframe, $endDate);

            $this->merge([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }

        // Optimize performance settings based on data size
        if ($this->input('include_analytics') || $this->input('scope') === 'analytics') {
            if (! $this->has('cache_ttl')) {
                $this->merge(['cache_ttl' => 600]); // 10 minutes for analytics
            }
        }

        // Log dashboard data request for analytics
        Log::info('Dashboard data request prepared', [
            'scope' => $this->input('scope'),
            'timeframe' => $this->input('timeframe'),
            'user_type' => $this->input('user_type'),
            'includes_analytics' => $this->input('include_analytics', false),
            'real_time_enabled' => $this->input('real_time', false),
            'performance_tracking' => $this->input('track_performance', false),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful validation with comprehensive metrics
        Log::info('Dashboard data request validated successfully', [
            'request_parameters' => [
                'scope' => $this->input('scope'),
                'timeframe' => $this->input('timeframe'),
                'dashboard_view' => $this->input('dashboard_view'),
                'format' => $this->input('format'),
                'locale' => $this->input('locale'),
            ],
            'data_inclusions' => [
                'stats' => $this->input('include_stats', false),
                'charts' => $this->input('include_charts', false),
                'activities' => $this->input('include_activities', false),
                'notifications' => $this->input('include_notifications', false),
                'metrics' => $this->input('include_metrics', false),
                'health' => $this->input('include_health', false),
                'performance' => $this->input('include_performance', false),
                'analytics' => $this->input('include_analytics', false),
            ],
            'filtering_applied' => [
                'metric_types' => ! empty($this->input('metric_types')),
                'activity_types' => ! empty($this->input('activity_types')),
                'geographic' => ! empty($this->input('countries')) || ! empty($this->input('cities')),
                'industry' => ! empty($this->input('industries')),
                'custom_filters' => ! empty($this->input('custom_filters')),
            ],
            'performance_settings' => [
                'cache_enabled' => $this->input('use_cache', true),
                'cache_ttl' => $this->input('cache_ttl'),
                'force_refresh' => $this->input('force_refresh', false),
                'real_time' => $this->input('real_time', false),
                'compression' => $this->input('data_compression', false),
            ],
            'advanced_features' => [
                'ai_insights' => $this->input('enable_ai_insights', false),
                'predictive_analytics' => $this->input('predictive_analytics', false),
                'anomaly_detection' => $this->input('anomaly_detection', false),
                'sentiment_analysis' => $this->input('sentiment_analysis', false),
            ],
            'privacy_compliance' => [
                'anonymize_data' => $this->input('anonymize_data', false),
                'gdpr_compliant' => $this->input('gdpr_compliant', false),
                'privacy_level' => $this->input('privacy_level'),
                'audit_logging' => $this->input('audit_logging', false),
            ],
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);

        // Cache request signature for performance analytics
        if ($this->input('track_performance', false)) {
            $signature = $this->generateRequestSignature();
            Cache::put("dashboard_request_signature_{$signature}", [
                'parameters' => $this->validated(),
                'timestamp' => now(),
                'ip' => $this->ip(),
            ], 3600);
        }
    }

    /**
     * Get start date for predefined timeframe.
     */
    private function getStartDateForTimeframe(string $timeframe, $endDate)
    {
        return match ($timeframe) {
            '1h' => $endDate->copy()->subHour(),
            '6h' => $endDate->copy()->subHours(6),
            '12h' => $endDate->copy()->subHours(12),
            '24h' => $endDate->copy()->subDay(),
            '7d' => $endDate->copy()->subWeek(),
            '30d' => $endDate->copy()->subMonth(),
            '90d' => $endDate->copy()->subMonths(3),
            default => $endDate->copy()->subDay(),
        };
    }

    /**
     * Generate unique request signature for caching and analytics.
     */
    private function generateRequestSignature(): string
    {
        $keyParams = [
            'scope', 'timeframe', 'user_type', 'dashboard_view',
            'include_stats', 'include_charts', 'include_activities',
            'metric_types', 'countries', 'industries',
        ];

        $signature = '';
        foreach ($keyParams as $param) {
            $value = $this->input($param);
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $signature .= $param.':'.$value.'|';
        }

        return md5($signature);
    }
}
