<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for UpdateCityRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class UpdateCityRequest extends FormRequest
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
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.city_name_required'),
            'name.max' => __('validation.city_name_max'),
            'state_id.required' => __('validation.city_state_id_required'),
            'state_id.max' => __('validation.city_state_id_max'),
            'is_active.required' => __('validation.city_is_active_required'),
            'is_active.max' => __('validation.city_is_active_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.city_name'),
            'state_id' => __('validation.attributes.city_state_id'),
            'is_active' => __('validation.attributes.city_is_active'),
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