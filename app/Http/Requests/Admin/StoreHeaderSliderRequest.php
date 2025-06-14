<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Universal Form Request for storing HeaderSlider
 * Implements Laravel 12 best practices with Universal MCP patterns
 */
class StoreHeaderSliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Authorization check
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', headerslider::class) ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Comprehensive validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:headersliders,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.unique' => __('validation.name_unique'),
            'name.max' => __('validation.name_max'),
            'status.required' => __('validation.status_required'),
            'status.boolean' => __('validation.status_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'description' => __('validation.attributes.description'),
            'status' => __('validation.attributes.status'),
            'sort_order' => __('validation.attributes.sort_order'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'description' => trim($this->description ?? '') ?: null,
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'sort_order' => $this->sort_order ? (int) $this->sort_order : 0,
        ]);
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Additional business logic validation
            if ($this->hasConflictingData()) {
                $validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Universal Pattern: Custom business logic check
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->info('Store validation failed for StoreHeaderSliderRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->toArray(),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }
}
