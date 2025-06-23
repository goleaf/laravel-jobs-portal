<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public endpoint - anyone can view company profiles
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'include' => 'sometimes|string|max:500',
            'fields' => 'sometimes|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'include.string' => __('validation.string', ['attribute' => __('validation.attributes.include')]),
            'include.max' => __('validation.max.string', ['attribute' => __('validation.attributes.include'), 'max' => 500]),
            'fields.string' => __('validation.string', ['attribute' => __('validation.attributes.fields')]),
            'fields.max' => __('validation.max.string', ['attribute' => __('validation.attributes.fields'), 'max' => 500]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'include' => __('validation.attributes.include'),
            'fields' => __('validation.attributes.fields'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate include relationships if provided
            if ($this->has('include') && is_array($this->include)) {
                $allowedIncludes = ['user', 'jobs', 'employees', 'industry', 'size', 'location'];
                foreach ($this->include as $include) {
                    if (!in_array($include, $allowedIncludes)) {
                        $validator->errors()->add('include', __('validation.in', ['attribute' => __('validation.attributes.include')]));
                    }
                }
            }

            // Validate field selection if provided
            if ($this->has('fields') && is_array($this->fields)) {
                $allowedFields = ['id', 'name', 'description', 'website', 'logo', 'industry', 'size', 'location', 'founded_year', 'created_at', 'updated_at'];
                foreach ($this->fields as $field) {
                    if (!in_array($field, $allowedFields)) {
                        $validator->errors()->add('fields', __('validation.in', ['attribute' => __('validation.attributes.fields')]));
                    }
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Parse include relationships
        if ($this->has('include')) {
            $this->merge([
                'include' => array_filter(explode(',', $this->input('include'))),
            ]);
        }

        // Parse field selection
        if ($this->has('fields')) {
            $this->merge([
                'fields' => array_filter(explode(',', $this->input('fields'))),
            ]);
        }
    }
}
