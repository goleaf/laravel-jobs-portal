<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Validation\Rule;

/**
 * 📋 **ADMIN TAXONOMY INDEX REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for taxonomy listing with advanced search, filtering, and sorting
 * **Domain**: Admin Management - Taxonomy operations
 * **Security Level**: HIGH - Admin panel access validation
 * **Context**: Authentication-free system with universal access
 *
 * **Key Features**:
 * - Advanced search parameter validation
 * - Multi-field filtering with business rules
 * - Sorting and pagination validation
 * - Performance optimization for large datasets
 * - Comprehensive error messaging in multiple languages
 *
 * **Business Rules**:
 * - Search terms must be meaningful (minimum 2 characters)
 * - Pagination limits prevent system overload
 * - Sort parameters restricted to valid fields
 * - Filter combinations validated for business logic
 *
 * @version 1.0.0 - Enterprise Edition
 *
 * @since 2024-12-28
 */
class TaxonomyIndexRequest extends MasterDataRequest
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

            // Filter parameters
            'type' => [
                'sometimes',
                'string',
                Rule::in(['job_type', 'skill', 'industry', 'location']),
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'all']),
            ],

            'visibility' => [
                'sometimes',
                'string',
                Rule::in(['public', 'private', 'all']),
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['name', 'created_at', 'terms_count']),
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
                Rule::in(['csv', 'excel', 'json', 'pdf']),
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

            // Bulk operations
            'bulk_action' => [
                'sometimes',
                'string',
                Rule::in(['activate', 'deactivate', 'delete', 'export']),
            ],

            'selected_ids' => [
                'sometimes',
                'array',
                'max:100',
            ],

            'selected_ids.*' => [
                'integer',
                'exists:taxonomies,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.min' => __('validation.admin.taxonomy.search_too_short'),
            'type.in' => __('validation.admin.taxonomy.invalid_type'),
            'status.in' => __('validation.admin.taxonomy.invalid_status'),
            'sort_by.in' => __('validation.admin.taxonomy.invalid_sort_field'),
            'per_page.max' => __('validation.admin.taxonomy.per_page_too_large'),
            'selected_ids.max' => __('validation.admin.taxonomy.too_many_selected'),
            'selected_ids.*.exists' => __('validation.admin.taxonomy.selected_taxonomy_not_found'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => __('validation.admin.taxonomy.attributes.search'),
            'type' => __('validation.admin.taxonomy.attributes.type'),
            'status' => __('validation.admin.taxonomy.attributes.status'),
            'visibility' => __('validation.admin.taxonomy.attributes.visibility'),
            'sort_by' => __('validation.admin.taxonomy.attributes.sort_by'),
            'sort_direction' => __('validation.admin.taxonomy.attributes.sort_direction'),
            'per_page' => __('validation.admin.taxonomy.attributes.per_page'),
            'terms_count_min' => __('validation.admin.taxonomy.attributes.terms_count_min'),
            'terms_count_max' => __('validation.admin.taxonomy.attributes.terms_count_max'),
            'selected_ids' => __('validation.admin.taxonomy.attributes.selected_ids'),
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
        ]);

        // Clean search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Normalize status and visibility
        if ($this->has('status') && $this->status === 'all') {
            $this->merge(['status' => null]);
        }

        if ($this->has('visibility') && $this->visibility === 'all') {
            $this->merge(['visibility' => null]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log search activity for analytics
        if ($this->has('search')) {
            \Log::info('Admin Taxonomy Search', [
                'search_term' => $this->search,
                'filters' => $this->only(['type', 'status', 'visibility']),
                'sort' => $this->only(['sort_by', 'sort_direction']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);
        }

        // Track bulk actions for audit
        if ($this->has('bulk_action')) {
            \Log::info('Admin Taxonomy Bulk Action', [
                'action' => $this->bulk_action,
                'selected_count' => count($this->selected_ids ?? []),
                'ip_address' => request()->ip(),
                'timestamp' => now(),
            ]);
        }
    }
}
