<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Validation\Rule;

/**
 * 🗄️ **ADMIN MASTER DATA INDEX REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for master data listing with advanced search and filtering
 * **Domain**: Admin Management - Master data operations
 * **Security Level**: HIGH - System core data access validation
 * **Context**: Authentication-free system with universal access
 *
 * **Key Features**:
 * - Multi-entity search parameter validation
 * - Advanced filtering for master data categories
 * - Pagination and performance optimization
 * - Comprehensive error messaging
 * - Audit logging for data access
 *
 * **Business Rules**:
 * - Search terms must be meaningful (minimum 2 characters)
 * - Category filtering restricted to valid master data types
 * - Pagination limits prevent system overload
 * - Export functionality with format validation
 *
 * @version 1.0.0 - Enterprise Edition
 *
 * @since 2024-12-28
 */
class MasterDataIndexRequest extends MasterDataRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authentication-free system - universal access
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Search parameters
            'search' => [
                'sometimes',
                'string',
                'min:2',
                'max:255',
            ],

            // Category filtering
            'category' => [
                'sometimes',
                'string',
                Rule::in([
                    'countries',
                    'states',
                    'cities',
                    'skills',
                    'industries',
                    'company_sizes',
                    'functional_areas',
                    'career_levels',
                ]),
            ],

            // Status filtering
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'all']),
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['name', 'created_at', 'updated_at']),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Pagination parameters
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:100',
            ],

            // Export parameters
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['csv', 'excel', 'json']),
            ],

            // Advanced filtering
            'created_after' => [
                'sometimes',
                'date',
                'before_or_equal:created_before',
            ],

            'created_before' => [
                'sometimes',
                'date',
                'after_or_equal:created_after',
            ],

            'updated_after' => [
                'sometimes',
                'date',
                'before_or_equal:updated_before',
            ],

            'updated_before' => [
                'sometimes',
                'date',
                'after_or_equal:updated_after',
            ],

            // Geographic filtering
            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
            ],

            // Usage statistics filtering
            'usage_count_min' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'usage_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'gte:usage_count_min',
            ],

            // Bulk operations
            'bulk_action' => [
                'sometimes',
                'string',
                Rule::in(['activate', 'deactivate', 'export', 'sync']),
            ],

            'selected_ids' => [
                'sometimes',
                'array',
                'max:100',
            ],

            'selected_ids.*' => [
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.min' => __('validation.admin.master_data.search_too_short'),
            'category.in' => __('validation.admin.master_data.invalid_category'),
            'status.in' => __('validation.admin.master_data.invalid_status'),
            'per_page.max' => __('validation.admin.master_data.per_page_too_large'),
            'export_format.in' => __('validation.admin.master_data.invalid_export_format'),
            'created_before.after_or_equal' => __('validation.admin.master_data.invalid_date_range'),
            'updated_before.after_or_equal' => __('validation.admin.master_data.invalid_date_range'),
            'country_id.exists' => __('validation.admin.master_data.country_not_found'),
            'state_id.exists' => __('validation.admin.master_data.state_not_found'),
            'usage_count_max.gte' => __('validation.admin.master_data.usage_count_range_invalid'),
            'selected_ids.max' => __('validation.admin.master_data.too_many_selected'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => __('validation.admin.master_data.attributes.search'),
            'category' => __('validation.admin.master_data.attributes.category'),
            'status' => __('validation.admin.master_data.attributes.status'),
            'sort_by' => __('validation.admin.master_data.attributes.sort_by'),
            'sort_direction' => __('validation.admin.master_data.attributes.sort_direction'),
            'per_page' => __('validation.admin.master_data.attributes.per_page'),
            'export_format' => __('validation.admin.master_data.attributes.export_format'),
            'country_id' => __('validation.admin.master_data.attributes.country'),
            'state_id' => __('validation.admin.master_data.attributes.state'),
            'usage_count_min' => __('validation.admin.master_data.attributes.usage_count_min'),
            'usage_count_max' => __('validation.admin.master_data.attributes.usage_count_max'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values for optional parameters
        $this->merge([
            'sort_by' => $this->sort_by ?? 'name',
            'sort_direction' => $this->sort_direction ?? 'asc',
            'per_page' => $this->per_page ?? 20,
            'page' => $this->page ?? 1,
            'category' => $this->category ?? 'all',
            'status' => $this->status ?? 'all',
        ]);

        // Clean search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Normalize status and category
        if ($this->category === 'all') {
            $this->merge(['category' => null]);
        }

        if ($this->status === 'all') {
            $this->merge(['status' => null]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log search activity for analytics
        if ($this->has('search') || $this->has('category')) {
            \Log::info('Admin Master Data Search', [
                'search_term' => $this->search,
                'category' => $this->category,
                'filters' => $this->only(['status', 'country_id', 'state_id']),
                'sort' => $this->only(['sort_by', 'sort_direction']),
                'pagination' => $this->only(['page', 'per_page']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);
        }

        // Track export requests
        if ($this->has('export_format')) {
            \Log::info('Admin Master Data Export Request', [
                'format' => $this->export_format,
                'category' => $this->category,
                'filters_applied' => $this->only(['search', 'status', 'country_id', 'state_id']),
                'ip_address' => request()->ip(),
                'timestamp' => now(),
            ]);
        }
    }
}
