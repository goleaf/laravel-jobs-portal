<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for UpdateStateRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class UpdateStateRequest extends FormRequest
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
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'code' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.state_name_required'),
            'name.max' => __('validation.state_name_max'),
            'country_id.required' => __('validation.state_country_id_required'),
            'country_id.max' => __('validation.state_country_id_max'),
            'code.required' => __('validation.state_code_required'),
            'code.max' => __('validation.state_code_max'),
            'is_active.required' => __('validation.state_is_active_required'),
            'is_active.max' => __('validation.state_is_active_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.state_name'),
            'country_id' => __('validation.attributes.state_country_id'),
            'code' => __('validation.attributes.state_code'),
            'is_active' => __('validation.attributes.state_is_active'),
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