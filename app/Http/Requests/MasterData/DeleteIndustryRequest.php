<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Context7 Enhanced Form Request for Delete Action
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class DeleteIndustryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Context7 Pattern: Enhanced authorization with null checks
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['sometimes', 'integer', 'exists:industries,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.exists' => __('validation.resource_not_found'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id' => __('validation.attributes.id'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}