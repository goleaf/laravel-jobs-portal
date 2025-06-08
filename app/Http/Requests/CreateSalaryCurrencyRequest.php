<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSalaryCurrencyRequest extends FormRequest
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
            'currency_name' => [
                'required',
                'string',
                'max:150',
                'unique:salary_currencies,currency_name'
            ],
            'currency_code' => [
                'required',
                'string',
                'size:3',
                'uppercase',
                'unique:salary_currencies,currency_code'
            ],
            'currency_icon' => [
                'required',
                'string',
                'max:10',
                'unique:salary_currencies,currency_icon'
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
            'currency_name.required' => __('validation.required', ['attribute' => __('validation.attributes.currency_name')]),
            'currency_name.string' => __('validation.string', ['attribute' => __('validation.attributes.currency_name')]),
            'currency_name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.currency_name'), 'max' => 150]),
            'currency_name.unique' => __('validation.unique', ['attribute' => __('validation.attributes.currency_name')]),
            
            'currency_code.required' => __('validation.required', ['attribute' => __('validation.attributes.currency_code')]),
            'currency_code.string' => __('validation.string', ['attribute' => __('validation.attributes.currency_code')]),
            'currency_code.size' => __('validation.size.string', ['attribute' => __('validation.attributes.currency_code'), 'size' => 3]),
            'currency_code.uppercase' => __('validation.uppercase', ['attribute' => __('validation.attributes.currency_code')]),
            'currency_code.unique' => __('validation.unique', ['attribute' => __('validation.attributes.currency_code')]),
            
            'currency_icon.required' => __('validation.required', ['attribute' => __('validation.attributes.currency_icon')]),
            'currency_icon.string' => __('validation.string', ['attribute' => __('validation.attributes.currency_icon')]),
            'currency_icon.max' => __('validation.max.string', ['attribute' => __('validation.attributes.currency_icon'), 'max' => 10]),
            'currency_icon.unique' => __('validation.unique', ['attribute' => __('validation.attributes.currency_icon')]),
            
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
            'currency_name' => __('validation.attributes.currency_name'),
            'currency_code' => __('validation.attributes.currency_code'),
            'currency_icon' => __('validation.attributes.currency_icon'),
            'is_default' => __('validation.attributes.is_default'),
            'is_active' => __('validation.attributes.is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('currency_code')) {
            $this->merge([
                'currency_code' => strtoupper($this->currency_code),
            ]);
        }

        // Ensure only one default currency exists
        if ($this->boolean('is_default')) {
            $this->merge([
                'is_default' => true,
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
