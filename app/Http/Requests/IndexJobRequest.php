<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('viewAny', \App\Models\Job::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|integer|exists:job_categories,id',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive',
            'sort_by' => 'nullable|string|in:recent,popular,featured',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => __('validation.max.string', ['attribute' => 'search', 'max' => 255]),
            'category.exists' => __('validation.exists', ['attribute' => 'category']),
            'location.max' => __('validation.max.string', ['attribute' => 'location', 'max' => 255]),
            'status.in' => __('validation.in', ['attribute' => 'status']),
            'sort_by.in' => __('validation.in', ['attribute' => 'sort by']),
            'per_page.min' => __('validation.min.numeric', ['attribute' => 'per page', 'min' => 1]),
            'per_page.max' => __('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]),
        ];
    }
}
