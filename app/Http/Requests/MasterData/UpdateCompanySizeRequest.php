<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for UpdateCompanySizeRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class UpdateCompanySizeRequest extends FormRequest
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
            'size' => ['required', 'string', 'max:255'],
            'from_range' => ['nullable', 'integer', 'min:1'],
            'to_range' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'size.required' => __('validation.companysize_size_required'),
            'size.max' => __('validation.companysize_size_max'),
            'from_range.required' => __('validation.companysize_from_range_required'),
            'from_range.max' => __('validation.companysize_from_range_max'),
            'to_range.required' => __('validation.companysize_to_range_required'),
            'to_range.max' => __('validation.companysize_to_range_max'),
            'is_active.required' => __('validation.companysize_is_active_required'),
            'is_active.max' => __('validation.companysize_is_active_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'size' => __('validation.attributes.companysize_size'),
            'from_range' => __('validation.attributes.companysize_from_range'),
            'to_range' => __('validation.attributes.companysize_to_range'),
            'is_active' => __('validation.attributes.companysize_is_active'),
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
                $validator->errors()->add('name', __('validation.companysize_business_conflict'));
            }
        });
    }

    private function hasBusinessLogicConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }
}