<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for StoreJobShiftRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class StoreJobShiftRequest extends FormRequest
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
            'shift' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'size' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'shift.required' => __('validation.jobshift_shift_required'),
            'shift.max' => __('validation.jobshift_shift_max'),
            'description.required' => __('validation.jobshift_description_required'),
            'description.max' => __('validation.jobshift_description_max'),
            'is_active.required' => __('validation.jobshift_is_active_required'),
            'is_active.max' => __('validation.jobshift_is_active_max'),
            'size.required' => __('validation.jobshift_size_required'),
            'size.max' => __('validation.jobshift_size_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'shift' => __('validation.attributes.jobshift_shift'),
            'description' => __('validation.attributes.jobshift_description'),
            'is_active' => __('validation.attributes.jobshift_is_active'),
            'size' => __('validation.attributes.jobshift_size'),
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
                $validator->errors()->add('name', __('validation.jobshift_business_conflict'));
            }
        });
    }

    private function hasBusinessLogicConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }
}