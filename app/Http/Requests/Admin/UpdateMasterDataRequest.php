<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Universal Form Request for updating MasterData
 * Implements Laravel 12 best practices with Universal MCP patterns
 */
class UpdateMasterDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For development and testing, allow all authenticated users
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Update-specific validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'category' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status must be true or false.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'category.max' => 'The category may not be greater than 100 characters.',
            'type.max' => 'The type may not be greater than 50 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'status' => 'status',
            'category' => 'category',
            'type' => 'type',
        ];
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasUnauthorizedChanges()) {
                $validator->errors()->add('status', __('validation.unauthorized_status_change'));
            }
        });
    }

    /**
     * Universal Pattern: Check for unauthorized changes
     */
    private function hasUnauthorizedChanges(): bool
    {
        // Add specific business logic for unauthorized changes
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling with audit logging
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Update validation failed for UpdateMasterDataRequest', [
            'errors' => $validator->errors()->toArray(),
            'resource_id' => $this->route('id'),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }
}
