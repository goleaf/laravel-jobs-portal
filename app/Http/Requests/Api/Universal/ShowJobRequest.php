<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public endpoint - anyone can view job listings
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
            'track_view' => 'sometimes|boolean',
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
            'track_view.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.track_view')]),
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
            'track_view' => __('validation.attributes.track_view'),
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
                $allowedIncludes = [
                    'company', 'category', 'type', 'shift', 'level', 'skills',
                    'applications', 'location', 'salary_currency', 'benefits',
                ];
                foreach ($this->include as $include) {
                    if (!in_array($include, $allowedIncludes)) {
                        $validator->errors()->add('include', __('validation.in', ['attribute' => __('validation.attributes.include')]));
                    }
                }
            }

            // Validate field selection if provided
            if ($this->has('fields') && is_array($this->fields)) {
                $allowedFields = [
                    'id', 'title', 'description', 'requirements', 'benefits',
                    'salary_min', 'salary_max', 'currency', 'location', 'type',
                    'level', 'experience_required', 'education_required',
                    'skills_required', 'status', 'featured', 'remote_ok',
                    'posted_at', 'expires_at', 'created_at', 'updated_at',
                ];
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

        // Convert track_view to boolean
        if ($this->has('track_view')) {
            $this->merge([
                'track_view' => filter_var($this->track_view, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
