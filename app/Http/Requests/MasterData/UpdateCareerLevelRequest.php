<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for UpdateCareerLevelRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class UpdateCareerLevelRequest extends FormRequest
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
            'level_name' => ['required', 'string', 'max:255'],
            'from_year' => ['nullable', 'integer', 'min:0', 'max:50'],
            'to_year' => ['nullable', 'integer', 'min:0', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'level_name.required' => __('validation.careerlevel_level_name_required'),
            'level_name.max' => __('validation.careerlevel_level_name_max'),
            'from_year.required' => __('validation.careerlevel_from_year_required'),
            'from_year.max' => __('validation.careerlevel_from_year_max'),
            'to_year.required' => __('validation.careerlevel_to_year_required'),
            'to_year.max' => __('validation.careerlevel_to_year_max'),
            'is_active.required' => __('validation.careerlevel_is_active_required'),
            'is_active.max' => __('validation.careerlevel_is_active_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'level_name' => __('validation.attributes.careerlevel_level_name'),
            'from_year' => __('validation.attributes.careerlevel_from_year'),
            'to_year' => __('validation.attributes.careerlevel_to_year'),
            'is_active' => __('validation.attributes.careerlevel_is_active'),
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
                $validator->errors()->add('name', __('validation.careerlevel_business_conflict'));
            }
        });
    }

    private function hasBusinessLogicConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }
}