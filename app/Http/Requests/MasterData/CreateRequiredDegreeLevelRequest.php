<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for CreateRequiredDegreeLevelRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class CreateRequiredDegreeLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:required_degree_levels,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'level_order' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.requireddegree_name_required'),
            'name.max' => __('validation.requireddegree_name_max'),
            'name.unique' => __('validation.requireddegree_name_unique'),
            'description.max' => __('validation.requireddegree_description_max'),
            'level_order.integer' => __('validation.requireddegree_level_order_integer'),
            'level_order.min' => __('validation.requireddegree_level_order_min'),
            'level_order.max' => __('validation.requireddegree_level_order_max'),
            'is_active.boolean' => __('validation.requireddegree_is_active_boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.requireddegree_name'),
            'description' => __('validation.attributes.requireddegree_description'),
            'level_order' => __('validation.attributes.requireddegree_level_order'),
            'is_active' => __('validation.attributes.requireddegree_is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default active status
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }

        // Set default level order if not provided
        if (!$this->has('level_order')) {
            $this->merge([
                'level_order' => 1,
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => __('validation.failed'),
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
} 