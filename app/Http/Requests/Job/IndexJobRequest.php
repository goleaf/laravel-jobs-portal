<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IndexJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated employers can view job index
        return Auth::check() && (Auth::user()->hasRole('Employer') || Auth::user()->hasRole('Admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|string|in:open,closed,drafted,paused',
            'search' => 'sometimes|string|max:255',
            'sort_by' => 'sometimes|string|in:title,created_at,expires_at,status',
            'sort_order' => 'sometimes|string|in:asc,desc',
            'per_page' => 'sometimes|integer|min:5|max:100',
            'page' => 'sometimes|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => __('jobs.validation.status_invalid'),
            'search.max' => __('jobs.validation.search_too_long'),
            'sort_by.in' => __('jobs.validation.sort_by_invalid'),
            'sort_order.in' => __('jobs.validation.sort_order_invalid'),
            'per_page.min' => __('jobs.validation.per_page_min'),
            'per_page.max' => __('jobs.validation.per_page_max'),
            'page.min' => __('jobs.validation.page_min'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => __('jobs.attributes.status'),
            'search' => __('jobs.attributes.search'),
            'sort_by' => __('jobs.attributes.sort_by'),
            'sort_order' => __('jobs.attributes.sort_order'),
            'per_page' => __('jobs.attributes.per_page'),
            'page' => __('jobs.attributes.page'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'per_page' => $this->per_page ?? 15,
            'sort_by' => $this->sort_by ?? 'created_at',
            'sort_order' => $this->sort_order ?? 'desc',
            'page' => $this->page ?? 1,
        ]);
    }

    /**
     * Get the validated data with additional computed fields.
     */
    public function getValidatedWithDefaults(): array
    {
        return array_merge($this->validated(), [
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company?->id,
        ]);
    }
} 