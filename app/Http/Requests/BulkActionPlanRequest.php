<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class BulkActionPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', \App\Models\Plan::class) || Gate::allows('delete', \App\Models\Plan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'action' => 'required|string|in:activate,deactivate,delete',
            'plan_ids' => 'required|array|min:1',
            'plan_ids.*' => 'integer|exists:plans,id',
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
            'action.required' => __('validation.required', ['attribute' => 'action']),
            'action.in' => __('validation.in', ['attribute' => 'action']),
            'plan_ids.required' => __('validation.required', ['attribute' => 'plan IDs']),
            'plan_ids.array' => __('validation.array', ['attribute' => 'plan IDs']),
            'plan_ids.min' => __('validation.min.array', ['attribute' => 'plan IDs', 'min' => 1]),
            'plan_ids.*.integer' => __('validation.integer', ['attribute' => 'plan ID']),
            'plan_ids.*.exists' => __('validation.exists', ['attribute' => 'plan ID']),
        ];
    }
} 