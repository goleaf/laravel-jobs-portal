<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Form Request for storing MaritalStatus
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class StoreMaritalStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Context7 Pattern: Authorization check
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', maritalstatus::class) ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:maritalstatuss,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Context7 Pattern: Multilingual error messages
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
     * Context7 Pattern: User-friendly field names
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
     * Context7 Pattern: Data normalization
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
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Context7 Pattern: Additional business logic validation
            if ($this->hasConflictingData()) {
                $validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Context7 Pattern: Custom business logic check
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->info('Store validation failed for StoreMaritalStatusRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->toArray(),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }
}
