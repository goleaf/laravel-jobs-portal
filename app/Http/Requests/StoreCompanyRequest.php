<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
            'industry_id' => 'nullable|exists:industries,id',
            'company_size_id' => 'nullable|exists:company_sizes,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'established_year' => 'nullable|digits:4|min:1800|max:2024',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'This :attribute has already been taken.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'exists' => 'The selected :attribute is invalid.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'numeric' => 'The :attribute must be a number.',
            'date' => 'The :attribute is not a valid date.',
            'after' => 'The :attribute must be a date after :date.',
            'url' => 'The :attribute format is invalid.',
            'boolean' => 'The :attribute field must be true or false.',
            'array' => 'The :attribute must be an array.',
            'accepted' => 'The :attribute must be accepted.',
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
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'website' => 'Website',
            'description' => 'Description',
            'address' => 'Address',
            'city_id' => 'City Id',
            'state_id' => 'State Id',
            'country_id' => 'Country Id',
            'industry_id' => 'Industry Id',
            'company_size_id' => 'Company Size Id',
            'logo' => 'Logo',
            'established_year' => 'Established Year',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add any data preparation logic here
        // Example: Convert empty strings to null
        $this->merge([
            // Add any automatic data transformations
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Add any custom validation logic here
        });
    }
}
