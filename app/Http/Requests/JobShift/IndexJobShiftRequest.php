<?php

namespace App\Http\Requests\JobShift;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class IndexJobShiftRequest
 * Context7 Enhanced Index Request for JobShift
 */
class IndexJobShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && 
               ($this->user()->hasRole(['admin', 'super_admin']) || 
                $this->user()->can('view job_shifts'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Pagination parameters
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            
            // Sorting parameters
            'sort_by' => ['sometimes', 'string', 'in:name,description,created_at,updated_at'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
            
            // Filter parameters
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive,all'],
            'is_active' => ['sometimes', 'boolean'],
            'date_from' => ['sometimes', 'date', 'before_or_equal:date_to'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            
            // Advanced filters
            'shift_type' => ['sometimes', 'string', 'max:50'],
            'time_range' => ['sometimes', 'array'],
            'time_range.*' => ['string', 'in:morning,afternoon,evening,night,flexible'],
            
            // Output format
            'format' => ['sometimes', 'string', 'in:json,excel,pdf'],
            'include' => ['sometimes', 'array'],
            'include.*' => ['string', 'in:jobs,statistics'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'page.integer' => __('validation.integer', ['attribute' => __('pagination.page')]),
            'page.min' => __('validation.min.numeric', ['attribute' => __('pagination.page'), 'min' => 1]),
            'per_page.integer' => __('validation.integer', ['attribute' => __('pagination.per_page')]),
            'per_page.min' => __('validation.min.numeric', ['attribute' => __('pagination.per_page'), 'min' => 1]),
            'per_page.max' => __('validation.max.numeric', ['attribute' => __('pagination.per_page'), 'max' => 100]),
            
            'sort_by.in' => __('validation.in', ['attribute' => __('sorting.sort_by')]),
            'sort_direction.in' => __('validation.in', ['attribute' => __('sorting.sort_direction')]),
            
            'search.string' => __('validation.string', ['attribute' => __('common.search')]),
            'search.max' => __('validation.max.string', ['attribute' => __('common.search'), 'max' => 255]),
            
            'status.in' => __('validation.in', ['attribute' => __('common.status')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('common.is_active')]),
            
            'date_from.date' => __('validation.date', ['attribute' => __('common.date_from')]),
            'date_to.date' => __('validation.date', ['attribute' => __('common.date_to')]),
            'date_from.before_or_equal' => __('validation.before_or_equal', ['attribute' => __('common.date_from'), 'date' => __('common.date_to')]),
            'date_to.after_or_equal' => __('validation.after_or_equal', ['attribute' => __('common.date_to'), 'date' => __('common.date_from')]),
            
            'shift_type.string' => __('validation.string', ['attribute' => __('job_shift.shift_type')]),
            'shift_type.max' => __('validation.max.string', ['attribute' => __('job_shift.shift_type'), 'max' => 50]),
            
            'time_range.array' => __('validation.array', ['attribute' => __('job_shift.time_range')]),
            'time_range.*.in' => __('validation.in', ['attribute' => __('job_shift.time_range')]),
            
            'format.in' => __('validation.in', ['attribute' => __('common.format')]),
            'include.array' => __('validation.array', ['attribute' => __('common.include')]),
            'include.*.in' => __('validation.in', ['attribute' => __('common.include')]),
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'page' => __('pagination.page'),
            'per_page' => __('pagination.per_page'),
            'sort_by' => __('sorting.sort_by'),
            'sort_direction' => __('sorting.sort_direction'),
            'search' => __('common.search'),
            'status' => __('common.status'),
            'is_active' => __('common.is_active'),
            'date_from' => __('common.date_from'),
            'date_to' => __('common.date_to'),
            'shift_type' => __('job_shift.shift_type'),
            'time_range' => __('job_shift.time_range'),
            'format' => __('common.format'),
            'include' => __('common.include'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 15,
            'sort_by' => $this->sort_by ?? 'name',
            'sort_direction' => $this->sort_direction ?? 'asc',
            'status' => $this->status ?? 'all',
            'format' => $this->format ?? 'json',
        ]);

        // Clean and prepare search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search)
            ]);
        }

        // Convert status to boolean for is_active filter
        if ($this->status === 'active') {
            $this->merge(['is_active' => true]);
        } elseif ($this->status === 'inactive') {
            $this->merge(['is_active' => false]);
        }
    }

    /**
     * Get the validated data with defaults.
     */
    public function getFilters(): array
    {
        $validated = $this->validated();
        
        return [
            'page' => $validated['page'] ?? 1,
            'per_page' => $validated['per_page'] ?? 15,
            'sort_by' => $validated['sort_by'] ?? 'name',
            'sort_direction' => $validated['sort_direction'] ?? 'asc',
            'search' => $validated['search'] ?? null,
            'is_active' => $validated['is_active'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'shift_type' => $validated['shift_type'] ?? null,
            'time_range' => $validated['time_range'] ?? [],
            'format' => $validated['format'] ?? 'json',
            'include' => $validated['include'] ?? [],
        ];
    }
} 