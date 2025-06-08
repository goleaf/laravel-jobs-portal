<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaritalStatusRequest extends FormRequest
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
            'marital_status' => [
                'required',
                'string',
                'max:150',
                'unique:marital_statuses,marital_status'
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
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'marital_status.required' => __('validation.required', ['attribute' => __('validation.attributes.marital_status')]),
            'marital_status.string' => __('validation.string', ['attribute' => __('validation.attributes.marital_status')]),
            'marital_status.max' => __('validation.max.string', ['attribute' => __('validation.attributes.marital_status'), 'max' => 150]),
            'marital_status.unique' => __('validation.unique', ['attribute' => __('validation.attributes.marital_status')]),
            
            'description.string' => __('validation.string', ['attribute' => __('validation.attributes.description')]),
            'description.max' => __('validation.max.string', ['attribute' => __('validation.attributes.description'), 'max' => 500]),
            
            'is_default.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_default')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_active')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'marital_status' => __('validation.attributes.marital_status'),
            'description' => __('validation.attributes.description'),
            'is_default' => __('validation.attributes.is_default'),
            'is_active' => __('validation.attributes.is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure only one default marital status exists
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
