<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
        $companySizeId = $this->route('companySize')->id ?? $this->route('companySize');
        
        return [
            'size' => [
                'required',
                'string',
                'max:255',
                Rule::unique('company_sizes', 'size')->ignore($companySizeId),
                'regex:/^[a-zA-Z0-9\s\-+()]{2,}$/'
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'size.required' => __('Company size is required'),
            'size.string' => __('Company size must be a valid text'),
            'size.max' => __('Company size must not exceed 255 characters'),
            'size.unique' => __('This company size already exists'),
            'size.regex' => __('Company size contains invalid characters'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'size' => __('Company Size'),
        ];
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
                    'message' => __('Validation failed'),
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
