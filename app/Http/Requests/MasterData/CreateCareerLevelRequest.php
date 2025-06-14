<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class CreateCareerLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'level_name' => [
                'required',
                'string',
                'max:150',
                'unique:career_levels,level_name'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'is_default' => [
                'sometimes',
                'boolean'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ],
            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'level_name.required' => __('validation.required', ['attribute' => __('validation.attributes.level_name')]),
            'level_name.string' => __('validation.string', ['attribute' => __('validation.attributes.level_name')]),
            'level_name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.level_name'), 'max' => 150]),
            'level_name.unique' => __('validation.unique', ['attribute' => __('validation.attributes.level_name')]),
            
            'description.string' => __('validation.string', ['attribute' => __('validation.attributes.description')]),
            'description.max' => __('validation.max.string', ['attribute' => __('validation.attributes.description'), 'max' => 500]),
            
            'is_default.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_default')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_active')]),
            
            'sort_order.integer' => __('validation.integer', ['attribute' => __('validation.attributes.sort_order')]),
            'sort_order.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.sort_order'), 'min' => 0]),
            'sort_order.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.sort_order'), 'max' => 999]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'level_name' => __('validation.attributes.level_name'),
            'description' => __('validation.attributes.description'),
            'is_default' => __('validation.attributes.is_default'),
            'is_active' => __('validation.attributes.is_active'),
            'sort_order' => __('validation.attributes.sort_order'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure only one default career level exists
        if ($this->boolean('is_default')) {
            $this->merge([
                'is_default' => true,
            ]);
        }

        // Set default active status
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }

        // Set default sort order if not provided
        if (!$this->has('sort_order')) {
            $this->merge([
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
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
