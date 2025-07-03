<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IndexTaxonomyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system, but admin access required
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
            // Pagination parameters
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:9999',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:100',
                Rule::in([5, 10, 15, 20, 25, 50, 100]),
            ],

            // Search parameters
            'search' => [
                'sometimes',
                'string',
                'max:255',
                'min:2',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_search_content'));
                    }
                },
            ],

            'search_type' => [
                'sometimes',
                'string',
                Rule::in(['name', 'slug', 'description', 'type', 'all']),
            ],

            'search_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'search_fields.*' => [
                'string',
                Rule::in(['name', 'slug', 'description', 'type', 'meta_title', 'meta_description']),
            ],

            // Filtering parameters
            'type' => [
                'sometimes',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $validTypes = ['job_category', 'skill', 'industry', 'location', 'education', 'experience_level', 'employment_type', 'company_size', 'custom'];
                    if (! in_array($value, $validTypes)) {
                        $fail(__('validation.invalid_taxonomy_type'));
                    }
                },
            ],

            'types' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'types.*' => [
                'string',
                'max:50',
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'draft', 'archived', 'all']),
            ],

            'visibility' => [
                'sometimes',
                'string',
                Rule::in(['public', 'private', 'restricted', 'all']),
            ],

            'hierarchical' => [
                'sometimes',
                'boolean',
            ],

            'is_system' => [
                'sometimes',
                'boolean',
            ],

            'has_terms' => [
                'sometimes',
                'boolean',
            ],

            'has_description' => [
                'sometimes',
                'boolean',
            ],

            'has_meta' => [
                'sometimes',
                'boolean',
            ],

            // Terms count filters
            'terms_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
            ],

            'terms_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
                'gte:terms_count_min',
            ],

            'terms_count_zero' => [
                'sometimes',
                'boolean',
            ],

            // Usage statistics filters
            'usage_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000000',
            ],

            'usage_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000000',
                'gte:usage_count_min',
            ],

            'last_used_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:last_used_to',
            ],

            'last_used_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:last_used_from',
            ],

            'never_used' => [
                'sometimes',
                'boolean',
            ],

            'recently_used' => [
                'sometimes',
                'boolean',
            ],

            'recently_used_days' => [
                'sometimes',
                'integer',
                'min:1',
                'max:365',
            ],

            // Date filters
            'created_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:created_to',
            ],

            'created_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:created_from',
            ],

            'updated_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:updated_to',
            ],

            'updated_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:updated_from',
            ],

            'stale_days' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1095', // 3 years
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'name',
                    'slug',
                    'type',
                    'status',
                    'visibility',
                    'created_at',
                    'updated_at',
                    'terms_count',
                    'usage_count',
                    'last_used_at',
                    'sort_order',
                    'alphabetical',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            'sort_secondary' => [
                'sometimes',
                'string',
                Rule::in(['name', 'created_at', 'terms_count', 'usage_count']),
            ],

            'sort_secondary_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Advanced display options
            'with_terms' => [
                'sometimes',
                'boolean',
            ],

            'with_usage_stats' => [
                'sometimes',
                'boolean',
            ],

            'with_meta' => [
                'sometimes',
                'boolean',
            ],

            'with_counts' => [
                'sometimes',
                'boolean',
            ],

            'terms_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            // Performance optimization
            'minimal_data' => [
                'sometimes',
                'boolean',
            ],

            'exclude_system' => [
                'sometimes',
                'boolean',
            ],

            'active_only' => [
                'sometimes',
                'boolean',
            ],

            'public_only' => [
                'sometimes',
                'boolean',
            ],

            // Export parameters
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['csv', 'excel', 'json', 'xml']),
            ],

            'export_fields' => [
                'sometimes',
                'array',
                'required_with:export_format',
                'max:20',
            ],

            'export_fields.*' => [
                'string',
                Rule::in([
                    'name',
                    'slug',
                    'type',
                    'description',
                    'status',
                    'visibility',
                    'terms_count',
                    'usage_count',
                    'created_at',
                    'updated_at',
                    'last_used_at',
                    'meta_title',
                    'meta_description',
                    'sort_order',
                ]),
            ],

            'export_include_terms' => [
                'sometimes',
                'boolean',
            ],

            'export_file_name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\s]+$/',
            ],

            // Admin bulk operations
            'bulk_action' => [
                'sometimes',
                'string',
                Rule::in(['activate', 'deactivate', 'delete', 'export', 'archive', 'make_public', 'make_private', 'update_type']),
            ],

            'selected_taxonomies' => [
                'sometimes',
                'array',
                'required_with:bulk_action',
                'max:100',
            ],

            'selected_taxonomies.*' => [
                'integer',
                'exists:taxonomies,id',
            ],

            'bulk_value' => [
                'sometimes',
                'string',
                'max:100',
                'required_if:bulk_action,update_type',
            ],

            // Analytics and reporting
            'include_analytics' => [
                'sometimes',
                'boolean',
            ],

            'analytics_period' => [
                'sometimes',
                'string',
                'required_if:include_analytics,true',
                Rule::in(['week', 'month', 'quarter', 'year', 'all']),
            ],

            'trending_period' => [
                'sometimes',
                'integer',
                'min:1',
                'max:365',
            ],

            // API response format
            'response_format' => [
                'sometimes',
                'string',
                Rule::in(['full', 'minimal', 'tree', 'flat']),
            ],

            'include_relationships' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'include_relationships.*' => [
                'string',
                Rule::in(['terms', 'parent', 'children', 'usage_stats', 'meta']),
            ],

            // Hierarchy filters for hierarchical taxonomies
            'parent_id' => [
                'sometimes',
                'integer',
                'exists:taxonomies,id',
            ],

            'level' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10',
            ],

            'has_children' => [
                'sometimes',
                'boolean',
            ],

            'has_parent' => [
                'sometimes',
                'boolean',
            ],

            'root_only' => [
                'sometimes',
                'boolean',
            ],

            'leaf_only' => [
                'sometimes',
                'boolean',
            ],

            // Cache control
            'force_refresh' => [
                'sometimes',
                'boolean',
            ],

            'cache_duration' => [
                'sometimes',
                'integer',
                'min:0',
                'max:3600', // 1 hour max
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.min' => __('validation.search_too_short'),
            'search.max' => __('validation.search_too_long'),
            'per_page.in' => __('validation.invalid_page_size'),
            'page.max' => __('validation.page_number_too_high'),
            'terms_count_max.gte' => __('validation.terms_count_range_invalid'),
            'usage_count_max.gte' => __('validation.usage_count_range_invalid'),
            'created_to.after_or_equal' => __('validation.date_range_invalid'),
            'updated_to.after_or_equal' => __('validation.date_range_invalid'),
            'last_used_to.after_or_equal' => __('validation.date_range_invalid'),
            'search_fields.max' => __('validation.too_many_search_fields'),
            'types.max' => __('validation.too_many_types_selected'),
            'export_fields.required_with' => __('validation.export_fields_required'),
            'export_fields.max' => __('validation.too_many_export_fields'),
            'selected_taxonomies.max' => __('validation.bulk_action_limit_exceeded'),
            'bulk_value.required_if' => __('validation.bulk_value_required'),
            'analytics_period.required_if' => __('validation.analytics_period_required'),
            'parent_id.exists' => __('validation.invalid_parent_taxonomy'),
            'level.max' => __('validation.taxonomy_level_too_deep'),
            'cache_duration.max' => __('validation.cache_duration_too_long'),
            'export_file_name.regex' => __('validation.invalid_file_name_format'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'per_page' => __('validation.attributes.items_per_page'),
            'search_type' => __('validation.attributes.search_type'),
            'search_fields' => __('validation.attributes.search_fields'),
            'terms_count_min' => __('validation.attributes.minimum_terms_count'),
            'terms_count_max' => __('validation.attributes.maximum_terms_count'),
            'usage_count_min' => __('validation.attributes.minimum_usage_count'),
            'usage_count_max' => __('validation.attributes.maximum_usage_count'),
            'last_used_from' => __('validation.attributes.last_used_from_date'),
            'last_used_to' => __('validation.attributes.last_used_to_date'),
            'recently_used_days' => __('validation.attributes.recently_used_days'),
            'created_from' => __('validation.attributes.created_from_date'),
            'created_to' => __('validation.attributes.created_to_date'),
            'updated_from' => __('validation.attributes.updated_from_date'),
            'updated_to' => __('validation.attributes.updated_to_date'),
            'stale_days' => __('validation.attributes.stale_days'),
            'sort_by' => __('validation.attributes.sort_field'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'terms_limit' => __('validation.attributes.terms_limit'),
            'export_format' => __('validation.attributes.export_format'),
            'export_fields' => __('validation.attributes.export_fields'),
            'export_file_name' => __('validation.attributes.export_file_name'),
            'bulk_action' => __('validation.attributes.bulk_action'),
            'selected_taxonomies' => __('validation.attributes.selected_taxonomies'),
            'bulk_value' => __('validation.attributes.bulk_value'),
            'analytics_period' => __('validation.attributes.analytics_period'),
            'trending_period' => __('validation.attributes.trending_period'),
            'response_format' => __('validation.attributes.response_format'),
            'include_relationships' => __('validation.attributes.include_relationships'),
            'parent_id' => __('validation.attributes.parent_taxonomy'),
            'cache_duration' => __('validation.attributes.cache_duration'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 20]);
        }

        if (! $this->has('sort_by')) {
            $this->merge(['sort_by' => 'name']);
        }

        if (! $this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'asc']);
        }

        if (! $this->has('search_type')) {
            $this->merge(['search_type' => 'all']);
        }

        if (! $this->has('response_format')) {
            $this->merge(['response_format' => 'full']);
        }

        if (! $this->has('recently_used_days')) {
            $this->merge(['recently_used_days' => 30]);
        }

        if (! $this->has('terms_limit')) {
            $this->merge(['terms_limit' => 10]);
        }

        // Clean search input
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->input('search')),
            ]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'hierarchical', 'is_system', 'has_terms', 'has_description', 'has_meta',
            'terms_count_zero', 'never_used', 'recently_used', 'with_terms',
            'with_usage_stats', 'with_meta', 'with_counts', 'minimal_data',
            'exclude_system', 'active_only', 'public_only', 'export_include_terms',
            'include_analytics', 'has_children', 'has_parent', 'root_only',
            'leaf_only', 'force_refresh',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = ['search_fields', 'types', 'export_fields', 'selected_taxonomies', 'include_relationships'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && ! is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field))),
                ]);
            }
        }

        // Auto-set boolean flags based on other parameters
        if ($this->has('terms_count_min') && $this->input('terms_count_min') == 0) {
            $this->merge(['terms_count_zero' => true]);
        }

        if ($this->has('parent_id')) {
            $this->merge(['has_parent' => true]);
        }

        // Log admin taxonomy search for audit purposes
        if ($this->has('search') && $this->input('search')) {
            Log::info('Admin taxonomy search performed', [
                'search_term' => $this->input('search'),
                'search_type' => $this->input('search_type'),
                'filters_applied' => count(array_filter($this->only(['type', 'status', 'visibility']))),
                'ip_address' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful admin taxonomy index request for audit
        Log::info('Admin taxonomy index request validated', [
            'filters_applied' => count(array_filter($this->only([
                'search', 'type', 'status', 'visibility', 'hierarchical',
                'has_terms', 'terms_count_min', 'terms_count_max',
            ]))),
            'search_performed' => $this->has('search'),
            'export_requested' => $this->has('export_format'),
            'bulk_action_requested' => $this->has('bulk_action'),
            'analytics_requested' => $this->has('include_analytics'),
            'sort_by' => $this->input('sort_by'),
            'response_format' => $this->input('response_format'),
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Check if content contains inappropriate material.
     */
    private function containsInappropriateContent(string $content): bool
    {
        $inappropriateWords = [
            'spam', 'scam', 'fraud', 'fake', 'illegal', 'hack', 'virus',
            'malware', 'phishing', 'adult', 'xxx', 'porn', 'sex', 'drug',
            'weapon', 'violence', 'hate', 'racist', 'terrorist',
        ];

        $lowercaseContent = strtolower($content);

        foreach ($inappropriateWords as $word) {
            if (strpos($lowercaseContent, $word) !== false) {
                return true;
            }
        }

        // Check for SQL injection patterns
        $sqlPatterns = [
            '/(\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\bunion\b)/i',
            '/(\bor\s+1\s*=\s*1\b|\band\s+1\s*=\s*1\b)/i',
            '/(--|\/\*|\*\/|;)/i',
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}
