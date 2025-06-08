<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for Store State
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class StoreStateRequest extends FormRequest
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
            'country_id.required' => __('validation.state_country_required'),
            'country_id.exists' => __('validation.state_country_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.state_name'),
            'country_id' => __('validation.attributes.state_country'),
            'code' => __('validation.attributes.state_code'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'code' => trim(strtoupper($this->code ?? '')),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasNameConflictInCountry()) {
                $validator->errors()->add('name', __('validation.state_name_exists_in_country'));
            }
        });
    }

    private function hasNameConflictInCountry(): bool
    {
        return \DB::table('states')
            ->where('country_id', $this->country_id)
            ->where('name', $this->name)
            ->exists();
    }
} 