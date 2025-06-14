<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type_id' => 'required|exists:job_types,id',
            'job_category_id' => 'required|exists:job_categories,id',
            'company_id' => 'required|exists:companies,id',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|gt:salary_min',
            'salary_currency_id' => 'nullable|exists:salary_currencies,id',
            'experience_required' => 'nullable|string',
            'skills_required' => 'nullable|array',
            'application_deadline' => 'nullable|date|after:today',
            'is_featured' => 'boolean',
            'is_remote' => 'boolean',
            'status' => 'required|in:draft,published,closed',
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
            'title' => 'Title',
            'description' => 'Description',
            'job_type_id' => 'Job Type Id',
            'job_category_id' => 'Job Category Id',
            'company_id' => 'Company Id',
            'location' => 'Location',
            'salary_min' => 'Salary Min',
            'salary_max' => 'Salary Max',
            'salary_currency_id' => 'Salary Currency Id',
            'experience_required' => 'Experience Required',
            'skills_required' => 'Skills Required',
            'application_deadline' => 'Application Deadline',
            'is_featured' => 'Is Featured',
            'is_remote' => 'Is Remote',
            'status' => 'Status',
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
