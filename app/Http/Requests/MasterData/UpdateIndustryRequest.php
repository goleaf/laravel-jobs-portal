<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for UpdateIndustryRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class UpdateIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'size' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.industry_name_required'),
            'name.max' => __('validation.industry_name_max'),
            'description.required' => __('validation.industry_description_required'),
            'description.max' => __('validation.industry_description_max'),
            'is_active.required' => __('validation.industry_is_active_required'),
            'is_active.max' => __('validation.industry_is_active_max'),
            'size.required' => __('validation.industry_size_required'),
            'size.max' => __('validation.industry_size_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.industry_name'),
            'description' => __('validation.attributes.industry_description'),
            'is_active' => __('validation.attributes.industry_is_active'),
            'size' => __('validation.attributes.industry_size'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }
}