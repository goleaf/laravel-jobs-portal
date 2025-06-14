<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Enhanced Enhanced Form Request for UpdateSalaryCurrencyRequest
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Following proven MasterData pattern
 */
class UpdateSalaryCurrencyRequest extends FormRequest
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
            'currency_name' => ['required', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_name.required' => __('validation.salarycurrency_currency_name_required'),
            'currency_name.max' => __('validation.salarycurrency_currency_name_max'),
            'currency_code.required' => __('validation.salarycurrency_currency_code_required'),
            'currency_code.max' => __('validation.salarycurrency_currency_code_max'),
            'currency_icon.required' => __('validation.salarycurrency_currency_icon_required'),
            'currency_icon.max' => __('validation.salarycurrency_currency_icon_max'),
            'is_active.required' => __('validation.salarycurrency_is_active_required'),
            'is_active.max' => __('validation.salarycurrency_is_active_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'currency_name' => __('validation.attributes.salarycurrency_currency_name'),
            'currency_code' => __('validation.attributes.salarycurrency_currency_code'),
            'currency_icon' => __('validation.attributes.salarycurrency_currency_icon'),
            'is_active' => __('validation.attributes.salarycurrency_is_active'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [];
        
        if (isset($this->name)) {
            $data['name'] = trim($this->name);
        }
        
        if (isset($this->currency_name)) {
            $data['currency_name'] = trim($this->currency_name);
        }
        
        if (isset($this->level_name)) {
            $data['level_name'] = trim($this->level_name);
        }
        
        if (isset($this->shift)) {
            $data['shift'] = trim($this->shift);
        }
        
        if (isset($this->size)) {
            $data['size'] = trim($this->size);
        }
        
        $data['is_active'] = filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        
        $this->merge($data);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasBusinessLogicConflicts()) {
                $validator->errors()->add('name', __('validation.salarycurrency_business_conflict'));
            }
        });
    }

    private function hasBusinessLogicConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }
}