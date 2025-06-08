<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequiredDegreeLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $requiredDegreeLevel = $this->route('required_degree_level') ?? $this->route('requiredDegreeLevel');
        return $this->user()->can('update', $requiredDegreeLevel);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $requiredDegreeLevel = $this->route('required_degree_level') ?? $this->route('requiredDegreeLevel');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('required_degree_levels', 'name')->ignore($requiredDegreeLevel?->id)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('required_degree_levels.attributes.name')]),
            'name.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('required_degree_levels.attributes.name'), 'max' => 255]),
            'name.unique' => __('validation.unique', ['attribute' => __('required_degree_levels.attributes.name')]),
            'description.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.description')]),
            'description.max' => __('validation.max.string', ['attribute' => __('required_degree_levels.attributes.description'), 'max' => 1000]),
            'is_default.boolean' => __('validation.boolean', ['attribute' => __('required_degree_levels.attributes.is_default')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('required_degree_levels.attributes.is_active')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('required_degree_levels.attributes.name'),
            'description' => __('required_degree_levels.attributes.description'),
            'is_default' => __('required_degree_levels.attributes.is_default'),
            'is_active' => __('required_degree_levels.attributes.is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->input('name'))
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->input('description'))
            ]);
        }

        // Convert string booleans to actual booleans
        if ($this->has('is_default')) {
            $this->merge([
                'is_default' => filter_var($this->input('is_default'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation logic can be added here
            
            // Ensure only one default required degree level exists
            if ($this->input('is_default') === true) {
                $requiredDegreeLevel = $this->route('required_degree_level') ?? $this->route('requiredDegreeLevel');
                $existingDefault = \App\Models\RequiredDegreeLevel::where('is_default', true)
                    ->where('id', '!=', $requiredDegreeLevel?->id)
                    ->exists();
                
                if ($existingDefault) {
                    $validator->errors()->add('is_default', __('required_degree_levels.validation.only_one_default'));
                }
            }
        });
    }
} 