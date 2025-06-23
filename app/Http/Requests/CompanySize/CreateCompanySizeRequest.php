<?php

namespace App\Http\Requests\CompanySize;

use App\Models\CompanySize;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanySizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CompanySize::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'size' => [
                'required',
                'string',
                'max:150',
                'unique:company_sizes,size',
                'regex:/^\d+(-\d+)?(\+)?( employees?)?$/i',
            ],
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'size.required' => __('validation.company_size.size.required'),
            'size.string' => __('validation.company_size.size.string'),
            'size.max' => __('validation.company_size.size.max'),
            'size.unique' => __('validation.company_size.size.unique'),
            'size.regex' => __('validation.company_size.size.regex'),
            'is_default.boolean' => __('validation.company_size.is_default.boolean'),
            'is_active.boolean' => __('validation.company_size.is_active.boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'size' => __('attributes.company_size.size'),
            'is_default' => __('attributes.company_size.is_default'),
            'is_active' => __('attributes.company_size.is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default', false),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log creation attempt for security
        \Log::info('Company size creation attempted', [
            'user_id' => $this->user()->id,
            'size' => $this->input('size'),
            'ip' => $this->ip(),
        ]);
    }
}
