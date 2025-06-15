<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCompanySizeRequest extends FormRequest
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
        $companySizeId = $this->route('companySize')->id ?? $this->route('company_size');

        return [
            'size' => [
                'required',
                'string',
                'max:255',
                Rule::unique('company_sizes', 'size')->ignore($companySizeId),
                'regex:/^[a-zA-Z0-9\s\-+()]{2,}$/',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'size.required' => __('validation.required', ['attribute' => __('Company Size')]),
            'size.string' => __('validation.string', ['attribute' => __('Company Size')]),
            'size.max' => __('validation.max.string', ['attribute' => __('Company Size'), 'max' => 255]),
            'size.unique' => __('validation.unique', ['attribute' => __('Company Size')]),
            'size.regex' => __('validation.regex', ['attribute' => __('Company Size')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('Active Status')]),
            'is_default.boolean' => __('validation.boolean', ['attribute' => __('Default Status')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'size' => __('Company Size'),
            'is_active' => __('Active Status'),
            'is_default' => __('Default Status'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param mixed $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional business logic validation
            if ($this->is_default && false === $this->is_active) {
                $validator->errors()->add('is_active', __('Default company sizes must be active'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        if ($this->has('is_default')) {
            $this->merge([
                'is_default' => filter_var($this->is_default, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Trim and clean the size field
        if ($this->has('size')) {
            $this->merge([
                'size' => trim($this->size),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => __('validation.failed'),
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
